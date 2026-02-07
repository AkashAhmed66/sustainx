<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Create Role') }}
        </h2>
    </x-slot>

    <div class="p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf

                    <!-- Role Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">
                            Role Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Enter role name (e.g., Manager, Editor)">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-neutral-700 mb-4">
                            Permissions
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($permissions as $permission)
                            <label class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 cursor-pointer transition-colors">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}"
                                       {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500 mr-3">
                                <span class="text-sm text-neutral-700">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('permissions')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-neutral-200">
                        <a href="{{ route('roles.index') }}" 
                           class="px-6 py-3 rounded-xl font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="btn-primary">
                            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
