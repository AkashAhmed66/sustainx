<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Edit Section') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('sections.update', $section) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">
                                Section Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $section->name) }}"
                                   required
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="Enter section name">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-neutral-700 mb-2">
                                Description
                            </label>
                            <textarea
                                   name="description"
                                   id="description"
                                   rows="4"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                   placeholder="Enter section description">{{ old('description', $section->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order Number -->
                        <div>
                            <label for="order_no" class="block text-sm font-medium text-neutral-700 mb-2">
                                Order Number <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="order_no"
                                   id="order_no"
                                   value="{{ old('order_no', $section->order_no) }}"
                                   required
                                   min="0"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('order_no') border-red-500 @enderror"
                                   placeholder="0">
                            @error('order_no')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $section->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-neutral-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t">
                        <a href="{{ route('sections.index') }}"
                           class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium">
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn-primary">
                            Update Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
