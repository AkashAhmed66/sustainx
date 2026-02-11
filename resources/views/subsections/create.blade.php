<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Create Subsection') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('subsections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <!-- Section -->
                        <div>
                            <label for="section_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                Section <span class="text-red-500">*</span>
                            </label>
                            <select name="section_id"
                                    id="section_id"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('section_id') border-red-500 @enderror">
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">
                                Subsection Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="Enter subsection name">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-neutral-700 mb-2">
                                Description
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="4"
                                      class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="Enter subsection description">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label for="images" class="block text-sm font-medium text-neutral-700 mb-2">
                                Subsection Images
                                <span class="text-neutral-500 text-xs">(For dashboard display - Max 5MB per image)</span>
                            </label>
                            <input type="file"
                                   name="images[]"
                                   id="images"
                                   multiple
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/svg+xml,image/webp"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('images') border-red-500 @enderror"
                                   onchange="previewImages(event)">
                            <p class="mt-1 text-xs text-neutral-500">
                                Accepted formats: JPEG, JPG, PNG, GIF, SVG, WEBP. You can select multiple images.
                            </p>
                            @error('images')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('images.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Image Preview Container -->
                            <div id="imagePreviewContainer" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                        </div>

                        <!-- Order Number -->
                        <div>
                            <label for="order_no" class="block text-sm font-medium text-neutral-700 mb-2">
                                Order Number <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="order_no"
                                   id="order_no"
                                   value="{{ old('order_no', 1) }}"
                                   required
                                   min="1"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('order_no') border-red-500 @enderror"
                                   placeholder="1">
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
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-neutral-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t">
                        <a href="{{ route('subsections.index') }}"
                           class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium">
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn-primary">
                            Create Subsection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewImages(event) {
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = '';
            
            const files = event.target.files;
            
            if (files) {
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative group';
                            div.innerHTML = `
                                <img src="${e.target.result}" 
                                     alt="Preview ${index + 1}"
                                     class="h-24 w-full object-cover rounded-lg border-2 border-neutral-200 shadow-sm">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded-lg flex items-center justify-center">
                                    <span class="text-xs font-medium text-white opacity-0 group-hover:opacity-100 bg-black bg-opacity-50 px-2 py-1 rounded">
                                        Image ${index + 1}
                                    </span>
                                </div>
                            `;
                            container.appendChild(div);
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
