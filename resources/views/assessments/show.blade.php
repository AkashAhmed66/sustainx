<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">{{ __('Assessment Details') }}</h2>
    </x-slot>

    <div class="p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <a href="{{ route('assessments.index') }}"
               class="inline-flex items-center justify-center px-4 py-2.5 h-[42px] text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            @if($assessment->status !== 'approved' && $assessment->status !== 'in_review')
                <a href="{{ route('assessments.perform', $assessment) }}"
                   class="btn-primary inline-flex items-center justify-center h-[42px]">
                    Perform Assessment
                </a>
            @endif
        </div>

        @if($assessment->status === 'in_review' && auth()->user()->can('edit assessments'))
            <div class="dashboard-card border-l-4 border-blue-500 mb-6">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-neutral-800 mb-2">Review Required</h3>
                    <p class="text-sm text-neutral-600 mb-4">This assessment is awaiting your review.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <form action="{{ route('assessments.reject', $assessment) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to reject this assessment?')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition-colors font-medium">
                                Reject
                            </button>
                        </form>
                        <form action="{{ route('assessments.approve', $assessment) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to approve this assessment?')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                Approve
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="dashboard-card mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-neutral-800 mb-4">Assessment Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-500 mb-1">Factory</label>
                        <p class="text-base font-semibold text-neutral-900">{{ $assessment->factory->name }}</p>
                        @if($assessment->factory->factoryType)
                            <p class="text-xs text-neutral-600 mt-1">{{ $assessment->factory->factoryType->name }}</p>
                        @endif
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

        <div class="space-y-6">
            @forelse($sections as $section)
                <div class="dashboard-card border-t-4 border-primary-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-neutral-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-lg">{{ $section->order_no }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-neutral-800">{{ $section->name }}</h3>
                            </div>
                        </div>

                        @forelse($section->subsections as $subsection)
                            <div class="mb-6 last:mb-0 border-l-4 border-primary-300 pl-4 py-3 bg-gradient-to-r from-primary-50 to-transparent rounded-r-lg">
                                <h4 class="text-base font-semibold text-neutral-800 mb-4">{{ $subsection->name }}</h4>

                                <div class="space-y-4">
                                    @forelse($subsection->questions as $questionIndex => $question)
                                        <div class="bg-neutral-50 rounded-lg p-4 border border-neutral-200">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 w-7 h-7 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">{{ $questionIndex + 1 }}</span>
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between mb-2">
                                                        <p class="text-sm text-neutral-800 font-medium flex-1">
                                                            {{ $question->question_text }}
                                                            @if($question->is_required)
                                                                <span class="text-red-500">*</span>
                                                            @endif
                                                        </p>
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
                                                    @elseif(isset($existingAnswers[$question->id]))
                                                        @php $answer = $existingAnswers[$question->id]; @endphp
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
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-neutral-500 italic p-4 bg-neutral-50 rounded-lg border border-neutral-200">No questions available</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-500 italic p-4 bg-neutral-50 rounded-lg border border-neutral-200">No subsections available</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="dashboard-card">
                    <div class="p-8 text-center">
                        <p class="text-neutral-600 font-medium">No sections configured for assessment</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
