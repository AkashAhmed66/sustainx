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
        @forelse($visualizationData as $index => $data)
            <div class="dashboard-card mb-6">
                <div class="p-6">
                    <!-- Question Header -->
                    <div class="flex items-start justify-between mb-6 pb-4 border-b-2 border-neutral-200">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-neutral-900 mb-2">
                                {{ $data['question']->question_text }}
                            </h3>
                            <div class="flex items-center gap-3 text-sm text-neutral-600">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-100 text-primary-800">
                                    {{ $data['item']->name }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-neutral-100 text-neutral-800">
                                    {{ $data['question']->questionType->name }}
                                </span>
                                @if($data['question']->unit)
                                    <span class="text-neutral-700 font-semibold">Unit: {{ $data['question']->unit }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($data['type'] == 1)
                        <!-- Numeric Question Visualization -->
                        @if(isset($data['chart_data']) && $data['chart_data']->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                            <!-- Summary Cards -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-2 border-blue-200 shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-blue-700 mb-1 uppercase">Total Value</p>
                                        <p class="text-3xl font-extrabold text-blue-900">
                                            {{ number_format($data['total'] ?? 0, 2) }}
                                            @if($data['question']->unit)
                                                <span class="text-base font-bold text-blue-700">{{ $data['question']->unit }}</span>
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

                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border-2 border-green-200 shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-green-700 mb-1 uppercase">Average</p>
                                        <p class="text-3xl font-extrabold text-green-900">
                                            {{ number_format($data['average'] ?? 0, 2) }}
                                            @if($data['question']->unit)
                                                <span class="text-base font-bold text-green-700">{{ $data['question']->unit }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border-2 border-purple-200 shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-purple-700 mb-1 uppercase">Data Points</p>
                                        <p class="text-3xl font-extrabold text-purple-900">
                                            {{ $data['chart_data']->count() }}
                                            <span class="text-base font-bold text-purple-700">{{ $data['chart_data']->count() == 1 ? 'factory' : 'factories' }}</span>
                                        </p>
                                    </div>
                                    <div class="w-14 h-14 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Bar Chart -->
                            <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200 shadow-sm">
                                <h4 class="text-base font-bold text-neutral-800 mb-4">Calculated Values by Factory</h4>
                                <div style="position: relative; height: 300px;">
                                    <canvas id="barChart{{ $index }}"></canvas>
                                </div>
                            </div>

                            <!-- Comparison Chart -->
                            <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200 shadow-sm">
                                <h4 class="text-base font-bold text-neutral-800 mb-4">Actual vs Calculated Values</h4>
                                <div style="position: relative; height: 300px;">
                                    <canvas id="comparisonChart{{ $index }}"></canvas>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="bg-neutral-50 rounded-xl p-10 text-center border-2 border-neutral-200">
                            <svg class="w-16 h-16 mx-auto text-neutral-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="text-neutral-700 font-bold text-base">No data available for this question</p>
                        </div>
                        @endif

                    @elseif($data['type'] == 2)
                        <!-- MCQ Question Visualization -->
                        @if(isset($data['chart_data']) && $data['chart_data']->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Pie Chart -->
                            <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200 shadow-sm">
                                <h4 class="text-base font-bold text-neutral-800 mb-4">Response Distribution</h4>
                                <div style="position: relative; height: 300px;">
                                    <canvas id="pieChart{{ $index }}"></canvas>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200 shadow-sm">
                                <h4 class="text-base font-bold text-neutral-800 mb-4">Response Summary</h4>
                                <div class="space-y-2">
                                    @foreach($data['chart_data'] as $optionData)
                                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border-2 border-neutral-200 shadow-sm">
                                            <span class="text-sm font-bold text-neutral-900">{{ $optionData['option'] }}</span>
                                            <span class="px-3 py-1.5 bg-primary-100 text-primary-800 rounded-full text-sm font-extrabold">
                                                {{ $optionData['count'] }} {{ $optionData['count'] == 1 ? 'response' : 'responses' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="bg-neutral-50 rounded-xl p-10 text-center border-2 border-neutral-200">
                            <svg class="w-16 h-16 mx-auto text-neutral-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="text-neutral-700 font-bold text-base">No data available for this question</p>
                        </div>
                        @endif
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
            };

            @foreach($visualizationData as $index => $data)
                @if($data['type'] == 1 && isset($data['chart_data']))
                    // Numeric Question Charts
                    @php
                        $factories = $data['chart_data']->pluck('factory')->toArray();
                        $actualAnswers = $data['chart_data']->pluck('actual_answer')->toArray();
                        $calculatedAnswers = $data['chart_data']->pluck('calculated_answer')->toArray();
                    @endphp
                    
                    console.log('Creating bar chart {{ $index }}');
                    const barChartElement{{ $index }} = document.getElementById('barChart{{ $index }}');
                    if (barChartElement{{ $index }}) {
                        const barChart{{ $index }} = new Chart(barChartElement{{ $index }}, {
                            type: 'bar',
                            data: {
                                labels: @json($factories),
                                datasets: [{
                                    label: 'Calculated Value ({{ $data['question']->unit ?? '' }})',
                                    data: @json($calculatedAnswers),
                                    backgroundColor: colors.primary,
                                    borderColor: colors.primary,
                                    borderWidth: 1,
                                    borderRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { 
                                        display: true,
                                        position: 'top',
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0,0,0,0.8)',
                                        padding: 12,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: 'rgba(0,0,0,0.05)' },
                                        ticks: {
                                            callback: function(value) {
                                                return value.toFixed(2);
                                            }
                                        }
                                    },
                                    x: {
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                        console.log('Bar chart {{ $index }} created successfully');
                    } else {
                        console.error('Bar chart element {{ $index }} not found');
                    }

                    console.log('Creating comparison chart {{ $index }}');
                    const comparisonChartElement{{ $index }} = document.getElementById('comparisonChart{{ $index }}');
                    if (comparisonChartElement{{ $index }}) {
                        const comparisonChart{{ $index }} = new Chart(comparisonChartElement{{ $index }}, {
                            type: 'bar',
                            data: {
                                labels: @json($factories),
                                datasets: [
                                    {
                                        label: 'Actual Answer',
                                        data: @json($actualAnswers),
                                        backgroundColor: colors.blue,
                                        borderRadius: 6,
                                    },
                                    {
                                        label: 'Calculated Answer',
                                        data: @json($calculatedAnswers),
                                        backgroundColor: colors.green,
                                        borderRadius: 6,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0,0,0,0.8)',
                                        padding: 12,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: 'rgba(0,0,0,0.05)' },
                                        ticks: {
                                            callback: function(value) {
                                                return value.toFixed(2);
                                            }
                                        }
                                    },
                                    x: {
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                        console.log('Comparison chart {{ $index }} created successfully');
                    } else {
                        console.error('Comparison chart element {{ $index }} not found');
                    }

                @elseif($data['type'] == 2 && isset($data['chart_data']))
                    // MCQ Question Chart
                    @php
                        $options = $data['chart_data']->pluck('option')->toArray();
                        $counts = $data['chart_data']->pluck('count')->toArray();
                    @endphp
                    
                    console.log('Creating pie chart {{ $index }}');
                    const pieChartElement{{ $index }} = document.getElementById('pieChart{{ $index }}');
                    if (pieChartElement{{ $index }}) {
                        const pieChart{{ $index }} = new Chart(pieChartElement{{ $index }}, {
                            type: 'doughnut',
                            data: {
                                labels: @json($options),
                                datasets: [{
                                    data: @json($counts),
                                    backgroundColor: [
                                        colors.primary,
                                        colors.blue,
                                        colors.green,
                                        colors.purple,
                                        colors.orange,
                                        colors.red,
                                        colors.yellow,
                                        colors.pink,
                                    ],
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
                        console.log('Pie chart {{ $index }} created successfully');
                    } else {
                        console.error('Pie chart element {{ $index }} not found');
                    }
                @endif
            @endforeach
        });
    </script>
    @endpush
</x-app-layout>
