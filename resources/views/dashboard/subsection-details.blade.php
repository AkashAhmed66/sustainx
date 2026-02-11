<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" 
               class="text-neutral-600 hover:text-primary-600 transition-colors p-2 hover:bg-primary-50 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-neutral-900">
                    {{ $subsection->name }}
                </h2>
                <p class="text-sm text-neutral-600 font-semibold">{{ $subsection->section->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6">
        <!-- Filters Card -->
        <div class="dashboard-card mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('dashboard.subsection', $subsection) }}" class="flex flex-col items-start gap-3 sm:flex-row sm:items-end">
                    <div class="w-full sm:flex-1">
                        <label class="block text-xs font-medium text-neutral-600 mb-1">Filter by Year</label>
                        <select name="year" 
                                onchange="this.form.submit()"
                                class="w-full px-4 py-2 bg-white border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm font-medium text-neutral-700">
                            <option value="">All Years</option>
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-full sm:flex-1">
                        <label class="block text-xs font-medium text-neutral-600 mb-1">Filter by Factory</label>
                        <select name="factory_id" 
                                onchange="this.form.submit()"
                                class="w-full px-4 py-2 bg-white border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm font-medium text-neutral-700">
                            <option value="">All Factories</option>
                            @foreach($factories as $factory)
                                <option value="{{ $factory->id }}" {{ $selectedFactoryId == $factory->id ? 'selected' : '' }}>
                                    {{ $factory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if($selectedYear || $selectedFactoryId)
                        <div class="w-full sm:w-auto sm:flex-shrink-0">
                            <a href="{{ route('dashboard.subsection', $subsection) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 text-neutral-600 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors text-sm font-medium w-full sm:w-auto h-[42px]">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
        @forelse($visualizationData as $index => $itemData)
            <div class="dashboard-card mb-6">
                <div class="p-6">
                    <!-- Item Header -->
                    <div class="flex items-start justify-between mb-6 pb-4 border-b-2 border-neutral-200">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-neutral-900 mb-2">
                                {{ $itemData['item']->name }}
                            </h3>
                            @if($itemData['item']->unit)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-100 text-primary-800">
                                    Unit: {{ $itemData['item']->unit }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(count($itemData['numeric_questions']) > 0)
                        <!-- Numeric Questions Visualization -->
                        <div class="mb-8">
                            <h4 class="text-lg font-bold text-neutral-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Numeric Questions Distribution
                            </h4>
                            
                            @php
                                $totalNumeric = collect($itemData['numeric_questions'])->sum('total');
                            @endphp
                            
                            <!-- Summary Card -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-2 border-blue-200 shadow-md mb-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-blue-700 mb-1 uppercase">Total Value</p>
                                        <p class="text-3xl font-extrabold text-blue-900">
                                            {{ number_format($totalNumeric, 2) }}
                                            @if($itemData['item']->unit)
                                                <span class="text-base font-bold text-blue-700">{{ $itemData['item']->unit }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Pie Chart -->
                                <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200 shadow-sm">
                                    <h5 class="text-base font-bold text-neutral-800 mb-4">Value Distribution by Question</h5>
                                    <div style="position: relative; height: 300px;">
                                        <canvas id="numericPieChart{{ $index }}"></canvas>
                                    </div>
                                </div>

                                <!-- Data Table -->
                                <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200 shadow-sm">
                                    <h5 class="text-base font-bold text-neutral-800 mb-4">Value Breakdown</h5>
                                    <div class="space-y-2 max-h-[300px] overflow-y-auto">
                                        @foreach($itemData['numeric_questions'] as $numQ)
                                            @php
                                                $percentage = $totalNumeric > 0 ? ($numQ['total'] / $totalNumeric * 100) : 0;
                                            @endphp
                                            <div class="p-3 bg-white rounded-lg border-2 border-neutral-200 shadow-sm">
                                                <div class="flex items-start justify-between mb-2">
                                                    <p class="text-sm font-semibold text-neutral-900 flex-1 pr-2">{{ $numQ['question']->question_text }}</p>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-neutral-600">
                                                        {{ number_format($numQ['total'], 2) }} {{ $itemData['item']->unit }}
                                                    </span>
                                                    <span class="px-2 py-1 bg-primary-100 text-primary-800 rounded-full text-xs font-bold">
                                                        {{ number_format($percentage, 1) }}%
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(count($itemData['mcq_questions']) > 0)
                        <!-- MCQ Questions Visualization -->
                        <div class="mb-8">
                            <h4 class="text-lg font-bold text-neutral-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Multiple Choice Questions
                            </h4>
                            
                            @foreach($itemData['mcq_questions'] as $mcqIndex => $mcqData)
                                <div class="mb-6 bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200">
                                    <h5 class="text-base font-semibold text-neutral-800 mb-4">{{ $mcqData['question']->question_text }}</h5>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <!-- Pie Chart -->
                                        <div style="position: relative; height: 250px;">
                                            <canvas id="mcqChart{{ $index }}_{{ $mcqIndex }}"></canvas>
                                        </div>
                                        
                                        <!-- Summary -->
                                        <div class="space-y-2">
                                            @foreach($mcqData['chart_data'] as $optionData)
                                                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-neutral-200">
                                                    <span class="text-sm font-semibold text-neutral-800">{{ $optionData['option'] }}</span>
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">
                                                        {{ $optionData['count'] }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(count($itemData['multiple_select_questions']) > 0)
                        <!-- Multiple Select Questions Visualization -->
                        <div class="mb-8">
                            <h4 class="text-lg font-bold text-neutral-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Multiple Select Questions
                            </h4>
                            
                            @foreach($itemData['multiple_select_questions'] as $msIndex => $msData)
                                <div class="mb-6 bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200">
                                    <h5 class="text-base font-semibold text-neutral-800 mb-4">{{ $msData['question']->question_text }}</h5>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <!-- Bar Chart -->
                                        <div style="position: relative; height: 250px;">
                                            <canvas id="msChart{{ $index }}_{{ $msIndex }}"></canvas>
                                        </div>
                                        
                                        <!-- Summary -->
                                        <div class="space-y-2">
                                            @foreach($msData['chart_data'] as $optionData)
                                                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-neutral-200">
                                                    <span class="text-sm font-semibold text-neutral-800">{{ $optionData['option'] }}</span>
                                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">
                                                        {{ $optionData['count'] }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="dashboard-card text-center py-12">
                <svg class="w-20 h-20 mx-auto text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="text-xl font-bold text-neutral-700 mb-2">No Data Available</h3>
                <p class="text-neutral-600 font-medium">No assessment data found for this subsection with the selected filters.</p>
            </div>
        @endforelse
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Chart.js loaded:', typeof Chart !== 'undefined');
            
            // Color palette
            const colors = {
                primary: 'rgb(58, 155, 111)',
                blue: 'rgb(59, 130, 246)',
                green: 'rgb(34, 197, 94)',
                purple: 'rgb(168, 85, 247)',
                orange: 'rgb(251, 146, 60)',
                red: 'rgb(239, 68, 68)',
                yellow: 'rgb(234, 179, 8)',
                pink: 'rgb(236, 72, 153)',
                teal: 'rgb(20, 184, 166)',
                indigo: 'rgb(99, 102, 241)',
            };

            const colorArray = [
                colors.primary, colors.blue, colors.green, colors.purple,
                colors.orange, colors.red, colors.yellow, colors.pink,
                colors.teal, colors.indigo
            ];

            @foreach($visualizationData as $index => $itemData)
                @if(count($itemData['numeric_questions']) > 0)
                    // Numeric Questions Pie Chart
                    @php
                        $labels = collect($itemData['numeric_questions'])->pluck('question.question_text')->toArray();
                        $values = collect($itemData['numeric_questions'])->pluck('total')->toArray();
                    @endphp
                    
                    console.log('Creating numeric pie chart {{ $index }}');
                    const numericPieElement{{ $index }} = document.getElementById('numericPieChart{{ $index }}');
                    if (numericPieElement{{ $index }}) {
                        new Chart(numericPieElement{{ $index }}, {
                            type: 'doughnut',
                            data: {
                                labels: @json($labels),
                                datasets: [{
                                    data: @json($values),
                                    backgroundColor: colorArray,
                                    borderWidth: 2,
                                    borderColor: '#fff',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            padding: 10,
                                            font: { size: 11 }
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0,0,0,0.8)',
                                        padding: 12,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.parsed || 0;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = ((value / total) * 100).toFixed(1);
                                                return label.substring(0, 30) + '...: ' + value.toFixed(2) + ' (' + percentage + '%)';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Numeric pie chart {{ $index }} created');
                    }
                @endif

                @foreach($itemData['mcq_questions'] as $mcqIndex => $mcqData)
                    // MCQ Pie Chart
                    @php
                        $mcqLabels = $mcqData['chart_data']->pluck('option')->toArray();
                        $mcqCounts = $mcqData['chart_data']->pluck('count')->toArray();
                    @endphp
                    
                    console.log('Creating MCQ chart {{ $index }}_{{ $mcqIndex }}');
                    const mcqElement{{ $index }}_{{ $mcqIndex }} = document.getElementById('mcqChart{{ $index }}_{{ $mcqIndex }}');
                    if (mcqElement{{ $index }}_{{ $mcqIndex }}) {
                        new Chart(mcqElement{{ $index }}_{{ $mcqIndex }}, {
                            type: 'doughnut',
                            data: {
                                labels: @json($mcqLabels),
                                datasets: [{
                                    data: @json($mcqCounts),
                                    backgroundColor: colorArray,
                                    borderWidth: 2,
                                    borderColor: '#fff',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            padding: 8,
                                            font: { size: 10 }
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0,0,0,0.8)',
                                        padding: 12,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.parsed || 0;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = ((value / total) * 100).toFixed(1);
                                                return label + ': ' + value + ' (' + percentage + '%)';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                @endforeach

                @foreach($itemData['multiple_select_questions'] as $msIndex => $msData)
                    // Multiple Select Bar Chart
                    @php
                        $msLabels = $msData['chart_data']->pluck('option')->toArray();
                        $msCounts = $msData['chart_data']->pluck('count')->toArray();
                    @endphp
                    
                    console.log('Creating MS chart {{ $index }}_{{ $msIndex }}');
                    const msElement{{ $index }}_{{ $msIndex }} = document.getElementById('msChart{{ $index }}_{{ $msIndex }}');
                    if (msElement{{ $index }}_{{ $msIndex }}) {
                        new Chart(msElement{{ $index }}_{{ $msIndex }}, {
                            type: 'bar',
                            data: {
                                labels: @json($msLabels),
                                datasets: [{
                                    label: 'Selections',
                                    data: @json($msCounts),
                                    backgroundColor: colorArray,
                                    borderRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0,0,0,0.8)',
                                        padding: 12,
                                        cornerRadius: 8,
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: 'rgba(0,0,0,0.05)' },
                                        ticks: {
                                            stepSize: 1,
                                            callback: function(value) {
                                                return Number.isInteger(value) ? value : '';
                                            }
                                        }
                                    },
                                    x: {
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                    }
                @endforeach
            @endforeach
        });
    </script>
    @endpush
</x-app-layout>
