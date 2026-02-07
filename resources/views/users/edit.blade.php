<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="John Doe">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                   placeholder="john@example.com">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">
                                New Password <span class="text-neutral-400">(Leave blank to keep current)</span>
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                   placeholder="••••••••">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">
                                Confirm New Password
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-neutral-700 mb-4">
                            Assign Roles
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @foreach($roles as $role)
                            <label class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 cursor-pointer transition-colors">
                                <input type="checkbox" 
                                       name="roles[]" 
                                       value="{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500 mr-3">
                                <div>
                                    <span class="text-sm font-medium text-neutral-700 block">{{ $role->name }}</span>
                                    <span class="text-xs text-neutral-500">{{ $role->permissions->count() }} permissions</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-neutral-200">
                        <a href="{{ route('users.index') }}" 
                           class="px-6 py-3 rounded-xl font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="btn-primary">
                            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
