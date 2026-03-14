<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Factory;
use App\Models\Item;
use App\Models\Question;
use App\Models\Section;
use App\Models\SupportingDocument;
use App\Models\User;
use App\Notifications\AssessmentSubmittedNotification;
use App\Notifications\AssessmentApprovedNotification;
use App\Notifications\AssessmentRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'status' => 'required|in:draft,submitted,approved',
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
        // Load assessment with all necessary relationships
        $assessment->load([
            'factory.factoryType',
            'factory.country',
            'answers.question.item.subsection.section',
            'answers.question.questionType',
            'answers.option'
        ]);

        // Get all sections with their hierarchy for this assessment
        $sections = Section::with([
            'subsections.items' => function ($query) use ($assessment) {
                $query->with([
                    'questions' => function ($questionQuery) {
                        $questionQuery->where('is_active', true)
                            ->with('questionType', 'options');
                    },
                    'supportingDocuments' => function ($docQuery) use ($assessment) {
                        $docQuery->where('assessment_id', $assessment->id);
                    },
                ]);
            },
        ])->get();

        // Get existing answers for this assessment
        $existingAnswers = $assessment->answers->keyBy('question_id');

        return view('assessments.show', compact('assessment', 'sections', 'existingAnswers'));
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
            'status' => 'required|in:draft,submitted,approved',
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
        // Load assessment with necessary relationships
        $assessment->load([
            'factory.country',
            'answers.question',
            'answers.option'
        ]);

        // Get all sections with active questions
        $sections = Section::with([
            'subsections.items' => function ($query) use ($assessment) {
                $query->with([
                    'questions' => function ($questionQuery) {
                        $questionQuery->where('is_active', true)
                            ->with(['questionType', 'options' => function($q) {
                                $q->orderBy('order_no');
                            }, 'equation.factors' => function($q) {
                                $q->orderBy('sn');
                            }]);
                    },
                    'supportingDocuments' => function ($docQuery) use ($assessment) {
                        $docQuery->where('assessment_id', $assessment->id);
                    },
                ]);
            },
        ])->where('is_active', true)
          ->orderBy('order_no')
          ->get();

        // Get existing answers keyed by question_id
        $existingAnswers = $assessment->answers->keyBy('question_id');

        $questionDependencyMap = [];
        $initialAnswerState = [];

        foreach ($sections as $section) {
            foreach ($section->subsections as $subsection) {
                foreach ($subsection->items as $item) {
                    foreach ($item->questions as $question) {
                        $questionDependencyMap[$question->id] = [
                            'question_type_id' => (int) $question->question_type_id,
                            'depends_on_question_id' => $question->depends_on_question_id ? (int) $question->depends_on_question_id : null,
                            'depends_on_option_id' => $question->depends_on_option_id ? (int) $question->depends_on_option_id : null,
                        ];

                        $existingAnswer = $existingAnswers->get($question->id);
                        $initialAnswerState[$question->id] = [
                            'selectedOptionId' => $existingAnswer?->option_id ? (int) $existingAnswer->option_id : null,
                            'selectedOptionIds' => array_map('intval', $existingAnswer?->selected_options ?? []),
                        ];
                    }
                }
            }
        }

        return view('assessments.perform', compact('assessment', 'sections', 'existingAnswers', 'questionDependencyMap', 'initialAnswerState'));
    }

    /**
     * Store answers for the assessment.
     */
    public function storeAnswers(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.item_id' => 'required|exists:items,id',
            'answers.*.value' => 'nullable',
            'answers.*.option_id' => 'nullable|exists:options,id',
            'answers.*.option_ids' => 'nullable|array',
            'answers.*.option_ids.*' => 'exists:options,id',
            'item_documents' => 'nullable|array',
            'item_documents.*' => 'nullable|array',
            'item_documents.*.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', // 10MB max
            'submit_action' => 'nullable|in:save,submit',
            'save_item_id' => 'nullable|integer|exists:items,id',
        ]);

        $saveItemId = isset($validated['save_item_id']) ? (int) $validated['save_item_id'] : null;

        $answers = collect($validated['answers']);
        if ($saveItemId) {
            $answers = $answers
                ->filter(fn ($answer) => (int) ($answer['item_id'] ?? 0) === $saveItemId)
                ->values();
        }

        $questionIds = $answers
            ->pluck('question_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $questions = Question::with([
                'questionType',
                'equation.factors',
                'options:id,question_id',
            ])
            ->whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');

        $submittedAnswerState = $this->buildSubmittedAnswerState($answers->all(), $questions);
        $visibilityMemo = [];

        DB::beginTransaction();
        try {
            foreach ($answers as $answerData) {
                $questionId = (int) $answerData['question_id'];
                $itemId = (int) $answerData['item_id'];
                $question = $questions->get($questionId);

                if (!$question) {
                    continue;
                }

                if ((int) $question->item_id !== $itemId) {
                    throw ValidationException::withMessages([
                        'answers' => 'Invalid question and item combination submitted.',
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
                    'item_id' => $itemId,
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

            // Handle file uploads per item
            foreach ($request->file('item_documents', []) as $itemId => $files) {
                if (!is_numeric($itemId)) {
                    continue;
                }

                $itemId = (int) $itemId;
                if ($saveItemId && $itemId !== $saveItemId) {
                    continue;
                }

                if (!Item::whereKey($itemId)->exists()) {
                    continue;
                }

                $files = is_array($files) ? $files : [$files];
                if (count($files) === 0) {
                    continue;
                }

                // Replace existing item documents when new files are uploaded.
                SupportingDocument::where('assessment_id', $assessment->id)
                    ->where('item_id', $itemId)
                    ->get()
                    ->each
                    ->delete();

                foreach ($files as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    $filePath = $file->storeAs(
                        "supporting_documents/{$assessment->id}/item_{$itemId}",
                        $fileName,
                        'public'
                    );

                    SupportingDocument::create([
                        'assessment_id' => $assessment->id,
                        'item_id' => $itemId,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'original_name' => $originalName,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            // Check if submitting for review
            if ($request->submit_action === 'submit') {
                $assessment->update([
                    'status' => 'in_review',
                    'submitted_at' => now(),
                ]);
                
                // Notify all admins and managers about the submission
                $adminsAndManagers = User::role(['admin', 'manager'])->get();
                Notification::send($adminsAndManagers, new AssessmentSubmittedNotification($assessment, auth()->user()));
                
                DB::commit();
                return redirect()->route('assessments.show', $assessment)
                    ->with('success', 'Assessment submitted for review successfully.');
            }

            DB::commit();
            return redirect()->route('assessments.show', $assessment)
                ->with('success', 'Assessment answers saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save answers: ' . $e->getMessage());
        }
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
    public function approve(Assessment $assessment)
    {
        if ($assessment->status !== 'in_review') {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'Only assessments in review can be approved.');
        }

        $assessment->update([
            'status' => 'approved',
        ]);
        
        // Notify all users connected to the factory
        $factoryUsers = $assessment->factory->users;
        Notification::send($factoryUsers, new AssessmentApprovedNotification($assessment));

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Assessment approved successfully.');
    }

    /**
     * Reject the assessment (admin only).
     */
    public function reject(Assessment $assessment)
    {
        if ($assessment->status !== 'in_review') {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'Only assessments in review can be rejected.');
        }

        $assessment->update([
            'status' => 'draft',
        ]);
        
        // Notify all users connected to the factory
        $factoryUsers = $assessment->factory->users;
        $rejectionReason = 'Please review and resubmit your assessment.';
        Notification::send($factoryUsers, new AssessmentRejectedNotification($assessment, $rejectionReason));

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Assessment rejected and returned to draft status.');
    }
}
