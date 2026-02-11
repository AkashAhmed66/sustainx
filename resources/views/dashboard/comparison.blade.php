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
                    {{ __('Comparison Dashboard') }}
                </h2>
                <p class="text-sm text-neutral-600 font-medium">Year-over-Year Performance Analysis</p>
            </div>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6">
        <!-- Year Selection Card -->
        <div class="dashboard-card mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('dashboard.comparison') }}" id="comparisonForm">
                    <input type="hidden" name="subsection_id" value="{{ $selectedSubsection?->id ?? '' }}">
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-bold text-neutral-700 mb-2">Select Years to Compare</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                                @foreach($availableYears as $year)
                                    <label class="flex items-center p-3 bg-white border-2 rounded-lg cursor-pointer transition-all hover:border-primary-500 {{ in_array($year, $selectedYears) ? 'border-primary-600 bg-primary-50' : 'border-neutral-200' }}">
                                        <input type="checkbox" 
                                               name="years[]" 
                                               value="{{ $year }}"
                                               {{ in_array($year, $selectedYears) ? 'checked' : '' }}
                                               onchange="document.getElementById('comparisonForm').submit()"
                                               class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                        <span class="ml-2 text-sm font-semibold text-neutral-800">{{ $year }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Sidebar: Sections & Subsections -->
            <div class="lg:col-span-3">
                <div class="dashboard-card sticky top-6">
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-neutral-900 mb-4">Subsections</h3>
                        <div class="space-y-2 max-h-[calc(100vh-300px)] overflow-y-auto">
                            @foreach($sections as $section)
                                <div class="mb-4">
                                    <!-- Section Name -->
                                    <div class="px-3 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-lg mb-2">
                                        <h4 class="font-bold text-sm">{{ $section->name }}</h4>
                                    </div>
                                    
                                    <!-- Subsections -->
                                    @foreach($section->subsections as $subsection)
                                        <a href="{{ route('dashboard.comparison', ['subsection_id' => $subsection->id, 'years' => $selectedYears]) }}"
                                           class="block px-4 py-3 rounded-lg transition-all {{ $selectedSubsection?->id == $subsection->id ? 'bg-primary-100 border-2 border-primary-600 text-primary-900 font-bold' : 'bg-neutral-50 hover:bg-neutral-100 text-neutral-700 hover:text-neutral-900 border-2 border-transparent' }}">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                                </svg>
                                                <span class="text-sm">{{ $subsection->name }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content: Comparison Charts -->
            <div class="lg:col-span-9">
                @if($selectedSubsection && $comparisonData)
                    <div class="dashboard-card">
                        <div class="p-6">
                            <!-- Subsection Header -->
                            <div class="flex items-start justify-between mb-6 pb-4 border-b-2 border-neutral-200">
                                <div>
                                    <h3 class="text-2xl font-bold text-neutral-900 mb-2">
                                        {{ $selectedSubsection->name }}
                                    </h3>
                                    <p class="text-sm text-neutral-600 font-medium">
                                        {{ $selectedSubsection->section->name }}
                                    </p>
                                    @if($unit)
                                        <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-xs font-bold bg-primary-100 text-primary-800">
                                            Unit: {{ $unit }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if(count($comparisonData) > 0)
                                <!-- Summary Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    @php
                                        $totals = collect($comparisonData)->pluck('total');
                                        $latestYear = collect($comparisonData)->first();
                                        $previousYear = collect($comparisonData)->skip(1)->first();
                                        $percentageChange = 0;
                                        if ($previousYear && $previousYear['total'] > 0) {
                                            $percentageChange = (($latestYear['total'] - $previousYear['total']) / $previousYear['total']) * 100;
                                        }
                                    @endphp
                                    
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-2 border-blue-200 shadow-md">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-blue-700 mb-1 uppercase">Latest Year</p>
                                                <p class="text-3xl font-extrabold text-blue-900 break-words">
                                                    {{ number_format($latestYear['total'], 2) }}
                                                    @if($unit)
                                                        <span class="text-base font-bold text-blue-700">{{ $unit }}</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-blue-600 mt-1 font-semibold">{{ $latestYear['year'] }}</p>
                                            </div>
                                            <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border-2 border-green-200 shadow-md">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-green-700 mb-1 uppercase">Total Average</p>
                                                <p class="text-3xl font-extrabold text-green-900 break-words">
                                                    {{ number_format($totals->avg(), 2) }}
                                                    @if($unit)
                                                        <span class="text-base font-bold text-green-700">{{ $unit }}</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-green-600 mt-1 font-semibold">Across {{ count($comparisonData) }} {{ count($comparisonData) == 1 ? 'year' : 'years' }}</p>
                                            </div>
                                            <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    @if($percentageChange >= 0)
                                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5 border-2 border-orange-200 shadow-md">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <p class="text-sm font-bold text-orange-700 mb-1 uppercase">Year Change</p>
                                                    <p class="text-3xl font-extrabold text-orange-900">
                                                        +{{ number_format($percentageChange, 1) }}%
                                                    </p>
                                                    @if($previousYear)
                                                        <p class="text-xs text-orange-600 mt-1 font-semibold">vs {{ $previousYear['year'] }}</p>
                                                    @endif
                                                </div>
                                                <div class="w-14 h-14 bg-orange-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5 border-2 border-red-200 shadow-md">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <p class="text-sm font-bold text-red-700 mb-1 uppercase">Year Change</p>
                                                    <p class="text-3xl font-extrabold text-red-900">
                                                        {{ number_format($percentageChange, 1) }}%
                                                    </p>
                                                    @if($previousYear)
                                                        <p class="text-xs text-red-600 mt-1 font-semibold">vs {{ $previousYear['year'] }}</p>
                                                    @endif
                                                </div>
                                                <div class="w-14 h-14 bg-red-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Comparison Chart -->
                                <div class="bg-neutral-50 rounded-xl p-6 border-2 border-neutral-200 shadow-sm">
                                    <h4 class="text-lg font-bold text-neutral-800 mb-4">Year-over-Year Comparison</h4>
                                    <div style="position: relative; height: 400px;">
                                        <canvas id="comparisonChart"></canvas>
                                    </div>
                                </div>

                                <!-- Data Table -->
                                <div class="mt-6 bg-neutral-50 rounded-xl p-6 border-2 border-neutral-200 shadow-sm">
                                    <h4 class="text-lg font-bold text-neutral-800 mb-4">Detailed Data</h4>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-neutral-200">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-bold text-neutral-800">Year</th>
                                                    <th class="px-4 py-3 text-right font-bold text-neutral-800">Total Value</th>
                                                    <th class="px-4 py-3 text-right font-bold text-neutral-800">Data Points</th>
                                                    <th class="px-4 py-3 text-right font-bold text-neutral-800">Change</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-neutral-200">
                                                @foreach($comparisonData as $index => $data)
                                                    @php
                                                        $prevData = $index > 0 ? $comparisonData[$index - 1] : null;
                                                        $change = null;
                                                        if ($prevData && $prevData['total'] > 0) {
                                                            $change = (($data['total'] - $prevData['total']) / $prevData['total']) * 100;
                                                        }
                                                    @endphp
                                                    <tr class="hover:bg-neutral-50">
                                                        <td class="px-4 py-3 font-bold text-neutral-900">{{ $data['year'] }}</td>
                                                        <td class="px-4 py-3 text-right font-semibold text-neutral-800">
                                                            {{ number_format($data['total'], 2) }} {{ $unit }}
                                                        </td>
                                                        <td class="px-4 py-3 text-right text-neutral-600">{{ $data['count'] }}</td>
                                                        <td class="px-4 py-3 text-right font-bold {{ $change !== null ? ($change >= 0 ? 'text-green-600' : 'text-red-600') : 'text-neutral-400' }}">
                                                            @if($change !== null)
                                                                {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="bg-neutral-50 rounded-xl p-10 text-center border-2 border-neutral-200">
                                    <svg class="w-16 h-16 mx-auto text-neutral-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <p class="text-neutral-600 font-medium">No data available for the selected years</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="dashboard-card">
                        <div class="p-12 text-center">
                            <svg class="w-20 h-20 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <h3 class="text-xl font-bold text-neutral-700 mb-2">Select a Subsection</h3>
                            <p class="text-neutral-600 font-medium">Choose a subsection from the left sidebar to view comparison data</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($selectedSubsection && $comparisonData && count($comparisonData) > 0)
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('comparisonChart');
                if (ctx) {
                    @php
                        $years = collect($comparisonData)->pluck('year')->toArray();
                        $totals = collect($comparisonData)->pluck('total')->toArray();
                        $counts = collect($comparisonData)->pluck('count')->toArray();
                    @endphp
                    
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json($years),
                            datasets: [
                                {
                                    label: 'Total Value ({{ $unit }})',
                                    data: @json($totals),
                                    backgroundColor: 'rgba(58, 155, 111, 0.8)',
                                    borderColor: 'rgb(58, 155, 111)',
                                    borderWidth: 2,
                                    borderRadius: 8,
                                    yAxisID: 'y',
                                },
                                {
                                    label: 'Number of Data Points',
                                    data: @json($counts),
                                    type: 'line',
                                    backgroundColor: 'rgba(251, 146, 60, 0.1)',
                                    borderColor: 'rgb(251, 146, 60)',
                                    borderWidth: 3,
                                    pointBackgroundColor: 'rgb(251, 146, 60)',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 6,
                                    pointHoverRadius: 8,
                                    tension: 0.4,
                                    yAxisID: 'y1',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 13,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    padding: 12,
                                    cornerRadius: 8,
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += context.datasetIndex === 0 ? context.parsed.y.toFixed(2) : context.parsed.y;
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return value.toFixed(2);
                                        },
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Total Value ({{ $unit }})',
                                        font: {
                                            size: 13,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    beginAtZero: true,
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                    ticks: {
                                        stepSize: 1,
                                        callback: function(value) {
                                            return Number.isInteger(value) ? value : '';
                                        },
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Data Points',
                                        font: {
                                            size: 13,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 13,
                                            weight: 'bold'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
        @endpush
    @endif
</x-app-layout>
