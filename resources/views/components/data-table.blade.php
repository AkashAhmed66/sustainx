@props(['title' => null, 'createRoute' => null, 'createText' => 'Create New', 'paginator' => null, 'columns' => [], 'bulkDeleteRoute' => null, 'searchPlaceholder' => 'Search...', 'bulkEnabled' => false])

<div class="dashboard-card overflow-hidden" 
     x-data="{
         selectedItems: [],
         selectAll: false,
         toggleAll() {
             if (this.selectAll) {
                 this.selectedItems = Array.from(document.querySelectorAll('.item-checkbox')).map(cb => cb.value);
             } else {
                 this.selectedItems = [];
             }
         },
         bulkDelete() {
             if (this.selectedItems.length === 0) {
                 alert('Please select items to delete');
                 return;
             }
             if (confirm(`Are you sure you want to delete ${this.selectedItems.length} item(s)?`)) {
                 let form = document.createElement('form');
                 form.method = 'POST';
                 form.action = '{{ $bulkDeleteRoute }}';
                 
                 let csrfInput = document.createElement('input');
                 csrfInput.type = 'hidden';
                 csrfInput.name = '_token';
                 csrfInput.value = '{{ csrf_token() }}';
                 form.appendChild(csrfInput);
                 
                 let methodInput = document.createElement('input');
                 methodInput.type = 'hidden';
                 methodInput.name = '_method';
                 methodInput.value = 'DELETE';
                 form.appendChild(methodInput);
                 
                 let idsInput = document.createElement('input');
                 idsInput.type = 'hidden';
                 idsInput.name = 'ids';
                 idsInput.value = JSON.stringify(this.selectedItems);
                 form.appendChild(idsInput);
                 
                 document.body.appendChild(form);
                 form.submit();
             }
         }
     }">
    <!-- Table Header -->
    @if(isset($title) || isset($createRoute))
    <div class="px-6 pt-6 pb-4 border-b border-neutral-200">
        <div class="flex items-center justify-between mb-4">
            @if(isset($title))
            <h3 class="card-title mb-0">{{ $title }}</h3>
            @endif
            
            <div class="flex items-center gap-3">
                @if($bulkEnabled && $bulkDeleteRoute)
                <button @click="bulkDelete()" 
                        x-show="selectedItems.length > 0"
                        x-cloak
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete <span x-text="selectedItems.length"></span>
                </button>
                @endif
                
                @if(isset($createRoute))
                <a href="{{ $createRoute }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    {{ $createText }}
                </a>
                @endif
            </div>
        </div>
        
        <!-- Search and Filters -->
        <form method="GET" class="flex items-center justify-between gap-3">
            <div class="relative" style="width: 320px;">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="{{ $searchPlaceholder }}" 
                       class="w-full pl-10 pr-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            <div class="flex items-center gap-3">
                @if(request('search') || request('per_page'))
                <a href="{{ url()->current() }}" 
                   class="px-4 py-2 text-neutral-600 hover:text-neutral-900 border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors text-sm font-medium">
                    Clear
                </a>
                @endif
                
                <select name="per_page" 
                        onchange="this.form.submit()" 
                        class="px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                </select>
            </div>
        </form>
    </div>
    @endif

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mx-6 mt-6 mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mx-6 mt-6 mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-neutral-50 border-b border-neutral-200">
                <tr>
                    @if($bulkEnabled && $bulkDeleteRoute)
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" 
                               x-model="selectAll" 
                               @change="toggleAll()"
                               class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                    </th>
                    @endif
                    
                    @if(!empty($columns))
                        @foreach($columns as $key => $label)
                            <th class="px-6 py-3 text-{{ $key === 'actions' ? 'right' : 'left' }} text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                @if($key !== 'actions')
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'direction' => request('sort') === $key && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="inline-flex items-center gap-1 hover:text-neutral-900 group">
                                    {{ $label }}
                                    <svg class="w-4 h-4 {{ request('sort') === $key ? 'text-primary-600' : 'text-neutral-400 opacity-0 group-hover:opacity-100' }}" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if(request('sort') === $key && request('direction') === 'desc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @endif
                                    </svg>
                                </a>
                                @else
                                    {{ $label }}
                                @endif
                            </th>
                        @endforeach
                    @else
                        {{ $header ?? '' }}
                    @endif
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
                    <a href="{{ $paginator->appends(request()->except('page'))->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
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
                            <a href="{{ $paginator->appends(request()->except('page'))->url($page) }}" class="px-4 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Button --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->appends(request()->except('page'))->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
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
