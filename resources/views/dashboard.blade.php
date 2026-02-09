<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('ESG Performance Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6">
        <!-- Filters Card -->
        <div class="dashboard-card mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row items-end gap-3">
                    <div class="flex-1">
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
                    
                    <div class="flex-1">
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
                        <div class="sm:flex-shrink-0">
                            <a href="{{ route('dashboard') }}" 
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

        <div class="space-y-8">
        @forelse($sections as $section)
            <!-- Section Container -->
            <div class="space-y-4">
                <!-- Section Header -->
                <div class="dashboard-card bg-gradient-to-r from-primary-600 to-primary-700 shadow-lg">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white text-2xl font-bold">{{ $section->name }}</h2>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                            {{ $section->subsections->count() }} Subsections
                        </span>
                    </div>
                </div>

                <!-- Subsections Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($section->subsections as $subsection)
                        <a href="{{ route('dashboard.subsection', $subsection) }}?year={{ $selectedYear }}&factory_id={{ $selectedFactoryId }}" 
                           class="dashboard-card hover:shadow-xl hover:scale-105 transition-all duration-200 cursor-pointer border-b-4 border-primary-500 group">
                            <div class="flex flex-col h-full">
                                <!-- Icon Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <svg class="w-5 h-5 text-neutral-400 group-hover:text-primary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                                
                                <!-- Subsection Name -->
                                <h3 class="text-neutral-800 font-semibold text-base leading-tight mb-3 flex-grow">
                                    {{ $subsection->name }}
                                </h3>
                                
                                <!-- Cumulative Data -->
                                <div class="pt-3 border-t border-neutral-200">
                                    <div class="flex items-baseline justify-between">
                                        <span class="text-xs text-neutral-500 font-medium">Total Value</span>
                                        <div class="text-right">
                                            <span class="text-xl font-bold text-primary-700">
                                                {{ number_format($subsection->cumulative_total, 2) }}
                                            </span>
                                            @if($subsection->unit)
                                                <span class="text-xs text-neutral-600 ml-1">{{ $subsection->unit }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full dashboard-card text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-neutral-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-neutral-500">No subsections available for {{ $section->name }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="dashboard-card text-center py-12">
                <svg class="w-16 h-16 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-neutral-700 mb-2">No Data Available</h3>
                <p class="text-neutral-500 mb-6">No assessment data found for the selected filters.</p>
                @can('create assessments')
                    <a href="{{ route('assessments.create') }}" class="btn-primary inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Assessment
                    </a>
                @endcan
            </div>
        @endforelse
        </div>
    </div>
</x-app-layout>
