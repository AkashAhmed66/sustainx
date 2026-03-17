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

                    @if($reviewDocuments->isNotEmpty())
                        <div class="mb-4 space-y-2">
                            @foreach($reviewDocuments as $document)
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

                    @if($errors->has('review_documents') || $errors->has('review_documents.*'))
                        <div class="mb-4 space-y-1">
                            @error('review_documents')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @foreach($errors->get('review_documents.*') as $errorGroup)
                                @foreach($errorGroup as $errorMessage)
                                    <p class="text-sm text-red-600">{{ $errorMessage }}</p>
                                @endforeach
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Upload Review Files (Optional)</label>
                            <input type="file"
                                   name="review_documents[]"
                                   multiple
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png"
                                   class="block w-full text-sm text-neutral-700 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100">
                            <p class="mt-2 text-xs text-neutral-500">Allowed: PDF, DOC, DOCX, XLS, XLSX, CSV, JPG, JPEG, PNG. Max 10 MB each.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="submit"
                                    formaction="{{ route('assessments.reject', $assessment) }}"
                                    onclick="return confirm('Are you sure you want to reject this assessment?')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition-colors font-medium">
                                Reject
                            </button>
                            <button type="submit"
                                    formaction="{{ route('assessments.approve', $assessment) }}"
                                    onclick="return confirm('Are you sure you want to approve this assessment?')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                Approve
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($reviewDocuments->isNotEmpty() && !($assessment->status === 'in_review' && auth()->user()->can('edit assessments')))
            <div class="dashboard-card mb-6">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-neutral-800 mb-3">Review Files</h3>
                    <div class="space-y-2">
                        @foreach($reviewDocuments as $document)
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
                            @elseif($assessment->status === 'rejected') bg-red-100 text-red-800
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
                                    @php
                                        $rootQuestions = $subsection->questions->whereNull('depends_on_question_id')->values();
                                    @endphp
                                    @forelse($rootQuestions as $questionIndex => $question)
                                        @include('assessments.show-question', [
                                            'question' => $question,
                                            'questions' => $subsection->questions,
                                            'existingAnswers' => $existingAnswers,
                                            'supportingDocumentsByQuestion' => $supportingDocumentsByQuestion,
                                            'displayIndex' => (string) ($questionIndex + 1),
                                            'isRelated' => false,
                                            'depth' => 0,
                                        ])
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
