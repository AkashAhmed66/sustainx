@php
    $existingAnswer = $existingAnswers[$question->id] ?? null;
    $fieldName = "answers[{$questionKey}]";
    $dependentQuestions = $questions
        ->where('depends_on_question_id', $question->id)
        ->sortBy([
            fn ($questionItem) => $questionItem->sl_no === null ? 1 : 0,
            fn ($questionItem) => $questionItem->sl_no ?? PHP_INT_MAX,
            fn ($questionItem) => $questionItem->id,
        ])
        ->values();
    $entityDocuments = !$isRelated
        ? ($supportingDocumentsByQuestion->get($question->id) ?? collect())
        : collect();
    $documentErrors = !$isRelated
        ? collect($errors->get("documents.{$question->id}.*"))
        : collect();
    $containerClasses = $isRelated
        ? 'rounded-lg border border-amber-200 bg-white p-4 shadow-sm'
        : 'bg-neutral-50 rounded-xl p-5 border border-neutral-200 hover:border-primary-300 transition-colors';
    $badgeClasses = $isRelated
        ? 'w-5 h-5 bg-amber-100 rounded-full flex items-center justify-center text-[10px] font-bold text-amber-700 mr-2 flex-shrink-0 mt-0.5'
        : 'w-6 h-6 bg-neutral-200 rounded-full flex items-center justify-center text-xs font-bold text-neutral-700 mr-2 flex-shrink-0 mt-0.5';
@endphp

<div class="{{ $containerClasses }}"
     data-question-id="{{ $question->id }}"
     x-data="{ hasAnswer: {{ $existingAnswer ? 'true' : 'false' }} }"
     x-show="isQuestionVisible('{{ $question->id }}')"
     x-transition
     @change="hasAnswer = true; updateProgress()"
     @input.debounce.500ms="updateProgress()"
     @if($isRelated) style="margin-left: {{ $depth * 1.25 }}rem;" @endif>

    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <label class="text-sm text-neutral-900 font-semibold flex items-start">
                <span class="{{ $badgeClasses }}">{{ $displayIndex }}</span>
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
                        Unit: {{ $question->input_unit }}
                    </span>
                </div>
            @endif
        </div>
        <div class="ml-3 flex items-center gap-2">
            <span x-show="hasAnswer" class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold whitespace-nowrap">Answered</span>
            <span class="px-3 py-1 text-xs rounded-lg bg-neutral-200 text-neutral-700 font-semibold whitespace-nowrap">
                {{ ucwords(str_replace('_', ' ', $question->questionType->name ?? 'N/A')) }}
            </span>
        </div>
    </div>

    @if($question->question_type_id == 4)
        <div class="space-y-2">
            @forelse($question->childQuestions as $childIndex => $childQuestion)
                @php
                    $existingChildAnswer = $existingAnswers[$childQuestion->id] ?? null;
                    $childFieldName = "answers[{$questionKey}_{$childIndex}]";
                @endphp
                <div class="rounded-lg border border-neutral-200 bg-white p-3">
                    <input type="hidden" name="{{ $childFieldName }}[question_id]" value="{{ $childQuestion->id }}">
                    <input type="hidden" name="{{ $childFieldName }}[subsection_id]" value="{{ $subsection->id }}">

                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                        <div class="flex items-center flex-1 min-w-0">
                            <p class="text-sm font-medium text-neutral-800 truncate">{{ $childQuestion->question_text }}</p>
                            <span class="hidden sm:block mx-3 flex-1 border-t border-dashed border-neutral-300"></span>
                        </div>
                        <div class="w-full sm:w-56">
                            <input type="number"
                                   name="{{ $childFieldName }}[value]"
                                   step="any"
                                   value="{{ old($childFieldName . '.value', $existingChildAnswer->actual_answer ?? $existingChildAnswer->numeric_value ?? '') }}"
                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'bg-neutral-50 cursor-not-allowed' : '' }}"
                                   placeholder="Value"
                                   {{ $childQuestion->is_required ? 'required' : '' }}
                                   {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'readonly' : '' }}>
                        </div>
                    </div>
                    @if($childQuestion->input_unit)
                        <p class="mt-1 text-xs text-neutral-500">Input Unit: {{ $childQuestion->input_unit }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-neutral-400 italic">No child questions configured for this question.</p>
            @endforelse
        </div>
    @else
        <input type="hidden" name="{{ $fieldName }}[question_id]" value="{{ $question->id }}">
        <input type="hidden" name="{{ $fieldName }}[subsection_id]" value="{{ $subsection->id }}">

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
            </div>
        @elseif($question->question_type_id == 2)
            <div class="space-y-2">
                @forelse($question->options as $option)
                    <label class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 cursor-pointer transition-colors {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'opacity-60 cursor-not-allowed' : '' }}">
                        <input type="radio"
                               name="{{ $fieldName }}[option_id]"
                               value="{{ $option->id }}"
                               {{ old($fieldName . '.option_id', $existingAnswer->option_id ?? '') == $option->id ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 border-neutral-300 focus:ring-primary-500"
                               @change="setSingleAnswer('{{ $question->id }}', $event.target.value)"
                               {{ $question->is_required ? 'required' : '' }}
                               {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'disabled' : '' }}>
                        <span class="ml-3 text-sm text-neutral-800">{{ $option->option_text }}</span>
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
                               @change="setMultiAnswer('{{ $question->id }}', '{{ $option->id }}', $event.target.checked)"
                               {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'disabled' : '' }}>
                        <span class="ml-3 text-sm text-neutral-800">{{ $option->option_text }}</span>
                    </label>
                @empty
                    <p class="text-sm text-neutral-400 italic">No options available</p>
                @endforelse
            </div>
        @endif
    @endif

    @if($dependentQuestions->isNotEmpty())
        <div class="mt-4 space-y-3 border-l-2 border-amber-200 pl-3">
            @foreach($dependentQuestions as $dependentIndex => $dependentQuestion)
                @include('assessments.perform-question', [
                    'question' => $dependentQuestion,
                    'questions' => $questions,
                    'subsection' => $subsection,
                    'assessment' => $assessment,
                    'existingAnswers' => $existingAnswers,
                    'supportingDocumentsByQuestion' => $supportingDocumentsByQuestion,
                    'questionKey' => $questionKey . '_rel_' . $dependentIndex,
                    'displayIndex' => $displayIndex . '.' . ($dependentIndex + 1),
                    'isRelated' => true,
                    'depth' => $depth + 1,
                ])
            @endforeach
        </div>
    @endif

    @if(!$isRelated)
        <div class="mt-4 rounded-xl border border-neutral-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div>
                    <h5 class="text-sm font-semibold text-neutral-800">Supporting Documents</h5>
                    <p class="text-xs text-neutral-500 mt-1">Upload files for this question and its related questions.</p>
                </div>
                <span class="px-2.5 py-1 text-xs rounded-full bg-neutral-100 text-neutral-600 font-medium">
                    {{ $entityDocuments->count() }} file{{ $entityDocuments->count() === 1 ? '' : 's' }}
                </span>
            </div>

            @if($entityDocuments->isNotEmpty())
                <div class="mb-4 space-y-2">
                    @foreach($entityDocuments as $document)
                        <a href="{{ $document->file_url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="flex items-center justify-between gap-3 rounded-lg border border-neutral-200 px-3 py-2 hover:bg-neutral-50 transition-colors">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-neutral-800 truncate">{{ $document->original_name }}</p>
                                <p class="text-xs text-neutral-500">{{ $document->formatted_size }} • {{ $document->uploader->name ?? 'Unknown user' }} • {{ $document->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="text-xs font-medium text-primary-600 whitespace-nowrap">Open</span>
                        </a>
                    @endforeach
                </div>
            @endif

            @if($documentErrors->isNotEmpty())
                <div class="mb-3 space-y-1">
                    @foreach($documentErrors as $documentError)
                        <p class="text-sm text-red-600">{{ $documentError }}</p>
                    @endforeach
                </div>
            @endif

            <div>
                <input type="file"
                       name="documents[{{ $question->id }}][]"
                       multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png"
                       class="block w-full text-sm text-neutral-700 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100 {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'pointer-events-none opacity-60' : '' }}"
                       {{ ($assessment->status === 'approved' || $assessment->status === 'in_review') ? 'disabled' : '' }}>
                <p class="mt-2 text-xs text-neutral-500">Allowed: PDF, DOC, DOCX, XLS, XLSX, CSV, JPG, JPEG, PNG. Max 10 MB each.</p>
            </div>
        </div>

        @unless($assessment->status === 'approved' || $assessment->status === 'in_review')
            <div class="mt-4 flex justify-end">
                <button type="submit"
                        @click="submitAction = 'save'; saveQuestionId = '{{ $question->id }}'"
                        class="px-4 py-2 text-sm bg-white text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-50 transition-colors font-medium">
                    Save This Question
                </button>
            </div>
        @endunless
    @endif
</div>