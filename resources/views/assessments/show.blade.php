<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Assessment Details') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6">
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 mb-6">
            <a href="{{ route('assessments.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            @if($assessment->status !== 'approved' && $assessment->status !== 'in_review')
                <a href="{{ route('assessments.perform', $assessment) }}"
                   class="btn-primary inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Perform Assessment
                </a>
            @endif
        </div>

        <!-- Admin Approval Actions -->
        @if($assessment->status === 'in_review' && auth()->user()->can('edit assessments'))
            <div class="dashboard-card mb-6 border-l-4 border-blue-500">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-800 mb-1">Assessment Review Required</h3>
                            <p class="text-sm text-neutral-600">This assessment has been submitted and is awaiting your review.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                            <form action="{{ route('assessments.reject', $assessment) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to reject this assessment? It will be returned to draft status.')"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-white text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition-colors font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </form>
                            <form action="{{ route('assessments.approve', $assessment) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to approve this assessment? This action will finalize the assessment.')"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Approve
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Assessment Summary Card -->
        <div class="dashboard-card mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-neutral-800 mb-4">Assessment Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Factory -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-500 mb-1">Factory</label>
                        <p class="text-base font-semibold text-neutral-900">{{ $assessment->factory->name }}</p>
                        @if($assessment->factory->factoryType)
                            <p class="text-sm text-neutral-600 mt-1">{{ $assessment->factory->factoryType->name }}</p>
                        @endif
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-500 mb-1">Location</label>
                        <p class="text-base text-neutral-900">{{ $assessment->factory->country->name ?? 'N/A' }}</p>
                        @if($assessment->factory->address)
                            <p class="text-sm text-neutral-600 mt-1">{{ $assessment->factory->address }}</p>
                        @endif
                    </div>

                    <!-- Year & Period -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-500 mb-1">Assessment Period</label>
                        <p class="text-base text-neutral-900">{{ $assessment->year }} - {{ ucfirst($assessment->period) }}</p>
                    </div>

                    <!-- Status -->
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

                    <!-- Created At -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-500 mb-1">Created</label>
                        <p class="text-base text-neutral-900">{{ $assessment->created_at->format('M d, Y') }}</p>
                        <p class="text-sm text-neutral-600">{{ $assessment->created_at->diffForHumans() }}</p>
                    </div>

                    <!-- Submitted At -->
                    @if($assessment->submitted_at)
                    <div>
                        <label class="block text-sm font-medium text-neutral-500 mb-1">Submitted</label>
                        <p class="text-base text-neutral-900">{{ $assessment->submitted_at->format('M d, Y') }}</p>
                        <p class="text-sm text-neutral-600">{{ $assessment->submitted_at->diffForHumans() }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Questions & Answers -->
        <div class="space-y-6">
            @forelse($sections as $section)
                <div class="dashboard-card">
                    <div class="p-6">
                        <!-- Section Header -->
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-neutral-200">
                            <h3 class="text-lg font-semibold text-neutral-800">{{ $section->name }}</h3>
                            <span class="text-sm text-neutral-500">Order: {{ $section->order_no }}</span>
                        </div>

                        <!-- Subsections -->
                        @forelse($section->subsections as $subsection)
                            <div class="mb-6 last:mb-0">
                                <h4 class="text-base font-semibold text-neutral-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    {{ $subsection->name }}
                                </h4>

                                <!-- Items -->
                                @forelse($subsection->items as $item)
                                    <div class="ml-7 mb-4 last:mb-0">
                                        <h5 class="text-sm font-medium text-neutral-600 mb-2">{{ $item->name }}</h5>
                                        
                                        <!-- Questions -->
                                        <div class="space-y-3">
                                            @forelse($item->questions as $question)
                                                <div class="bg-neutral-50 rounded-lg p-4 border border-neutral-200">
                                                    <div class="flex items-start justify-between mb-2">
                                                        <p class="text-sm text-neutral-800 font-medium flex-1">
                                                            {{ $question->question_text }}
                                                            @if($question->is_required)
                                                                <span class="text-red-500">*</span>
                                                            @endif
                                                        </p>
                                                        <span class="ml-2 px-2 py-1 text-xs rounded bg-neutral-200 text-neutral-700">
                                                            {{ $question->questionType->name ?? 'N/A' }}
                                                        </span>
                                                    </div>

                                                    @if($question->unit)
                                                        <p class="text-xs text-neutral-500 mb-2">Unit: {{ $question->unit }}</p>
                                                    @endif

                                                    <!-- Display Answer -->
                                                    @if(isset($existingAnswers[$question->id]))
                                                        @php $answer = $existingAnswers[$question->id]; @endphp
                                                        <div class="mt-3 pt-3 border-t border-neutral-300">
                                                            <p class="text-xs font-medium text-neutral-500 mb-1">Answer:</p>
                                                            @if($answer->numeric_value !== null)
                                                                <p class="text-sm font-semibold text-primary-600">
                                                                    {{ $answer->numeric_value }}
                                                                    @if($question->unit)
                                                                        <span class="text-neutral-600">{{ $question->unit }}</span>
                                                                    @endif
                                                                </p>
                                                            @elseif($answer->text_value)
                                                                <p class="text-sm text-neutral-800">{{ $answer->text_value }}</p>
                                                            @elseif($answer->option)
                                                                <p class="text-sm text-neutral-800">{{ $answer->option->option_text }}</p>
                                                            @endif
                                                            <p class="text-xs text-neutral-400 mt-1">
                                                                Updated: {{ $answer->updated_at->format('M d, Y H:i') }}
                                                            </p>
                                                        </div>
                                                    @else
                                                        <div class="mt-3 pt-3 border-t border-neutral-300">
                                                            <p class="text-xs text-neutral-400 italic">No answer provided yet</p>
                                                        </div>
                                                    @endif
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
    </div>
</x-app-layout>
