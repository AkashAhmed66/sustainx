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
                <h2 class="font-bold text-2xl text-neutral-900">{{ $subsection->name }}</h2>
                <p class="text-sm text-neutral-600 font-semibold">{{ $subsection->section->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6">
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
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
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
                                <option value="{{ $factory->id }}" {{ $selectedFactoryId == $factory->id ? 'selected' : '' }}>{{ $factory->name }}</option>
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

        @php
            $numericQuestions = $visualizationData['numeric_questions'] ?? [];
            $mcqQuestions = $visualizationData['mcq_questions'] ?? [];
            $multipleSelectQuestions = $visualizationData['multiple_select_questions'] ?? [];
            $totalNumeric = collect($numericQuestions)->sum('total');
        @endphp

        @if(count($numericQuestions) === 0 && count($mcqQuestions) === 0 && count($multipleSelectQuestions) === 0)
            <div class="dashboard-card text-center py-12">
                <svg class="w-20 h-20 mx-auto text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="text-xl font-bold text-neutral-700 mb-2">No Data Available</h3>
                <p class="text-neutral-600 font-medium">No assessment data found for this subsection with the selected filters.</p>
            </div>
        @else
            @if(count($numericQuestions) > 0)
                <div class="dashboard-card mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-neutral-800 mb-4">Numeric Questions (Question-Level)</h3>

                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-2 border-blue-200 shadow-md mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold text-blue-700 mb-1 uppercase">Total Value</p>
                                    <p class="text-3xl font-extrabold text-blue-900">{{ number_format($totalNumeric, 2) }}</p>
                                </div>
                                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200 shadow-sm">
                                <h4 class="text-base font-bold text-neutral-800 mb-4">Distribution by Question</h4>
                                <div style="position: relative; height: 320px;">
                                    <canvas id="numericQuestionChart"></canvas>
                                </div>
                            </div>
                            <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200 shadow-sm">
                                <h4 class="text-base font-bold text-neutral-800 mb-4">Question Breakdown</h4>
                                <div class="space-y-2 max-h-[320px] overflow-y-auto">
                                    @foreach($numericQuestions as $numQ)
                                        @php
                                            $percentage = $totalNumeric > 0 ? (($numQ['total'] / $totalNumeric) * 100) : 0;
                                        @endphp
                                        <div class="p-3 bg-white rounded-lg border-2 border-neutral-200 shadow-sm">
                                            <p class="text-sm font-semibold text-neutral-900 mb-1">{{ $numQ['question']->question_text }}</p>
                                            <div class="flex items-center justify-between text-xs text-neutral-600">
                                                <span>Total: {{ number_format($numQ['total'], 2) }}</span>
                                                <span>Avg: {{ number_format($numQ['average'], 2) }}</span>
                                                <span>Count: {{ $numQ['count'] }}</span>
                                                <span class="px-2 py-1 bg-primary-100 text-primary-800 rounded-full font-bold">{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($mcqQuestions) > 0)
                <div class="dashboard-card mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-neutral-800 mb-4">Multiple Choice Questions (Question-Level)</h3>
                        <div class="space-y-6">
                            @foreach($mcqQuestions as $index => $mcq)
                                <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200">
                                    <h4 class="text-base font-semibold text-neutral-800 mb-4">{{ $mcq['question']->question_text }}</h4>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div style="position: relative; height: 280px;">
                                            <canvas id="mcqChart{{ $index }}"></canvas>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($mcq['chart_data'] as $option)
                                                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-neutral-200">
                                                    <span class="text-sm font-semibold text-neutral-800">{{ $option['option'] }}</span>
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">{{ $option['count'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if(count($multipleSelectQuestions) > 0)
                <div class="dashboard-card mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-neutral-800 mb-4">Multiple Select Questions (Question-Level)</h3>
                        <div class="space-y-6">
                            @foreach($multipleSelectQuestions as $index => $ms)
                                <div class="bg-neutral-50 rounded-xl p-5 border-2 border-neutral-200">
                                    <h4 class="text-base font-semibold text-neutral-800 mb-4">{{ $ms['question']->question_text }}</h4>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div style="position: relative; height: 280px;">
                                            <canvas id="msChart{{ $index }}"></canvas>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($ms['chart_data'] as $option)
                                                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-neutral-200">
                                                    <span class="text-sm font-semibold text-neutral-800">{{ $option['option'] }}</span>
                                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">{{ $option['count'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const palette = [
                'rgb(58, 155, 111)',
                'rgb(59, 130, 246)',
                'rgb(34, 197, 94)',
                'rgb(168, 85, 247)',
                'rgb(251, 146, 60)',
                'rgb(239, 68, 68)',
                'rgb(234, 179, 8)',
                'rgb(20, 184, 166)',
                'rgb(99, 102, 241)',
                'rgb(236, 72, 153)',
            ];

            @if(count($numericQuestions) > 0)
                const numericCtx = document.getElementById('numericQuestionChart');
                if (numericCtx) {
                    const labels = @json(collect($numericQuestions)->pluck('question.question_text')->map(fn ($text) => \Illuminate\Support\Str::limit($text, 60))->toArray());
                    const values = @json(collect($numericQuestions)->pluck('total')->toArray());

                    new Chart(numericCtx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: palette,
                                borderWidth: 2,
                                borderColor: '#fff',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 12,
                                    }
                                }
                            }
                        }
                    });
                }
            @endif

            @foreach($mcqQuestions as $index => $mcq)
                (function() {
                    const ctx = document.getElementById('mcqChart{{ $index }}');
                    if (!ctx) return;

                    const labels = @json(collect($mcq['chart_data'])->pluck('option')->toArray());
                    const values = @json(collect($mcq['chart_data'])->pluck('count')->toArray());

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: palette,
                                borderWidth: 2,
                                borderColor: '#fff',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                }
                            }
                        }
                    });
                })();
            @endforeach

            @foreach($multipleSelectQuestions as $index => $ms)
                (function() {
                    const ctx = document.getElementById('msChart{{ $index }}');
                    if (!ctx) return;

                    const labels = @json(collect($ms['chart_data'])->pluck('option')->toArray());
                    const values = @json(collect($ms['chart_data'])->pluck('count')->toArray());

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Selections',
                                data: values,
                                backgroundColor: 'rgba(168, 85, 247, 0.8)',
                                borderColor: 'rgb(168, 85, 247)',
                                borderWidth: 2,
                                borderRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0,
                                    }
                                }
                            }
                        }
                    });
                })();
            @endforeach
        });
    </script>
    @endpush
</x-app-layout>
