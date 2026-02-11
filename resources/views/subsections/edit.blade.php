<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Edit Subsection') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('subsections.update', $subsection) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                    <option value="{{ $section->id }}" {{ old('section_id', $subsection->section_id) == $section->id ? 'selected' : '' }}>
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
                                   value="{{ old('name', $subsection->name) }}"
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
                                      placeholder="Enter subsection description">{{ old('description', $subsection->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                Subsection Images
                                <span class="text-neutral-500 text-xs">(For dashboard display - Max 5MB per image)</span>
                            </label>
                            
                            @if($subsection->images->count() > 0)
                                <div class="mb-4">
                                    <p class="text-xs font-medium text-neutral-600 mb-3">Current Images:</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                        @foreach($subsection->images as $image)
                                            <div class="relative group" id="image-{{ $image->id }}">
                                                <img src="{{ $image->image_url }}" 
                                                     alt="Subsection image {{ $loop->iteration }}"
                                                     class="h-24 w-full rounded-lg border-2 border-neutral-200 shadow-sm object-cover">
                                                <button type="button"
                                                        onclick="deleteImage({{ $image->id }})"
                                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                                                        title="Delete image">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                                <div class="absolute bottom-1 left-1 bg-black bg-opacity-50 text-white text-xs px-2 py-0.5 rounded">
                                                    #{{ $loop->iteration }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <label for="images" class="block text-sm font-medium text-neutral-700 mb-2">
                                {{ $subsection->images->count() > 0 ? 'Add More Images' : 'Upload Images' }}
                            </label>
                            <input type="file"
                                   name="images[]"
                                   id="images"
                                   multiple
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/svg+xml,image/webp"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('images') border-red-500 @enderror"
                                   onchange="previewNewImages(event)">
                            <p class="mt-1 text-xs text-neutral-500">
                                Accepted formats: JPEG, JPG, PNG, GIF, SVG, WEBP. You can select multiple images.
                            </p>
                            @error('images')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('images.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- New Image Preview Container -->
                            <div id="newImagePreviewContainer" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                        </div>

                        <!-- Order Number -->
                        <div>
                            <label for="order_no" class="block text-sm font-medium text-neutral-700 mb-2">
                                Order Number <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="order_no"
                                   id="order_no"
                                   value="{{ old('order_no', $subsection->order_no) }}"
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
                                       {{ old('is_active', $subsection->is_active) ? 'checked' : '' }}
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
                            Update Subsection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewNewImages(event) {
            const container = document.getElementById('newImagePreviewContainer');
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
                                     alt="New image ${index + 1}"
                                     class="h-24 w-full object-cover rounded-lg border-2 border-green-300 shadow-sm">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded-lg flex items-center justify-center">
                                    <span class="text-xs font-medium text-white opacity-0 group-hover:opacity-100 bg-green-600 bg-opacity-80 px-2 py-1 rounded">
                                        New #${index + 1}
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

        function deleteImage(imageId) {
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }

            fetch(`/subsection-images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the image element from DOM
                    const imageElement = document.getElementById(`image-${imageId}`);
                    if (imageElement) {
                        imageElement.remove();
                    }
                    
                    // Show success message (you can customize this)
                    alert('Image deleted successfully!');
                } else {
                    alert('Failed to delete image. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the image.');
            });
        }
    </script>
    @endpush
</x-app-layout>
