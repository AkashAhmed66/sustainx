<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Assessment Details') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6">
        <!-- Action Buttons Row -->
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
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Perform Assessment
                </a>
            @endif
        </div>

        <!-- Admin Approval Actions -->
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
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject
                            </button>
                        </form>
                        <form action="{{ route('assessments.approve', $assessment) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to approve this assessment?')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Approve
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Assessment Information -->
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
                            @if($assessment->factory->address)
                                <p class="text-xs text-neutral-600 mt-1">{{ Str::limit($assessment->factory->address, 30) }}</p>
                            @endif
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
                        <div>
                            <label class="block text-sm font-medium text-neutral-500 mb-1">Created</label>
                            <p class="text-base text-neutral-900">{{ $assessment->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-neutral-600">{{ $assessment->created_at->diffForHumans() }}</p>
                        </div>
                        @if($assessment->submitted_at)
                        <div>
                            <label class="block text-sm font-medium text-neutral-500 mb-1">Submitted</label>
                            <p class="text-base text-neutral-900">{{ $assessment->submitted_at->format('M d, Y') }}</p>
                            <p class="text-xs text-neutral-600">{{ $assessment->submitted_at->diffForHumans() }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        <!-- Questions & Answers -->
        <div class="space-y-6">
            @forelse($sections as $section)
                <div class="dashboard-card border-t-4 border-primary-500">
                    <div class="p-6">
                        <!-- Section Header -->
                        <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-neutral-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-lg">{{ $section->order_no }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-neutral-800">{{ $section->name }}</h3>
                            </div>
                        </div>

                        <!-- Subsections -->
                        @forelse($section->subsections as $subsection)
                            <div class="mb-6 last:mb-0 border-l-4 border-primary-300 pl-4 py-3 bg-gradient-to-r from-primary-50 to-transparent rounded-r-lg">
                                <h4 class="text-base font-semibold text-neutral-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    {{ $subsection->name }}
                                </h4>

                                <!-- Items -->
                                @forelse($subsection->items as $item)
                                    <div class="mb-5 last:mb-0 border-2 border-neutral-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                        <!-- Item Header -->
                                        <div class="bg-gradient-to-r from-primary-600 to-primary-500 px-4 py-3">
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-base font-bold text-white">{{ $item->name }}</h5>
                                            </div>
                                            @if($item->unit)
                                                <p class="text-sm text-white/90 mt-1 flex items-center font-medium">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                    Unit: <span class="font-bold ml-1">{{ $item->unit }}</span>
                                                </p>
                                            @endif
                                        </div>
                                        
                                        <!-- Questions -->
                                        <div class="p-5 bg-white space-y-4">
                                            @forelse($item->questions as $questionIndex => $question)
                                                <div class="bg-neutral-50 rounded-lg p-4 border border-neutral-200 hover:border-primary-300 transition-colors">
                                                    <div class="flex items-start gap-3">
                                                        <!-- Question Number Badge -->
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
                                                                    {{ $question->questionType->name ?? 'N/A' }}
                                                                </span>
                                                            </div>

                                                            @if($question->output_unit)
                                                                <p class="text-xs text-neutral-500 mb-2 flex items-center">
                                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                                    </svg>
                                                                    Unit: {{ $question->output_unit }}
                                                                </p>
                                                            @endif

                                                            <!-- Display Answer -->
                                                            @if(isset($existingAnswers[$question->id]))
                                                                @php $answer = $existingAnswers[$question->id]; @endphp
                                                                <div class="mt-3 pt-3 border-t-2 border-primary-200 bg-white rounded-lg p-3">
                                                                    <div class="flex items-center justify-between mb-2">
                                                                        <p class="text-xs font-bold text-primary-600 uppercase tracking-wide flex items-center">
                                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                            </svg>
                                                                            Answer Provided
                                                                        </p>
                                                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                                                            Answered
                                                                        </span>
                                                                    </div>
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
                                                                                <div class="flex items-center">
                                                                                    <svg class="w-4 h-4 mr-2 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                                    </svg>
                                                                                    <p class="text-sm text-neutral-800 font-medium">{{ $option->option_text }}</p>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @elseif($answer->option)
                                                                        <div class="flex items-center">
                                                                            <svg class="w-4 h-4 mr-2 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                            <p class="text-sm text-neutral-800 font-medium">{{ $answer->option->option_text }}</p>
                                                                        </div>
                                                                    @endif
                                                                    <p class="text-xs text-neutral-400 mt-2 flex items-center">
                                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                        </svg>
                                                                        Updated: {{ $answer->updated_at->format('M d, Y H:i') }}
                                                                    </p>

                                                                    <!-- Supporting Documents -->
                                                                    @if($answer->supportingDocuments && $answer->supportingDocuments->count() > 0)
                                                                        <div class="mt-3 pt-3 border-t border-neutral-200">
                                                                            <p class="text-xs font-bold text-neutral-700 mb-2 flex items-center uppercase tracking-wide">
                                                                                <svg class="w-4 h-4 mr-1.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                                                </svg>
                                                                                Documents ({{ $answer->supportingDocuments->count() }})
                                                                            </p>
                                                                            <div class="space-y-2">
                                                                                @foreach($answer->supportingDocuments as $doc)
                                                                                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-primary-50 to-white border border-primary-200 rounded-lg hover:border-primary-300 transition-colors">
                                                                                        <div class="flex items-center flex-1 min-w-0 mr-3">
                                                                                            <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                                                                                                <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                                                                </svg>
                                                                                            </div>
                                                                                            <div class="ml-3 flex-1 min-w-0">
                                                                                                <p class="text-sm text-neutral-800 font-semibold truncate">{{ $doc->original_name }}</p>
                                                                                                <p class="text-xs text-neutral-500 flex items-center mt-0.5">
                                                                                                    <span>{{ $doc->formatted_size }}</span>
                                                                                                    <span class="mx-1.5">•</span>
                                                                                                    <span>{{ $doc->created_at->format('M d, Y') }}</span>
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <a href="{{ $doc->file_url }}" 
                                                                                           target="_blank"
                                                                                           download="{{ $doc->original_name }}"
                                                                                           class="px-4 py-2 text-xs bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex-shrink-0 inline-flex items-center font-semibold shadow-sm hover:shadow-md">
                                                                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                                                            </svg>
                                                                                            Download
                                                                                        </a>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div class="mt-3 pt-3 border-t-2 border-neutral-200 bg-neutral-50 rounded-lg p-3">
                                                                    <p class="text-xs text-neutral-400 italic flex items-center">
                                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                        </svg>
                                                                        No answer provided yet
                                                                    </p>
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
                                    <p class="text-sm text-neutral-500 italic p-3 bg-neutral-50 rounded-lg">No items available</p>
                                @endforelse
                            </div>
                        @empty
                            <p class="text-sm text-neutral-500 italic p-4 bg-neutral-50 rounded-lg border border-neutral-200">No subsections available</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="dashboard-card">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-neutral-600 font-medium">No sections configured for assessment</p>
                        <p class="text-sm text-neutral-400 mt-1">Please configure sections and questions to begin</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
