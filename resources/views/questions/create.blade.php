<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Create Question') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('questions.store') }}" method="POST" 
                      x-data="{
                          questionType: '{{ old('question_type_id', '') }}',
                          options: {{ old('options') ? json_encode(old('options')) : $defaultOptions }},
                          factors: {{ old('factors') ? json_encode(old('factors')) : $defaultFactors }},
                          equationName: '{{ old('equation_name', '') }}',
                          addOption() {
                              this.options.push({
                                  option_text: '',
                                  option_value: '',
                                  order_no: this.options.length + 1
                              });
                          },
                          removeOption(index) {
                              if (this.options.length > 1) {
                                  this.options.splice(index, 1);
                                  // Reorder
                                  this.options.forEach((opt, idx) => opt.order_no = idx + 1);
                              }
                          },
                          addFactor() {
                              this.factors.push({
                                  sn: this.factors.length + 1,
                                  operation: 'multiply',
                                  factor_value: '',
                                  country_id: ''
                              });
                          },
                          removeFactor(index) {
                              if (this.factors.length > 1) {
                                  this.factors.splice(index, 1);
                                  // Reorder
                                  this.factors.forEach((fac, idx) => fac.sn = idx + 1);
                              }
                          }
                      }">
                    @csrf

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
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
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
                                      placeholder="Enter question text">{{ old('question_text') }}</textarea>
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
                                    x-model="questionType"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('question_type_id') border-red-500 @enderror">
                                <option value="">Select Type</option>
                                @foreach($questionTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('question_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ ucfirst($type->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('question_type_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Input Unit -->
                        <div>
                            <label for="input_unit" class="block text-sm font-medium text-neutral-700 mb-2">
                                Input Unit <span class="text-neutral-500 text-xs">(Unit shown during data entry - e.g., MWh, %, kg)</span>
                            </label>
                            <input type="text"
                                   name="input_unit"
                                   id="input_unit"
                                   value="{{ old('input_unit') }}"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('input_unit') border-red-500 @enderror"
                                   placeholder="Optional">
                            @error('input_unit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Output Unit -->
                        <div>
                            <label for="output_unit" class="block text-sm font-medium text-neutral-700 mb-2">
                                Output Unit <span class="text-neutral-500 text-xs">(Unit shown in reports/dashboard - e.g., tonnes, kg CO2e)</span>
                            </label>
                            <input type="text"
                                   name="output_unit"
                                   id="output_unit"
                                   value="{{ old('output_unit') }}"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('output_unit') border-red-500 @enderror"
                                   placeholder="Optional">
                            @error('output_unit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Equation Section (for Numeric type) -->
                        <div x-show="questionType == '1'"
                            <div class="border border-neutral-200 rounded-xl p-4 bg-neutral-50">
                                <h3 class="font-medium text-neutral-800 mb-4">Equation & Factors</h3>
                                
                                <!-- Equation Name -->
                                <div class="mb-4">
                                    <label for="equation_name" class="block text-sm font-medium text-neutral-700 mb-2">
                                        Equation Name
                                    </label>
                                    <input type="text"
                                           name="equation_name"
                                           id="equation_name"
                                           x-model="equationName"
                                           class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                           placeholder="e.g., Carbon Emission Calculation">
                                </div>

                                <!-- Factors -->
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-3">
                                        Factors
                                    </label>
                                    
                                    <template x-for="(factor, index) in factors" :key="index">
                                        <div class="grid grid-cols-12 gap-3 mb-3 items-start">
                                            <!-- SN -->
                                            <div class="col-span-1">
                                                <input type="number"
                                                       :name="'factors[' + index + '][sn]'"
                                                       x-model="factor.sn"
                                                       readonly
                                                       class="w-full px-3 py-2 border border-neutral-300 rounded-lg bg-neutral-100"
                                                       placeholder="SN">
                                            </div>

                                            <!-- Operation -->
                                            <div class="col-span-2">
                                                <select :name="'factors[' + index + '][operation]'"
                                                        x-model="factor.operation"
                                                        class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                    <option value="multiply">×</option>
                                                    <option value="add">+</option>
                                                    <option value="subtract">-</option>
                                                    <option value="divide">÷</option>
                                                </select>
                                            </div>

                                            <!-- Factor Value -->
                                            <div class="col-span-3">
                                                <input type="number"
                                                       :name="'factors[' + index + '][factor_value]'"
                                                       x-model="factor.factor_value"
                                                       step="any"
                                                       class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                       placeholder="Value">
                                            </div>

                                            <!-- Country -->
                                            <div class="col-span-5">
                                                <select :name="'factors[' + index + '][country_id]'"
                                                        x-model="factor.country_id"
                                                        class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                    <option value="">Select Country (Optional)</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Remove Button -->
                                            <div class="col-span-1">
                                                <button type="button"
                                                        @click="removeFactor(index)"
                                                        class="w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Remove Factor">
                                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <button type="button"
                                            @click="addFactor()"
                                            class="mt-2 px-4 py-2 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                        + Add Factor
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Options Section (for MCQ and Multiple Select types) -->
                        <div x-show="questionType == '2' || questionType == '3'"
                            <div class="border border-neutral-200 rounded-xl p-4 bg-neutral-50">
                                <h3 class="font-medium text-neutral-800 mb-4">
                                    <span x-text="questionType == '3' ? 'Multiple Select Options' : 'Multiple Choice Options'"></span>
                                </h3>
                                
                                <template x-for="(option, index) in options" :key="index">
                                    <div class="grid grid-cols-12 gap-3 mb-3 items-start">
                                        <!-- Order No -->
                                        <div class="col-span-1">
                                            <input type="number"
                                                   :name="'options[' + index + '][order_no]'"
                                                   x-model="option.order_no"
                                                   readonly
                                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg bg-neutral-100"
                                                   placeholder="#">
                                        </div>

                                        <!-- Option Text -->
                                        <div class="col-span-7">
                                            <input type="text"
                                                   :name="'options[' + index + '][option_text]'"
                                                   x-model="option.option_text"
                                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                   placeholder="Option Text">
                                        </div>

                                        <!-- Option Value -->
                                        <div class="col-span-3">
                                            <input type="number"
                                                   :name="'options[' + index + '][option_value]'"
                                                   x-model="option.option_value"
                                                   step="any"
                                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                   placeholder="Value (Optional)">
                                        </div>

                                        <!-- Remove Button -->
                                        <div class="col-span-1">
                                            <button type="button"
                                                    @click="removeOption(index)"
                                                    class="w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Remove Option">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <button type="button"
                                        @click="addOption()"
                                        class="mt-2 px-4 py-2 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                    + Add Option
                                </button>
                            </div>
                        </div>

                        <!-- Is Required -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_required"
                                       value="1"
                                       {{ old('is_required', true) ? 'checked' : '' }}
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
                                       {{ old('is_active', true) ? 'checked' : '' }}
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
                            Create Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
