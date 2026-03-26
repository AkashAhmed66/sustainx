@php
    $answer = $existingAnswers[$question->id] ?? null;
    $dependentQuestions = $questions
        ->where('depends_on_question_id', $question->id)
        ->sortBy(fn ($questionItem) => [
            $questionItem->sl_no === null ? 1 : 0,
            $questionItem->sl_no ?? PHP_INT_MAX,
            $questionItem->id,
        ])
        ->values();
    $entityDocuments = !$isRelated
        ? ($supportingDocumentsByQuestion->get($question->id) ?? collect())
        : collect();
    $containerClasses = $isRelated
        ? 'rounded-lg border border-amber-200 bg-white p-4 shadow-sm'
        : 'bg-neutral-50 rounded-lg p-4 border border-neutral-200';
    $badgeClasses = $isRelated
        ? 'w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center'
        : 'w-7 h-7 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center';
@endphp

<div class="{{ $containerClasses }}" @if($isRelated) style="margin-left: {{ $depth * 1.25 }}rem;" @endif>
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 {{ $badgeClasses }}">
            <span class="{{ $isRelated ? 'text-amber-700' : 'text-white' }} font-bold text-sm">{{ $question->sl_no ?? $displayIndex }}</span>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-start flex-1">
                    <p class="text-sm text-neutral-800 font-medium flex-1">
                        {{ $question->question_text }}
                        @if($question->is_required)
                            <span class="text-red-500">*</span>
                        @endif
                    </p>
                </div>
                <span class="ml-2 px-2.5 py-1 text-xs rounded-full font-semibold bg-primary-100 text-primary-700 flex-shrink-0">
                    {{ ucwords(str_replace('_', ' ', $question->questionType->name ?? 'N/A')) }}
                </span>
            </div>

            @if($question->output_unit)
                <p class="text-xs text-neutral-500 mb-2">Unit: {{ $question->output_unit }}</p>
            @endif

            @if($question->question_type_id == 4)
                <div class="mt-3 pt-3 border-t-2 border-primary-200 bg-white rounded-lg p-3 space-y-2">
                    @forelse($question->childQuestions as $childQuestion)
                        @php $childAnswer = $existingAnswers[$childQuestion->id] ?? null; @endphp
                        <div class="border border-neutral-200 rounded-lg p-3 bg-neutral-50">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                <div class="flex items-center flex-1 min-w-0">
                                    <p class="text-sm text-neutral-800 font-medium truncate">{{ $childQuestion->question_text }}</p>
                                    <span class="hidden sm:block mx-3 flex-1 border-t border-dashed border-neutral-300"></span>
                                </div>

                                <div class="w-full sm:w-56">
                                    @if($childAnswer && $childAnswer->numeric_value !== null)
                                        <p class="text-base font-bold text-primary-700">
                                            {{ $childAnswer->numeric_value }}
                                            @if($childQuestion->output_unit)
                                                <span class="text-neutral-600 font-semibold">{{ $childQuestion->output_unit }}</span>
                                            @endif
                                        </p>
                                    @else
                                        <p class="text-xs text-neutral-400 italic">No answer</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-neutral-400 italic">No child questions configured.</p>
                    @endforelse
                </div>
            @elseif($answer)
                <div class="mt-3 pt-3 border-t-2 border-primary-200 bg-white rounded-lg p-3">
                    @if($answer->numeric_value !== null)
                        <p class="text-base font-bold text-primary-700">
                            {{ $answer->numeric_value }}
                            @if($question->output_unit)
                                <span class="text-neutral-600 font-semibold">{{ $question->output_unit }}</span>
                            @endif
                        </p>
                    @elseif($answer->text_value)
                        <p class="text-sm text-neutral-800 leading-relaxed">{{ $answer->text_value }}</p>
                    @elseif($answer->selected_options && count($answer->selected_options) > 0)
                        @php
                            $selectedOptions = $question->options->whereIn('id', $answer->selected_options);
                        @endphp
                        <div class="space-y-1.5">
                            @foreach($selectedOptions as $option)
                                <p class="text-sm text-neutral-800 font-medium">- {{ $option->option_text }}</p>
                            @endforeach
                        </div>
                    @elseif($answer->option)
                        <p class="text-sm text-neutral-800 font-medium">{{ $answer->option->option_text }}</p>
                    @endif
                    <p class="text-xs text-neutral-400 mt-2">Updated: {{ $answer->updated_at->format('M d, Y H:i') }}</p>
                </div>
            @else
                <div class="mt-3 pt-3 border-t-2 border-neutral-200 bg-neutral-50 rounded-lg p-3">
                    <p class="text-xs text-neutral-400 italic">No answer provided yet</p>
                </div>
            @endif

            @if($dependentQuestions->isNotEmpty())
                <div class="mt-4 space-y-3 border-l-2 border-amber-200 pl-3">
                    @foreach($dependentQuestions as $dependentIndex => $dependentQuestion)
                        @include('assessments.show-question', [
                            'question' => $dependentQuestion,
                            'questions' => $questions,
                            'existingAnswers' => $existingAnswers,
                            'supportingDocumentsByQuestion' => $supportingDocumentsByQuestion,
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
                        <h5 class="text-sm font-semibold text-neutral-800">Supporting Documents</h5>
                        <span class="px-2.5 py-1 text-xs rounded-full bg-neutral-100 text-neutral-600 font-medium">
                            {{ $entityDocuments->count() }} file{{ $entityDocuments->count() === 1 ? '' : 's' }}
                        </span>
                    </div>

                    @if($entityDocuments->isNotEmpty())
                        <div class="space-y-2">
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
                    @else
                        <p class="text-xs text-neutral-400 italic">No supporting documents uploaded.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>