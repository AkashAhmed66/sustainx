<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-800">
                {{ __('Perform Assessment') }}
            </h2>
            <a href="{{ route('assessments.show', $assessment) }}"
               class="inline-flex items-center px-4 py-2 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium ml-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Details
            </a>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6">
        <!-- Assessment Summary Card -->
        <div class="dashboard-card mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-neutral-800 mb-4">Assessment Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                            @elseif($assessment->status === 'submitted') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($assessment->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Form -->
        <form action="{{ route('assessments.storeAnswers', $assessment) }}" method="POST" x-data="assessmentForm()">
            @csrf

            <div class="space-y-6">
                @forelse($sections as $sectionIndex => $section)
                    <div class="dashboard-card">
                        <div class="p-6">
                            <!-- Section Header -->
                            <div class="flex items-center justify-between mb-4 pb-4 border-b border-neutral-200">
                                <h3 class="text-lg font-semibold text-neutral-800">{{ $section->name }}</h3>
                                <span class="text-sm text-neutral-500">Section {{ $section->order_no }}</span>
                            </div>

                            <!-- Subsections -->
                            @forelse($section->subsections as $subsectionIndex => $subsection)
                                <div class="mb-6 last:mb-0">
                                    <h4 class="text-base font-semibold text-neutral-700 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        {{ $subsection->name }}
                                    </h4>

                                    <!-- Items -->
                                    @forelse($subsection->items as $itemIndex => $item)
                                        <div class="ml-7 mb-4 last:mb-0">
                                            <h5 class="text-sm font-medium text-neutral-600 mb-2">{{ $item->name }}</h5>
                                            
                                            <!-- Questions -->
                                            <div class="space-y-3">
                                                @forelse($item->questions as $questionIndex => $question)
                                                    @php
                                                        $existingAnswer = $existingAnswers[$question->id] ?? null;
                                                        $fieldName = "answers[{$sectionIndex}_{$subsectionIndex}_{$itemIndex}_{$questionIndex}]";
                                                    @endphp
                                                    
                                                    <div class="bg-white rounded-lg p-4 border border-neutral-200">
                                                        <!-- Question Text -->
                                                        <div class="flex items-start justify-between mb-3">
                                                            <label class="text-sm text-neutral-800 font-medium flex-1">
                                                                {{ $question->question_text }}
                                                                @if($question->is_required)
                                                                    <span class="text-red-500">*</span>
                                                                @endif
                                                                @if($question->unit)
                                                                    <span class="text-xs text-neutral-500 ml-2">({{ $question->unit }})</span>
                                                                @endif
                                                            </label>
                                                            <span class="ml-2 px-2 py-1 text-xs rounded bg-neutral-100 text-neutral-700 whitespace-nowrap">
                                                                {{ ucfirst($question->questionType->name ?? 'N/A') }}
                                                            </span>
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
                                                                       value="{{ old($fieldName . '.value', $existingAnswer->numeric_value ?? '') }}"
                                                                       class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                                                       placeholder="Enter numeric value"
                                                                       {{ $question->is_required ? 'required' : '' }}>
                                                                
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
                                                                    <label class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 cursor-pointer transition-colors">
                                                                        <input type="radio" 
                                                                               name="{{ $fieldName }}[option_id]"
                                                                               value="{{ $option->id }}"
                                                                               {{ old($fieldName . '.option_id', $existingAnswer->option_id ?? '') == $option->id ? 'checked' : '' }}
                                                                               class="w-4 h-4 text-primary-600 border-neutral-300 focus:ring-primary-500"
                                                                               {{ $question->is_required ? 'required' : '' }}>
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

                                                        @error($fieldName . '.value')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                        @error($fieldName . '.option_id')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                @empty
                                                    <p class="text-sm text-neutral-400 italic">No questions available</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-neutral-400 italic ml-7">No items available</p>
                                    @endforelse
                                </div>
                            @empty
                                <p class="text-sm text-neutral-400 italic">No subsections available</p>
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
                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('assessments.show', $assessment) }}"
                       class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="btn-primary">
                        Save Answers
                    </button>
                </div>
            @endif
        </form>
    </div>

    @push('scripts')
    <script>
        function assessmentForm() {
            return {
                // Add any Alpine.js functionality if needed
            }
        }
    </script>
    @endpush
</x-app-layout>
