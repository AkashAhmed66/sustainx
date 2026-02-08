<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 space-y-8">
        @forelse($sections as $section)
            <!-- Section Container -->
            <div class="space-y-4">
                <!-- Section Header -->
                <div class="dashboard-card bg-primary-600">
                    <h2 class="text-white text-2xl font-bold">{{ $section->name }}</h2>
                </div>

                <!-- Subsections Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($section->subsections as $subsection)
                        <div class="dashboard-card hover:shadow-md transition-shadow duration-200 cursor-pointer">
                            <div class="flex flex-col items-center justify-center text-center space-y-3 py-4">
                                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-neutral-800 font-semibold text-base leading-tight px-2">
                                    {{ $subsection->name }}
                                </h3>
                            </div>
                        </div>
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
                <h3 class="text-lg font-semibold text-neutral-700 mb-2">No Sections Available</h3>
                <p class="text-neutral-500 mb-6">Start by creating sections for your dashboard.</p>
                <a href="{{ route('sections.create') }}" class="btn-primary inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Section
                </a>
            </div>
        @endforelse
    </div>
</x-app-layout>
