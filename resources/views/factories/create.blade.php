<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Create Factory') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('factories.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">
                                Factory Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="Enter factory name">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Factory Type -->
                        <div>
                            <label for="factory_type_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                Factory Type <span class="text-red-500">*</span>
                            </label>
                            <select name="factory_type_id"
                                    id="factory_type_id"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('factory_type_id') border-red-500 @enderror">
                                <option value="">Select factory type</option>
                                @foreach($factoryTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('factory_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('factory_type_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <select name="country_id"
                                    id="country_id"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('country_id') border-red-500 @enderror">
                                <option value="">Select country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="flex items-center h-full">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-neutral-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-6">
                        <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">
                            Address
                        </label>
                        <textarea
                               name="address"
                               id="address"
                               rows="3"
                               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('address') border-red-500 @enderror"
                               placeholder="Enter factory address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Users -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-neutral-700 mb-2">
                            Assign Users
                        </label>
                        <div class="border border-neutral-300 rounded-xl p-4 max-h-60 overflow-y-auto @error('user_ids') border-red-500 @enderror">
                            @forelse($users as $user)
                                <label class="flex items-center py-2 hover:bg-neutral-50 px-2 rounded cursor-pointer">
                                    <input type="checkbox"
                                           name="user_ids[]"
                                           value="{{ $user->id }}"
                                           {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                    <span class="ml-3 text-sm text-neutral-700">{{ $user->name }} ({{ $user->email }})</span>
                                </label>
                            @empty
                                <p class="text-sm text-neutral-500 text-center py-2">No users available</p>
                            @endforelse
                        </div>
                        @error('user_ids')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-neutral-500">Select users who should have access to this factory</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t">
                        <a href="{{ route('factories.index') }}"
                           class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium">
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn-primary">
                            Create Factory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
