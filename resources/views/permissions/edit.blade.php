<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Edit Permission') }}
        </h2>
    </x-slot>

    <div class="p-6 max-w-2xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Permission Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">
                            Permission Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $permission->name) }}"
                               required
                               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Enter permission name">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-neutral-500">Use lowercase with spaces (e.g., "view users", "create roles")</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-neutral-200">
                        <a href="{{ route('permissions.index') }}" 
                           class="px-6 py-3 rounded-xl font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="btn-primary">
                            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
