<div class="dashboard-card overflow-hidden">
    <!-- Table Header -->
    @if(isset($title) || isset($createRoute))
    <div class="flex items-center justify-between p-6 border-b border-neutral-200">
        @if(isset($title))
        <h3 class="card-title mb-0">{{ $title }}</h3>
        @endif
        
        @if(isset($createRoute))
        <a href="{{ $createRoute }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            {{ $createText ?? 'Create New' }}
        </a>
        @endif
    </div>
    @endif

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="m-6 mb-0 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="m-6 mb-0 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-neutral-50 border-b border-neutral-200">
                <tr>
                    {{ $header }}
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($paginator) && $paginator->hasPages())
    <div class="p-6 border-t border-neutral-200">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Results Info -->
            <div class="text-sm text-neutral-600">
                Showing 
                <span class="font-medium text-neutral-900">{{ $paginator->firstItem() }}</span>
                to 
                <span class="font-medium text-neutral-900">{{ $paginator->lastItem() }}</span>
                of 
                <span class="font-medium text-neutral-900">{{ $paginator->total() }}</span>
                results
            </div>

            <!-- Pagination Links -->
            <div class="flex items-center gap-2">
                {{-- Previous Button --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 text-sm font-medium text-neutral-400 bg-neutral-100 border border-neutral-200 rounded-lg cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                        Previous
                    </a>
                @endif

                {{-- Page Numbers --}}
                <div class="hidden sm:flex gap-1">
                    @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-4 py-2 text-sm font-semibold text-white bg-primary-500 border border-primary-500 rounded-lg">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Button --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                        Next
                    </a>
                @else
                    <span class="px-3 py-2 text-sm font-medium text-neutral-400 bg-neutral-100 border border-neutral-200 rounded-lg cursor-not-allowed">
                        Next
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
