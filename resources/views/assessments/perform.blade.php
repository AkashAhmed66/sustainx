<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">{{ __('Perform Assessment') }}</h2>
    </x-slot>

    <div class="p-4 sm:p-6">
                <form action="{{ route('assessments.storeAnswers', $assessment) }}" method="POST" enctype="multipart/form-data"
              x-data="assessmentForm({{ json_encode($sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name])) }}, {{ json_encode($questionDependencyMap) }}, {{ json_encode($initialAnswerState) }})">
            @csrf
            <input type="hidden" name="submit_action" x-model="submitAction">
                        <input type="hidden" name="save_question_id" x-model="saveQuestionId">

            <div class="mb-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <a href="{{ route('assessments.show', $assessment) }}"
                               class="inline-flex items-center justify-center px-4 py-2.5 h-[42px] text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors font-medium w-full">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to Details
                            </a>
                        </div>
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

                    <div class="dashboard-card">
                        <div class="p-6">
                            <h3 class="text-xs font-semibold text-neutral-600 mb-4 uppercase tracking-wide">Assessment Progress</h3>
                            <div class="flex items-center justify-center">
                                <div class="relative w-full max-w-[220px]">
                                    <svg class="w-full h-auto" viewBox="0 0 200 110" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M 30,100 A 70,70 0 0,1 170,100" fill="none" stroke="#e5e7eb" stroke-width="18" stroke-linecap="round"/>
                                        <path d="M 30,100 A 70,70 0 0,1 170,100"
                                              fill="none"
                                              stroke-width="18"
                                              stroke-linecap="round"
                                              class="transition-all duration-700 ease-out"
                                              :stroke="progressPercentage >= 75 ? '#10b981' : progressPercentage >= 50 ? '#3b82f6' : progressPercentage >= 25 ? '#f59e0b' : '#ef4444'"
                                              :stroke-dasharray="220"
                                              :stroke-dashoffset="220 - (220 * progressPercentage / 100)"/>
                                        <text x="100" y="78" text-anchor="middle" font-size="36" font-weight="bold"
                                              :fill="progressPercentage >= 75 ? '#10b981' : progressPercentage >= 50 ? '#3b82f6' : progressPercentage >= 25 ? '#f59e0b' : '#ef4444'"
                                              x-text="progressPercentage + '%'">
                                        </text>
                                        <text x="100" y="98" text-anchor="middle" fill="#6b7280" font-size="10" font-weight="600" letter-spacing="1">COMPLETED</text>
                                    </svg>
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
                                    @elseif($assessment->status === 'rejected') bg-red-100 text-red-800
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
                            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-primary-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-white font-bold">
                                        {{ $section->order_no }}
                                    </div>
                                    <h3 class="text-xl font-bold text-neutral-800">{{ $section->name }}</h3>
                                </div>
                                <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-medium">Section {{ $section->order_no }}</span>
                            </div>

                            @forelse($section->subsections as $subsectionIndex => $subsection)
                                <div class="mb-8 last:mb-0 bg-gradient-to-r from-neutral-50 to-white border-l-4 border-primary-400 rounded-r-xl p-6 shadow-sm">
                                    <div class="flex items-center mb-5 pb-3 border-b border-neutral-200">
                                        <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-bold text-neutral-800">{{ $subsection->name }}</h4>
                                    </div>

                                    <div class="space-y-4">
                                        @php
                                            $rootQuestions = $subsection->questions->whereNull('depends_on_question_id')->values();
                                        @endphp
                                        @forelse($rootQuestions as $questionIndex => $question)
                                            @include('assessments.perform-question', [
                                                'question' => $question,
                                                'questions' => $subsection->questions,
                                                'subsection' => $subsection,
                                                'assessment' => $assessment,
                                                'existingAnswers' => $existingAnswers,
                                                'supportingDocumentsByQuestion' => $supportingDocumentsByQuestion,
                                                'questionKey' => $sectionIndex . '_' . $subsectionIndex . '_' . $questionIndex,
                                                'displayIndex' => (string) ($questionIndex + 1),
                                                'isRelated' => false,
                                                'depth' => 0,
                                            ])
                                        @empty
                                            <p class="text-sm text-neutral-400 italic ml-8 py-4 bg-neutral-50 rounded-lg px-4 border border-neutral-200">No questions available for this subsection</p>
                                        @endforelse
                                    </div>

                                </div>
                            @empty
                                <p class="text-sm text-neutral-400 italic py-4 bg-neutral-50 rounded-lg px-4 border border-neutral-200">No subsections available in this section</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="dashboard-card">
                        <div class="p-6 text-center">
                            <p class="text-neutral-500">No sections configured for assessment</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($sections->count() > 0)
                <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <a href="{{ route('assessments.show', $assessment) }}"
                       class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium text-center">
                        Cancel
                    </a>
                    @if($assessment->status !== 'approved' && $assessment->status !== 'in_review')
                        <button type="submit"
                                @click="submitAction = 'submit'; saveQuestionId = ''"
                                class="btn-primary"
                                onclick="return confirm('Are you sure you want to submit this assessment for review? You will not be able to edit it after submission.')">
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
        function assessmentForm(sections, questionDependencyMap, initialAnswerState) {
            return {
                availableSections: sections,
                selectedSection: '',
                totalQuestions: 0,
                answeredCount: 0,
                progressPercentage: 0,
                submitAction: 'save',
                saveQuestionId: '',
                questionRules: questionDependencyMap || {},
                answerState: initialAnswerState || {},

                init() {
                    this.$nextTick(() => this.calculateProgress());
                    this.$watch('selectedSection', () => {
                        this.$nextTick(() => this.calculateProgress());
                    });
                },

                isQuestionVisible(questionId, trail = []) {
                    const id = String(questionId);
                    const rule = this.questionRules[id];
                    if (!rule) return true;

                    if (trail.includes(id)) return true;

                    if (rule.parent_question_id) {
                        const motherId = String(rule.parent_question_id);
                        if (!this.isQuestionVisible(motherId, [...trail, id])) return false;
                    }

                    if (!rule.depends_on_question_id || !rule.depends_on_option_id) return true;

                    const parentId = String(rule.depends_on_question_id);
                    const requiredOptionId = String(rule.depends_on_option_id);

                    if (!this.isQuestionVisible(parentId, [...trail, id])) return false;

                    const parentRule = this.questionRules[parentId];
                    if (!parentRule) return false;

                    const state = this.answerState[parentId];
                    if (!state) return false;

                    if (parentRule.question_type_id === 2) {
                        return String(state.selectedOptionId) === requiredOptionId;
                    }

                    if (parentRule.question_type_id === 3) {
                        return (state.selectedOptionIds || []).map(String).includes(requiredOptionId);
                    }

                    return false;
                },

                setSingleAnswer(questionId, optionId) {
                    const key = String(questionId);
                    const current = this.answerState[key] || { selectedOptionId: null, selectedOptionIds: [] };
                    this.answerState[key] = { ...current, selectedOptionId: parseInt(optionId) };
                    this.$nextTick(() => this.calculateProgress());
                },

                setMultiAnswer(questionId, optionId, checked) {
                    const key = String(questionId);
                    const current = this.answerState[key] || { selectedOptionId: null, selectedOptionIds: [] };
                    const id = parseInt(optionId);
                    const ids = [...(current.selectedOptionIds || [])];
                    if (checked && !ids.includes(id)) {
                        ids.push(id);
                    } else if (!checked) {
                        const idx = ids.indexOf(id);
                        if (idx > -1) ids.splice(idx, 1);
                    }
                    this.answerState[key] = { ...current, selectedOptionIds: ids };
                    this.$nextTick(() => this.calculateProgress());
                },

                calculateProgress() {
                    const allQuestions = document.querySelectorAll('[data-question-id]');
                    let total = 0;
                    let answered = 0;

                    allQuestions.forEach(questionEl => {
                        const qId = questionEl.getAttribute('data-question-id');
                        if (!this.isQuestionVisible(qId)) return;
                        if (questionEl.offsetParent === null) return;

                        const answerInputs = questionEl.querySelectorAll('input[type="number"], input[type="radio"], input[type="checkbox"]');
                        if (answerInputs.length === 0) return;

                        total++;

                        const inputs = questionEl.querySelectorAll('input[type="number"], input[type="radio"]:checked, input[type="checkbox"]:checked');
                        const hasValue = Array.from(inputs).some(input => {
                            if (input.type === 'radio' || input.type === 'checkbox') {
                                return input.checked;
                            }
                            return input.value && input.value.trim() !== '';
                        });

                        if (hasValue) answered++;
                    });

                    this.totalQuestions = total;
                    this.answeredCount = answered;
                    this.progressPercentage = total > 0 ? Math.round((answered / total) * 100) : 0;
                },

                updateProgress() {
                    setTimeout(() => this.calculateProgress(), 100);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
