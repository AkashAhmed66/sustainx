<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Factory;
use App\Models\Question;
use App\Models\Section;
use App\Models\SupportingDocument;
use App\Models\User;
use App\Notifications\AssessmentSubmittedNotification;
use App\Notifications\AssessmentApprovedNotification;
use App\Notifications\AssessmentRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Assessment::with('factory');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('year', 'like', "%{$search}%")
                  ->orWhere('period', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('factory', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);

        $columns = [
            'factory' => 'Factory',
            'year' => 'Year',
            'period' => 'Period',
            'status' => 'Status',
            'submitted_at' => 'Submitted At',
            'created_at' => 'Created At',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Assessments Management',
            'tableTitle' => 'All Assessments',
            'createRoute' => route('assessments.create'),
            'createText' => 'Create Assessment',
            'editRoute' => 'assessments.edit',
            'destroyRoute' => 'assessments.destroy',
            'bulkDeleteRoute' => route('assessments.bulk-delete'),
            'searchPlaceholder' => 'Search assessments...',
        ];

        return view('assessments.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $factories = Factory::where('is_active', true)->orderBy('name')->get();
        return view('assessments.create', compact('factories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'factory_id' => 'required|exists:factories,id',
            'year' => 'required|integer|min:2000|max:2100',
            'period' => 'required|in:annual,quarterly',
            'status' => 'required|in:draft,submitted,in_review,approved,rejected',
        ]);

        Assessment::create($validated);

        return redirect()->route('assessments.index')
            ->with('success', 'Assessment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Assessment $assessment)
    {
        // Load assessment with all necessary relationships.
        $assessment->load([
            'factory.factoryType',
            'factory.country',
            'answers.question.subsection.section',
            'answers.question.questionType',
            'answers.option'
        ]);

        // Get all sections with subsection-level questions for this assessment.
        $sections = Section::with([
            'subsections' => function ($subsectionQuery) {
                $subsectionQuery->where('is_active', true)
                    ->orderBy('order_no')
                    ->with([
                        'questions' => function ($questionQuery) {
                            $questionQuery->where('is_active', true)
                                ->whereNull('parent_question_id')
                                ->orderBy('id')
                                ->with([
                                    'questionType',
                                    'options',
                                    'childQuestions' => function ($childQuery) {
                                        $childQuery->where('is_active', true)
                                            ->orderBy('child_order_no')
                                            ->orderBy('id')
                                            ->with(['questionType', 'equation.factors']);
                                    },
                                ]);
                        },
                    ]);
            },
        ])->where('is_active', true)
          ->orderBy('order_no')
          ->get();

        // Get existing answers for this assessment.
        $existingAnswers = $assessment->answers->keyBy('question_id');
        $supportingDocumentsByQuestion = $this->loadSupportingDocumentsByQuestion($assessment);
        $reviewDocuments = $this->loadReviewDocuments($assessment);

        return view('assessments.show', compact('assessment', 'sections', 'existingAnswers', 'supportingDocumentsByQuestion', 'reviewDocuments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assessment $assessment)
    {
        $factories = Factory::where('is_active', true)->orderBy('name')->get();
        return view('assessments.edit', compact('assessment', 'factories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'factory_id' => 'required|exists:factories,id',
            'year' => 'required|integer|min:2000|max:2100',
            'period' => 'required|in:annual,quarterly',
            'status' => 'required|in:draft,submitted,in_review,approved,rejected',
        ]);

        // If status is being changed to submitted, set submitted_at
        if ($validated['status'] === 'submitted' && $assessment->status !== 'submitted') {
            $validated['submitted_at'] = now();
        }

        $assessment->update($validated);

        return redirect()->route('assessments.index')
            ->with('success', 'Assessment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return redirect()->route('assessments.index')
            ->with('success', 'Assessment deleted successfully.');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('assessments.index')
                ->with('error', 'No assessments selected.');
        }

        Assessment::whereIn('id', $ids)->delete();

        return redirect()->route('assessments.index')
            ->with('success', count($ids) . ' assessment(s) deleted successfully.');
    }

    /**
     * Show the perform assessment page.
     */
    public function perform(Assessment $assessment)
    {
        // Load assessment with necessary relationships.
        $assessment->load([
            'factory.country',
            'answers.question',
            'answers.option'
        ]);

        // Get all sections with active subsection-level questions.
        $sections = Section::with([
            'subsections' => function ($subsectionQuery) {
                $subsectionQuery->where('is_active', true)
                    ->orderBy('order_no')
                    ->with([
                        'questions' => function ($questionQuery) {
                            $questionQuery->where('is_active', true)
                                ->whereNull('parent_question_id')
                                ->orderBy('id')
                                ->with([
                                    'questionType',
                                    'options' => function ($q) {
                                        $q->orderBy('order_no');
                                    },
                                    'equation.factors' => function ($q) {
                                        $q->orderBy('sn');
                                    },
                                    'childQuestions' => function ($childQuery) {
                                        $childQuery->where('is_active', true)
                                            ->orderBy('child_order_no')
                                            ->orderBy('id')
                                            ->with([
                                                'questionType',
                                                'equation.factors' => function ($q) {
                                                    $q->orderBy('sn');
                                                },
                                            ]);
                                    },
                                ]);
                        },
                    ]);
            },
        ])->where('is_active', true)
          ->orderBy('order_no')
          ->get();

        // Get existing answers keyed by question_id
        $existingAnswers = $assessment->answers->keyBy('question_id');
        $supportingDocumentsByQuestion = $this->loadSupportingDocumentsByQuestion($assessment);

        $questionDependencyMap = [];
        $initialAnswerState = [];

        foreach ($sections as $section) {
            foreach ($section->subsections as $subsection) {
                foreach ($subsection->questions as $question) {
                    $questionDependencyMap[$question->id] = [
                        'question_type_id' => (int) $question->question_type_id,
                        'parent_question_id' => $question->parent_question_id ? (int) $question->parent_question_id : null,
                        'depends_on_question_id' => $question->depends_on_question_id ? (int) $question->depends_on_question_id : null,
                        'depends_on_option_id' => $question->depends_on_option_id ? (int) $question->depends_on_option_id : null,
                    ];

                    $existingAnswer = $existingAnswers->get($question->id);
                    $initialAnswerState[$question->id] = [
                        'selectedOptionId' => $existingAnswer?->option_id ? (int) $existingAnswer->option_id : null,
                        'selectedOptionIds' => array_map('intval', $existingAnswer?->selected_options ?? []),
                    ];

                    foreach ($question->childQuestions as $childQuestion) {
                        $questionDependencyMap[$childQuestion->id] = [
                            'question_type_id' => (int) $childQuestion->question_type_id,
                            'parent_question_id' => $childQuestion->parent_question_id ? (int) $childQuestion->parent_question_id : null,
                            'depends_on_question_id' => $childQuestion->depends_on_question_id ? (int) $childQuestion->depends_on_question_id : null,
                            'depends_on_option_id' => $childQuestion->depends_on_option_id ? (int) $childQuestion->depends_on_option_id : null,
                        ];

                        $childExistingAnswer = $existingAnswers->get($childQuestion->id);
                        $initialAnswerState[$childQuestion->id] = [
                            'selectedOptionId' => $childExistingAnswer?->option_id ? (int) $childExistingAnswer->option_id : null,
                            'selectedOptionIds' => array_map('intval', $childExistingAnswer?->selected_options ?? []),
                        ];
                    }
                }
            }
        }

        return view('assessments.perform', compact('assessment', 'sections', 'existingAnswers', 'questionDependencyMap', 'initialAnswerState', 'supportingDocumentsByQuestion'));
    }

    /**
     * Store answers for the assessment.
     */
    public function storeAnswers(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.subsection_id' => 'required|exists:subsections,id',
            'answers.*.value' => 'nullable',
            'answers.*.option_id' => 'nullable|exists:options,id',
            'answers.*.option_ids' => 'nullable|array',
            'answers.*.option_ids.*' => 'exists:options,id',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|array',
            'documents.*.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png|max:10240',
            'submit_action' => 'nullable|in:save,submit',
            'save_question_id' => 'nullable|integer|exists:questions,id',
        ]);

        $saveQuestionId = isset($validated['save_question_id']) ? (int) $validated['save_question_id'] : null;
        $saveQuestion = null;
        $entityQuestionIds = null;

        if ($saveQuestionId) {
            $saveQuestion = Question::select('id', 'subsection_id', 'parent_question_id', 'depends_on_question_id')
                ->findOrFail($saveQuestionId);
            $saveQuestion = $this->resolveQuestionEntityRoot($saveQuestion);
            $saveQuestionId = (int) $saveQuestion->id;
            $entityQuestionIds = $this->collectQuestionEntityIds($saveQuestion);
        }

        $answers = collect($validated['answers']);
        if ($entityQuestionIds) {
            $answers = $answers
                ->filter(fn ($answer) => $entityQuestionIds->contains((int) ($answer['question_id'] ?? 0)))
                ->values();
        }

        $questionIds = $answers
            ->pluck('question_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $questions = $this->loadQuestionsForSubmission($questionIds);

        $submittedAnswerState = $this->buildSubmittedAnswerState($answers->all(), $questions);
        $visibilityMemo = [];

        DB::beginTransaction();
        try {
            foreach ($answers as $answerData) {
                $questionId = (int) $answerData['question_id'];
                $subsectionId = (int) $answerData['subsection_id'];
                $question = $questions->get($questionId);

                if (!$question) {
                    continue;
                }

                if ($question->subsection_id && (int) $question->subsection_id !== $subsectionId) {
                    throw ValidationException::withMessages([
                        'answers' => 'Invalid question and subsection combination submitted.',
                    ]);
                }

                $isVisible = $this->isQuestionVisibleForSubmission(
                    $questionId,
                    $questions,
                    $submittedAnswerState,
                    $visibilityMemo
                );

                if (!$isVisible) {
                    Answer::where('assessment_id', $assessment->id)
                        ->where('question_id', $questionId)
                        ->delete();
                    continue;
                }

                $dataToSave = [
                    'assessment_id' => $assessment->id,
                    'question_id' => $questionId,
                ];

                // Handle based on question type
                if ($question->question_type_id == 1) {
                    // Numeric type - perform calculation if factors exist
                    if (!isset($answerData['value']) || $answerData['value'] === '' || $answerData['value'] === null) {
                        Answer::where('assessment_id', $assessment->id)
                            ->where('question_id', $questionId)
                            ->delete();
                        continue;
                    }

                    $inputValue = floatval($answerData['value']);
                    
                    // Store actual answer (user input)
                    $dataToSave['actual_answer'] = $inputValue;
                    
                    if ($question->equation && $question->equation->factors->count() > 0) {
                        $result = $inputValue;
                        
                        // Apply factors sequentially for calculated answer
                        foreach ($question->equation->factors as $factor) {
                            switch ($factor->operation) {
                                case 'multiply':
                                    $result *= floatval($factor->factor_value);
                                    break;
                                case 'add':
                                    $result += floatval($factor->factor_value);
                                    break;
                                case 'subtract':
                                    $result -= floatval($factor->factor_value);
                                    break;
                                case 'divide':
                                    if (floatval($factor->factor_value) != 0) {
                                        $result /= floatval($factor->factor_value);
                                    }
                                    break;
                            }
                        }
                        
                        // Store calculated answer
                        $dataToSave['numeric_value'] = $result;
                    } else {
                        // No factors, calculated answer is same as actual answer
                        $dataToSave['numeric_value'] = $inputValue;
                    }
                    
                    $dataToSave['option_id'] = null;
                    $dataToSave['text_value'] = null;
                    $dataToSave['selected_options'] = null;
                } elseif ($question->question_type_id == 2) {
                    // MCQ type - store option_id
                    $selectedOptionId = isset($answerData['option_id']) ? (int) $answerData['option_id'] : null;
                    $validOptionIds = $question->options->pluck('id')->map(fn ($id) => (int) $id)->all();

                    if (!$selectedOptionId || !in_array($selectedOptionId, $validOptionIds, true)) {
                        Answer::where('assessment_id', $assessment->id)
                            ->where('question_id', $questionId)
                            ->delete();
                        continue;
                    }

                    $dataToSave['option_id'] = $selectedOptionId;
                    $dataToSave['numeric_value'] = null;
                    $dataToSave['actual_answer'] = null;
                    $dataToSave['text_value'] = null;
                    $dataToSave['selected_options'] = null;
                } elseif ($question->question_type_id == 3) {
                    // Multiple Select type - store array of option_ids
                    $validOptionIds = $question->options->pluck('id')->map(fn ($id) => (int) $id)->all();
                    $selectedOptionIds = collect($answerData['option_ids'] ?? [])
                        ->map(fn ($id) => (int) $id)
                        ->filter(fn ($id) => in_array($id, $validOptionIds, true))
                        ->values()
                        ->all();

                    if (count($selectedOptionIds) === 0) {
                        Answer::where('assessment_id', $assessment->id)
                            ->where('question_id', $questionId)
                            ->delete();
                        continue;
                    }

                    $dataToSave['selected_options'] = $selectedOptionIds;
                    $dataToSave['option_id'] = null;
                    $dataToSave['numeric_value'] = null;
                    $dataToSave['actual_answer'] = null;
                    $dataToSave['text_value'] = null;
                }

                // Update or create answer
                Answer::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'question_id' => $questionId,
                    ],
                    $dataToSave
                );
            }

            $this->storeSupportingDocuments($request, $assessment, $saveQuestionId);

            // Check if submitting for review
            if ($request->submit_action === 'submit') {
                $assessment->update([
                    'status' => 'in_review',
                    'submitted_at' => now(),
                ]);
                
                // Notify all admins and managers about the submission
                $adminsAndManagers = User::role(['admin', 'manager'])->get();
                Notification::send($adminsAndManagers, new AssessmentSubmittedNotification($assessment, Auth::user()));
                
                DB::commit();
                return redirect()->route('assessments.show', $assessment)
                    ->with('success', 'Assessment submitted for review successfully.');
            }

            DB::commit();
            $successMessage = $saveQuestionId
                ? 'Question progress and supporting documents saved successfully.'
                : 'Assessment answers saved successfully.';

            return redirect()->route('assessments.perform', $assessment)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save answers: ' . $e->getMessage());
        }
    }

    /**
     * Load supporting documents grouped by root question entity.
     */
    private function loadSupportingDocumentsByQuestion(Assessment $assessment): Collection
    {
        return $assessment->supportingDocuments()
            ->with('uploader')
            ->whereNotNull('question_id')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('question_id');
    }

    /**
     * Load review documents attached at assessment level.
     */
    private function loadReviewDocuments(Assessment $assessment): Collection
    {
        return $assessment->supportingDocuments()
            ->with('uploader')
            ->whereNull('question_id')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Store review files uploaded during approve/reject actions.
     */
    private function storeReviewDocuments(Request $request, Assessment $assessment): int
    {
        $reviewFiles = $request->file('review_documents', []);
        if (!is_array($reviewFiles) || count($reviewFiles) === 0) {
            return 0;
        }

        $uploadedCount = 0;

        foreach ($reviewFiles as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = now()->format('YmdHis') . '_review_' . uniqid();
            if ($extension !== '') {
                $fileName .= '.' . $extension;
            }

            $directory = 'supporting_documents/assessment_' . $assessment->id . '/review';
            $filePath = $file->storeAs($directory, $fileName, 'public');

            SupportingDocument::create([
                'assessment_id' => $assessment->id,
                'question_id' => null,
                'item_id' => null,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'uploaded_by' => Auth::id(),
            ]);

            $uploadedCount++;
        }

        return $uploadedCount;
    }

    /**
     * Store uploaded supporting documents for the requested question entity or the whole assessment.
     */
    private function storeSupportingDocuments(Request $request, Assessment $assessment, ?int $saveQuestionId = null): void
    {
        $documentBatches = collect($request->file('documents', []));

        if ($documentBatches->isEmpty()) {
            return;
        }

        if ($saveQuestionId) {
            $documentBatches = $documentBatches
                ->filter(fn ($files, $questionId) => (int) $questionId === $saveQuestionId);
        }

        if ($documentBatches->isEmpty()) {
            return;
        }

        $questions = Question::select('id', 'subsection_id', 'parent_question_id', 'depends_on_question_id')
            ->whereIn('id', $documentBatches->keys()->map(fn ($id) => (int) $id)->all())
            ->get()
            ->keyBy('id');

        foreach ($documentBatches as $questionId => $files) {
            $question = $questions->get((int) $questionId);

            if (!$question) {
                continue;
            }

            $rootQuestion = $this->resolveQuestionEntityRoot($question);

            foreach ((array) $files as $file) {
                if (!$file instanceof UploadedFile) {
                    continue;
                }

                $this->storeSupportingDocumentFile($assessment, $rootQuestion, $file);
            }
        }
    }

    /**
     * Persist one supporting document for a root question entity.
     */
    private function storeSupportingDocumentFile(Assessment $assessment, Question $question, UploadedFile $file): void
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = now()->format('YmdHis') . '_' . $question->id . '_' . uniqid();
        if ($extension !== '') {
            $fileName .= '.' . $extension;
        }

        $directory = 'supporting_documents/assessment_' . $assessment->id . '/question_' . $question->id;
        $filePath = $file->storeAs($directory, $fileName, 'public');

        SupportingDocument::create([
            'assessment_id' => $assessment->id,
            'question_id' => $question->id,
            'item_id' => null,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'uploaded_by' => Auth::id(),
        ]);
    }

    /**
     * Resolve the top-most root question for a save/upload entity.
     */
    private function resolveQuestionEntityRoot(Question $question): Question
    {
        $current = $question;
        $visited = [];

        while (true) {
            $nextId = $current->parent_question_id ?: $current->depends_on_question_id;

            if (!$nextId || in_array((int) $nextId, $visited, true)) {
                return $current;
            }

            $visited[] = (int) $current->id;

            $nextQuestion = Question::select('id', 'subsection_id', 'parent_question_id', 'depends_on_question_id')
                ->find((int) $nextId);

            if (!$nextQuestion) {
                return $current;
            }

            $current = $nextQuestion;
        }
    }

    /**
     * Collect all questions that belong to one root question entity.
     */
    private function collectQuestionEntityIds(Question $rootQuestion): Collection
    {
        $subsectionQuestions = Question::select('id', 'subsection_id', 'parent_question_id', 'depends_on_question_id')
            ->where('subsection_id', $rootQuestion->subsection_id)
            ->get();

        $collectedIds = collect([(int) $rootQuestion->id]);
        $pendingIds = collect([(int) $rootQuestion->id]);

        while ($pendingIds->isNotEmpty()) {
            $currentId = (int) $pendingIds->shift();

            $childIds = $subsectionQuestions
                ->filter(function (Question $question) use ($currentId) {
                    return (int) ($question->parent_question_id ?? 0) === $currentId
                        || (int) ($question->depends_on_question_id ?? 0) === $currentId;
                })
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => !$collectedIds->contains($id))
                ->values();

            if ($childIds->isEmpty()) {
                continue;
            }

            $collectedIds = $collectedIds->merge($childIds)->unique()->values();
            $pendingIds = $pendingIds->merge($childIds)->values();
        }

        return $collectedIds->values();
    }

    /**
     * Load submitted questions plus their parent/dependency chain for visibility checks.
     */
    private function loadQuestionsForSubmission(Collection $submittedQuestionIds): Collection
    {
        $questions = Question::with([
                'questionType',
                'equation.factors',
                'options:id,question_id',
            ])
            ->whereIn('id', $submittedQuestionIds->all())
            ->get()
            ->keyBy('id');

        $pendingIds = $questions
            ->flatMap(function (Question $question) {
                return [
                    $question->parent_question_id,
                    $question->depends_on_question_id,
                ];
            })
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        while ($pendingIds->isNotEmpty()) {
            $missingIds = $pendingIds
                ->filter(fn ($id) => !$questions->has((int) $id))
                ->values();

            if ($missingIds->isEmpty()) {
                break;
            }

            $additionalQuestions = Question::with([
                    'options:id,question_id',
                ])
                ->whereIn('id', $missingIds->all())
                ->get()
                ->keyBy('id');

            if ($additionalQuestions->isEmpty()) {
                break;
            }

            foreach ($additionalQuestions as $id => $question) {
                $questions->put((int) $id, $question);
            }

            $pendingIds = $additionalQuestions
                ->flatMap(function (Question $question) {
                    return [
                        $question->parent_question_id,
                        $question->depends_on_question_id,
                    ];
                })
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();
        }

        return $questions;
    }

    /**
     * Build a compact state map from submitted answers for visibility checks.
     */
    private function buildSubmittedAnswerState(array $answers, Collection $questions): array
    {
        $state = [];

        foreach ($answers as $answerData) {
            $questionId = isset($answerData['question_id']) ? (int) $answerData['question_id'] : 0;
            $question = $questions->get($questionId);

            if (!$question) {
                continue;
            }

            $state[$questionId] = [
                'selectedOptionId' => null,
                'selectedOptionIds' => [],
            ];

            if ((int) $question->question_type_id === 2) {
                $state[$questionId]['selectedOptionId'] = isset($answerData['option_id']) && $answerData['option_id'] !== ''
                    ? (int) $answerData['option_id']
                    : null;
            }

            if ((int) $question->question_type_id === 3) {
                $state[$questionId]['selectedOptionIds'] = collect($answerData['option_ids'] ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();
            }
        }

        return $state;
    }

    /**
     * Evaluate whether a question should be visible based on submitted trigger answers.
     */
    private function isQuestionVisibleForSubmission(
        int $questionId,
        Collection $questions,
        array $submittedAnswerState,
        array &$memo,
        array $trail = []
    ): bool {
        if (array_key_exists($questionId, $memo)) {
            return $memo[$questionId];
        }

        if (in_array($questionId, $trail, true)) {
            $memo[$questionId] = false;
            return false;
        }

        /** @var Question|null $question */
        $question = $questions->get($questionId);
        if (!$question) {
            $memo[$questionId] = false;
            return false;
        }

        $dependsOnQuestionId = $question->depends_on_question_id ? (int) $question->depends_on_question_id : null;
        $dependsOnOptionId = $question->depends_on_option_id ? (int) $question->depends_on_option_id : null;

        $parentQuestionId = $question->parent_question_id ? (int) $question->parent_question_id : null;
        if ($parentQuestionId) {
            $parentVisible = $this->isQuestionVisibleForSubmission(
                $parentQuestionId,
                $questions,
                $submittedAnswerState,
                $memo,
                [...$trail, $questionId]
            );

            if (!$parentVisible) {
                $memo[$questionId] = false;
                return false;
            }
        }

        if (!$dependsOnQuestionId || !$dependsOnOptionId) {
            $memo[$questionId] = true;
            return true;
        }

        $parentVisible = $this->isQuestionVisibleForSubmission(
            $dependsOnQuestionId,
            $questions,
            $submittedAnswerState,
            $memo,
            [...$trail, $questionId]
        );

        if (!$parentVisible) {
            $memo[$questionId] = false;
            return false;
        }

        /** @var Question|null $parentQuestion */
        $parentQuestion = $questions->get($dependsOnQuestionId);
        if (!$parentQuestion) {
            $memo[$questionId] = false;
            return false;
        }

        $parentState = $submittedAnswerState[$dependsOnQuestionId] ?? [
            'selectedOptionId' => null,
            'selectedOptionIds' => [],
        ];

        if ((int) $parentQuestion->question_type_id === 2) {
            $memo[$questionId] = (int) ($parentState['selectedOptionId'] ?? 0) === $dependsOnOptionId;
            return $memo[$questionId];
        }

        if ((int) $parentQuestion->question_type_id === 3) {
            $selected = array_map('intval', $parentState['selectedOptionIds'] ?? []);
            $memo[$questionId] = in_array($dependsOnOptionId, $selected, true);
            return $memo[$questionId];
        }

        $memo[$questionId] = false;
        return false;
    }

    /**
     * Approve the assessment (admin only).
     */
    public function approve(Request $request, Assessment $assessment)
    {
        if ($assessment->status !== 'in_review') {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'Only assessments in review can be approved.');
        }

        $request->validate([
            'review_documents' => 'nullable|array',
            'review_documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $uploadedCount = $this->storeReviewDocuments($request, $assessment);

            $assessment->update([
                'status' => 'approved',
            ]);

            // Notify all users connected to the factory
            $factoryUsers = $assessment->factory->users;
            Notification::send($factoryUsers, new AssessmentApprovedNotification($assessment));

            DB::commit();

            $message = 'Assessment approved successfully.';
            if ($uploadedCount > 0) {
                $message .= ' ' . $uploadedCount . ' review file(s) uploaded.';
            }

            return redirect()->route('assessments.show', $assessment)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to approve assessment: ' . $e->getMessage());
        }
    }

    /**
     * Reject the assessment (admin only).
     */
    public function reject(Request $request, Assessment $assessment)
    {
        if ($assessment->status !== 'in_review') {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'Only assessments in review can be rejected.');
        }

        $request->validate([
            'review_documents' => 'nullable|array',
            'review_documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $uploadedCount = $this->storeReviewDocuments($request, $assessment);

            $assessment->update([
                'status' => 'rejected',
            ]);

            // Notify all users connected to the factory
            $factoryUsers = $assessment->factory->users;
            $rejectionReason = 'Please review and resubmit your assessment.';
            Notification::send($factoryUsers, new AssessmentRejectedNotification($assessment, $rejectionReason));

            DB::commit();

            $message = 'Assessment rejected successfully.';
            if ($uploadedCount > 0) {
                $message .= ' ' . $uploadedCount . ' review file(s) uploaded.';
            }

            return redirect()->route('assessments.show', $assessment)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to reject assessment: ' . $e->getMessage());
        }
    }
}
