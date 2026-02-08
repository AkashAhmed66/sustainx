<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Edit Question') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('questions.update', $question) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <!-- Item -->
                        <div>
                            <label for="item_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                Item <span class="text-red-500">*</span>
                            </label>
                            <select name="item_id"
                                    id="item_id"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('item_id') border-red-500 @enderror">
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id', $question->item_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->subsection->section->name }} → {{ $item->subsection->name }} → {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Question Text -->
                        <div>
                            <label for="question_text" class="block text-sm font-medium text-neutral-700 mb-2">
                                Question Text <span class="text-red-500">*</span>
                            </label>
                            <textarea name="question_text"
                                      id="question_text"
                                      rows="4"
                                      required
                                      class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('question_text') border-red-500 @enderror"
                                      placeholder="Enter question text">{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Question Type -->
                        <div>
                            <label for="question_type_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                Question Type <span class="text-red-500">*</span>
                            </label>
                            <select name="question_type_id"
                                    id="question_type_id"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('question_type_id') border-red-500 @enderror">
                                <option value="">Select Type</option>
                                @foreach($questionTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('question_type_id', $question->question_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ ucfirst($type->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('question_type_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-neutral-700 mb-2">
                                Unit <span class="text-neutral-500 text-xs">(e.g., MWh, %, kg, tonnes)</span>
                            </label>
                            <input type="text"
                                   name="unit"
                                   id="unit"
                                   value="{{ old('unit', $question->unit) }}"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('unit') border-red-500 @enderror"
                                   placeholder="Optional">
                            @error('unit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Required -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_required"
                                       value="1"
                                       {{ old('is_required', $question->is_required) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-neutral-700">Required</span>
                            </label>
                        </div>

                        <!-- Is Active -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $question->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-neutral-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t">
                        <a href="{{ route('questions.index') }}"
                           class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium">
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn-primary">
                            Update Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Update Question') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
