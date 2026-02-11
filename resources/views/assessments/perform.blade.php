<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Perform Assessment') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6">
        <!-- Assessment Form -->
        <form action="{{ route('assessments.storeAnswers', $assessment) }}" method="POST" enctype="multipart/form-data" 
              x-data="assessmentForm({{ json_encode($sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name])) }})">
            @csrf

            <!-- Top Section: Filters/Progress and Information -->
            <div class="mb-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Left Column: Filters and Progress Meter -->
                <div class="space-y-4">
                    <!-- Top Row: Back Button and Section Filter -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Back Button -->
                        <div>
                            <a href="{{ route('assessments.show', $assessment) }}"
                               class="inline-flex items-center justify-center px-4 py-2.5 h-[42px] text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors font-medium w-full">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to Details
                            </a>
                        </div>

                        <!-- Section Filter -->
                        <div>
                            <select x-model="selectedSection" 
                                    class="w-full h-[42px] px-4 py-2.5 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                                <option value="">All Sections</option>
                                <template x-for="section in availableSections" :key="section.id">
                                    <option :value="section.id" x-text="section.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Bottom Row: Progress Meter -->
                    <div class="dashboard-card">
                        <div class="p-6">
                            <h3 class="text-xs font-semibold text-neutral-600 mb-4 uppercase tracking-wide">Assessment Progress</h3>
                            <div class="flex items-center justify-center">
                                <!-- Semicircle Gauge -->
                                <div class="relative w-full max-w-[220px]">
                                    <!-- SVG Gauge -->
                                    <svg class="w-full h-auto" viewBox="0 0 200 110" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Background Arc (Gray) -->
                                        <path
                                            d="M 30,100 A 70,70 0 0,1 170,100"
                                            fill="none"
                                            stroke="#e5e7eb"
                                            stroke-width="18"
                                            stroke-linecap="round"
                                        />
                                        
                                        <!-- Progress Arc (Colored) -->
                                        <path
                                            d="M 30,100 A 70,70 0 0,1 170,100"
                                            fill="none"
                                            stroke-width="18"
                                            stroke-linecap="round"
                                            class="transition-all duration-700 ease-out"
                                            :stroke="progressPercentage >= 75 ? '#10b981' : progressPercentage >= 50 ? '#3b82f6' : progressPercentage >= 25 ? '#f59e0b' : '#ef4444'"
                                            :stroke-dasharray="220"
                                            :stroke-dashoffset="220 - (220 * progressPercentage / 100)"
                                        />
                                        
                                        <!-- Center Text - Percentage -->
                                        <text 
                                            x="100" 
                                            y="78" 
                                            text-anchor="middle" 
                                            font-size="36"
                                            font-weight="bold"
                                            :fill="progressPercentage >= 75 ? '#10b981' : progressPercentage >= 50 ? '#3b82f6' : progressPercentage >= 25 ? '#f59e0b' : '#ef4444'"
                                            x-text="progressPercentage + '%'">
                                        </text>
                                        
                                        <!-- Progress Label -->
                                        <text 
                                            x="100" 
                                            y="98" 
                                            text-anchor="middle" 
                                            fill="#6b7280"
                                            font-size="10"
                                            font-weight="600"
                                            letter-spacing="1">
                                            COMPLETED
                                        </text>
                                    </svg>
                                    
                                    <!-- Counter Below Gauge -->
                                    <div class="text-center mt-1">
                                        <p class="text-sm font-semibold text-neutral-700">
                                            <span x-text="answeredCount" class="text-lg font-bold" 
                                                  :class="progressPercentage >= 75 ? 'text-green-600' : progressPercentage >= 50 ? 'text-blue-600' : progressPercentage >= 25 ? 'text-orange-600' : 'text-red-600'"></span>
                                            <span class="text-neutral-400 mx-1">/</span>
                                            <span x-text="totalQuestions" class="text-neutral-600 font-bold"></span>
                                            <span class="text-neutral-500 ml-1.5">Questions</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Assessment Information -->
                <div class="dashboard-card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4">Assessment Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-1">Factory</label>
                                <p class="text-base font-semibold text-neutral-900">{{ $assessment->factory->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-1">Location</label>
                                <p class="text-base text-neutral-900">{{ $assessment->factory->country->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-1">Period</label>
                                <p class="text-base text-neutral-900">{{ $assessment->year }} - {{ ucfirst($assessment->period) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-1">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($assessment->status === 'approved') bg-green-100 text-green-800
                                    @elseif($assessment->status === 'in_review') bg-blue-100 text-blue-800
                                    @elseif($assessment->status === 'submitted') bg-indigo-100 text-indigo-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $assessment->status === 'in_review' ? 'In Review' : ucfirst(str_replace('_', ' ', $assessment->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @forelse($sections as $sectionIndex => $section)
                    <div class="dashboard-card" 
                         x-show="selectedSection === '' || selectedSection == '{{ $section->id }}'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100">
                        <div class="p-6">
                            <!-- Section Header -->
                            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-primary-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-white font-bold">
                                        {{ $section->order_no }}
                                    </div>
                                    <h3 class="text-xl font-bold text-neutral-800">{{ $section->name }}</h3>
                                </div>
                                <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-medium">
                                    Section {{ $section->order_no }}
                                </span>
                            </div>

                            <!-- Subsections -->
                            @forelse($section->subsections as $subsectionIndex => $subsection)
                                <!-- Subsection Container -->
                                <div class="mb-8 last:mb-0 bg-gradient-to-r from-neutral-50 to-white border-l-4 border-primary-400 rounded-r-xl p-6 shadow-sm">
                                    <!-- Subsection Header -->
                                    <div class="flex items-center mb-5 pb-3 border-b border-neutral-200">
                                        <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-bold text-neutral-800">{{ $subsection->name }}</h4>
                                    </div>

                                    <!-- Items (Mother Questions) -->
                                    @forelse($subsection->items as $itemIndex => $item)
                                        <!-- Item Container - Mother Question -->
                                        <div class="mb-6 last:mb-0 bg-white border-2 border-neutral-200 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200">
                                            <!-- Item Header -->
                                            <div class="bg-gradient-to-r from-primary-50 to-primary-100 px-5 py-4 border-b-2 border-primary-200 rounded-t-xl">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-7 h-7 bg-primary-600 rounded-lg flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                                            </svg>
                                                        </div>
                                                        <h5 class="text-base font-bold text-primary-900">{{ $item->name }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Questions under Item -->
                                            <div class="p-5 space-y-4">
                                                @forelse($item->questions as $questionIndex => $question)
                                                    @php
                                                        $existingAnswer = $existingAnswers[$question->id] ?? null;
                                                        $fieldName = "answers[{$sectionIndex}_{$subsectionIndex}_{$itemIndex}_{$questionIndex}]";
                                                    @endphp
                                                    
                                                    <div class="bg-neutral-50 rounded-xl p-5 border border-neutral-200 hover:border-primary-300 transition-colors"
                                                         x-data="{ hasAnswer: {{ $existingAnswer ? 'true' : 'false' }} }"
                                                         @change="hasAnswer = true; $root.updateProgress()"
                                                         @input.debounce.500ms="$root.updateProgress()">
                                                        <!-- Question Header -->
                                                        <div class="flex items-start justify-between mb-4">
                                                            <div class="flex-1">
                                                                <label class="text-sm text-neutral-900 font-semibold flex items-start">
                                                                    <span class="w-6 h-6 bg-neutral-200 rounded-full flex items-center justify-center text-xs font-bold text-neutral-700 mr-2 flex-shrink-0 mt-0.5">
                                                                        {{ $questionIndex + 1 }}
                                                                    </span>
                                                                    <span class="flex-1">
                                                                        {{ $question->question_text }}
                                                                        @if($question->is_required)
                                                                            <span class="text-red-500 ml-1">*</span>
                                                                        @endif
                                                                    </span>
                                                                </label>
                                                                @if($question->input_unit)
                                                                    <div class="ml-8 mt-2">
                                                                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold border border-blue-200">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                                            </svg>
                                                                            Unit: {{ $question->input_unit }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="ml-3 flex flex-col items-end space-y-2">
                                                                <span class="px-3 py-1 text-xs rounded-lg bg-neutral-200 text-neutral-700 font-semibold whitespace-nowrap">
                                                                    {{ ucfirst($question->questionType->name ?? 'N/A') }}
                                                                </span>
                                                                <span x-show="hasAnswer" 
                                                                      class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold flex items-center">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    Answered
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Hidden fields -->
                                                        <input type="hidden" name="{{ $fieldName }}[question_id]" value="{{ $question->id }}">
                                                        <input type="hidden" name="{{ $fieldName }}[item_id]" value="{{ $item->id }}">

                                                        <!-- Numeric Question Type -->
                                                        @if($question->question_type_id == 1)
                                                            <div>
                                                                <input type="number" 
                                                                       name="{{ $fieldName }}[value]"
                                                                       step="any"
                                                                       value="{{ old($fieldName . '.value', $existingAnswer->actual_answer ?? $existingAnswer->numeric_value ?? '') }}"
                                                                       class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'bg-neutral-50 cursor-not-allowed' : '' }}"
                                                                       placeholder="Enter numeric value"
                                                                       {{ $question->is_required ? 'required' : '' }}
                                                                       {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'readonly' : '' }}>
                                                                
                                                                @if($existingAnswer && $question->equation && $question->equation->factors->count() > 0)
                                                                    <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                                        <p class="text-xs font-medium text-green-800 mb-1">
                                                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                            Saved Answer
                                                                        </p>
                                                                        <div class="text-xs text-green-700 space-y-1">
                                                                            <p><strong>Actual Answer (Your Input):</strong> {{ number_format($existingAnswer->actual_answer, 4) }} {{ $question->output_unit }}</p>
                                                                            <p><strong>Calculated Answer:</strong> {{ number_format($existingAnswer->numeric_value, 4) }} {{ $question->output_unit }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                
                                                                @if($question->equation && $question->equation->factors->count() > 0)
                                                                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                                        <p class="text-xs font-medium text-blue-800 mb-1">
                                                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                            Calculation: {{ $question->equation->name }}
                                                                        </p>
                                                                        <p class="text-xs text-blue-700">
                                                                            Your input will be calculated using: 
                                                                            @foreach($question->equation->factors as $factor)
                                                                                @if($loop->first) Input @endif
                                                                                @switch($factor->operation)
                                                                                    @case('multiply') × @break
                                                                                    @case('add') + @break
                                                                                    @case('subtract') - @break
                                                                                    @case('divide') ÷ @break
                                                                                @endswitch
                                                                                {{ $factor->factor_value }}
                                                                                @if($factor->country)
                                                                                    <span class="text-blue-600">({{ $factor->country->name }})</span>
                                                                                @endif
                                                                            @endforeach
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                        <!-- MCQ Question Type -->
                                                        @elseif($question->question_type_id == 2)
                                                            <div class="space-y-2">
                                                                @forelse($question->options as $option)
                                                                    <label class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 cursor-pointer transition-colors {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'opacity-60 cursor-not-allowed' : '' }}">
                                                                        <input type="radio" 
                                                                               name="{{ $fieldName }}[option_id]"
                                                                               value="{{ $option->id }}"
                                                                               {{ old($fieldName . '.option_id', $existingAnswer->option_id ?? '') == $option->id ? 'checked' : '' }}
                                                                               class="w-4 h-4 text-primary-600 border-neutral-300 focus:ring-primary-500"
                                                                               {{ $question->is_required ? 'required' : '' }}
                                                                               {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'disabled' : '' }}>
                                                                        <span class="ml-3 text-sm text-neutral-800">
                                                                            {{ $option->option_text }}
                                                                            @if($option->option_value !== null)
                                                                                <span class="text-neutral-500 ml-1">({{ $option->option_value }})</span>
                                                                            @endif
                                                                        </span>
                                                                    </label>
                                                                @empty
                                                                    <p class="text-sm text-neutral-400 italic">No options available</p>
                                                                @endforelse
                                                            </div>
                                                        @elseif($question->question_type_id == 3)
                                                            <div class="space-y-2">
                                                                @forelse($question->options as $option)
                                                                    <label class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 cursor-pointer transition-colors {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'opacity-60 cursor-not-allowed' : '' }}">
                                                                        <input type="checkbox" 
                                                                               name="{{ $fieldName }}[option_ids][]"
                                                                               value="{{ $option->id }}"
                                                                               {{ is_array(old($fieldName . '.option_ids', $existingAnswer->selected_options ?? [])) && in_array($option->id, old($fieldName . '.option_ids', $existingAnswer->selected_options ?? [])) ? 'checked' : '' }}
                                                                               class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500"
                                                                               {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'disabled' : '' }}>
                                                                        <span class="ml-3 text-sm text-neutral-800">
                                                                            {{ $option->option_text }}
                                                                            @if($option->option_value !== null)
                                                                                <span class="text-neutral-500 ml-1">({{ $option->option_value }})</span>
                                                                            @endif
                                                                        </span>
                                                                    </label>
                                                                @empty
                                                                    <p class="text-sm text-neutral-400 italic">No options available</p>
                                                                @endforelse
                                                            </div>
                                                        @endif

                                                        <!-- Supporting Documents Upload -->
                                                        <div class="mt-4 pt-4 border-t border-neutral-200">
                                                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                                </svg>
                                                                Supporting Documents (Optional)
                                                            </label>
                                                            <input type="file" 
                                                                   name="{{ $fieldName }}[documents][]"
                                                                   multiple
                                                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                                                   class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'bg-neutral-50 cursor-not-allowed' : '' }}"
                                                                   {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'disabled' : '' }}>
                                                            <p class="mt-1 text-xs text-neutral-500">
                                                                Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max 10MB per file)
                                                            </p>
                                                            
                                                            @php
                                                                $existingDocs = $existingAnswer?->supportingDocuments ?? collect();
                                                            @endphp
                                                            
                                                            @if($existingDocs->count() > 0)
                                                                <div class="mt-3 space-y-2">
                                                                    <p class="text-xs font-medium text-neutral-600">Uploaded Documents:</p>
                                                                    @foreach($existingDocs as $doc)
                                                                        <div class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded">
                                                                            <div class="flex items-center flex-1 min-w-0">
                                                                                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                                                </svg>
                                                                                <span class="ml-2 text-xs text-neutral-700 truncate">{{ $doc->original_name }}</span>
                                                                                <span class="ml-2 text-xs text-neutral-500">({{ $doc->formatted_size }})</span>
                                                                            </div>
                                                                            <a href="{{ $doc->file_url }}" 
                                                                               target="_blank"
                                                                               class="ml-2 px-2 py-1 text-xs bg-primary-600 text-white rounded hover:bg-primary-700 transition-colors flex-shrink-0">
                                                                                View
                                                                            </a>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @error($fieldName . '.value')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                        @error($fieldName . '.option_id')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                @empty
                                                    <p class="text-sm text-neutral-400 italic ml-8 py-4 bg-neutral-50 rounded-lg px-4 border border-neutral-200">
                                                        No questions available for this item
                                                    </p>
                                                @endforelse
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-neutral-400 italic py-4 bg-neutral-50 rounded-lg px-4 border border-neutral-200">
                                            No items available in this subsection
                                        </p>
                                    @endforelse
                                </div>
                            @empty
                                <p class="text-sm text-neutral-400 italic py-4 bg-neutral-50 rounded-lg px-4 border border-neutral-200">
                                    No subsections available in this section
                                </p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="dashboard-card">
                        <div class="p-6 text-center">
                            <svg class="w-12 h-12 mx-auto text-neutral-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-neutral-500">No sections configured for assessment</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Submit Button -->
            @if($sections->count() > 0)
                <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <a href="{{ route('assessments.show', $assessment) }}"
                       class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium text-center">
                        Cancel
                    </a>
                    @if($assessment->status !== 'approved' && $assessment->status !== 'in_review')
                        <button type="submit"
                                name="submit_action"
                                value="save"
                                class="px-6 py-2.5 bg-white text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-50 transition-colors font-medium">
                            Save Answers
                        </button>
                        <button type="submit"
                                name="submit_action"
                                value="submit"
                                class="btn-primary"
                                onclick="return confirm('Are you sure you want to submit this assessment for review? You will not be able to edit it after submission.')">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Submit for Review
                        </button>
                    @else
                        <div class="px-6 py-2.5 bg-neutral-100 text-neutral-500 rounded-lg font-medium text-center">
                            Assessment is {{ $assessment->status === 'approved' ? 'approved' : 'under review' }} and cannot be edited
                        </div>
                    @endif
                </div>
            @endif
        </form>
    </div>

    @push('scripts')
    <script>
        function assessmentForm(sections) {
            return {
                availableSections: sections,
                selectedSection: '',
                totalQuestions: 0,
                answeredCount: 0,
                progressPercentage: 0,
                
                init() {
                    this.calculateProgress();
                    // Listen for input changes
                    this.$watch('selectedSection', () => {
                        this.$nextTick(() => this.calculateProgress());
                    });
                },
                
                calculateProgress() {
                    // Count all questions
                    const allQuestions = document.querySelectorAll('[x-data*="hasAnswer"]');
                    this.totalQuestions = allQuestions.length;
                    
                    // Count answered questions
                    this.answeredCount = 0;
                    allQuestions.forEach(question => {
                        // Check if question has any input with value
                        const inputs = question.querySelectorAll('input[type="number"], input[type="radio"]:checked, input[type="checkbox"]:checked');
                        const hasValue = Array.from(inputs).some(input => {
                            if (input.type === 'radio' || input.type === 'checkbox') {
                                return input.checked;
                            }
                            return input.value && input.value.trim() !== '';
                        });
                        
                        if (hasValue) {
                            this.answeredCount++;
                        }
                    });
                    
                    this.progressPercentage = this.totalQuestions > 0 
                        ? Math.round((this.answeredCount / this.totalQuestions) * 100)
                        : 0;
                },
                
                updateProgress() {
                    setTimeout(() => this.calculateProgress(), 100);
                }
            }
        }
        
        // Update progress on any input change
        document.addEventListener('alpine:initialized', () => {
            document.addEventListener('input', () => {
                // Trigger progress recalculation
                const event = new CustomEvent('progress-update');
                document.dispatchEvent(event);
            });
            
            document.addEventListener('change', () => {
                const event = new CustomEvent('progress-update');
                document.dispatchEvent(event);
            });
        });
    </script>
    @endpush
</x-app-layout>
