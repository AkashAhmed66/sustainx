<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Factory;
use App\Models\Question;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'subsections.items.questions' => function($query) {
                $query->where('is_active', true)
                      ->with('questionType', 'options');
            }
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
            'subsections.items.questions' => function($query) {
                $query->where('is_active', true)
                      ->with(['questionType', 'options' => function($q) {
                          $q->orderBy('order_no');
                      }, 'equation.factors' => function($q) {
                          $q->orderBy('sn');
                      }]);
            }
        ])->where('is_active', true)
          ->orderBy('order_no')
          ->get();

        // Get existing answers keyed by question_id
        $existingAnswers = $assessment->answers->keyBy('question_id');

        return view('assessments.perform', compact('assessment', 'sections', 'existingAnswers'));
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
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['answers'] as $answerData) {
                if (empty($answerData['value']) && empty($answerData['option_id'])) {
                    continue; // Skip empty answers
                }

                // Get the question to determine type
                $question = Question::with(['questionType', 'equation.factors'])
                    ->findOrFail($answerData['question_id']);

                $dataToSave = [
                    'assessment_id' => $assessment->id,
                    'question_id' => $answerData['question_id'],
                    'item_id' => $answerData['item_id'],
                ];

                // Handle based on question type
                if ($question->question_type_id == 1) {
                    // Numeric type - perform calculation if factors exist
                    $inputValue = floatval($answerData['value'] ?? 0);
                    
                    if ($question->equation && $question->equation->factors->count() > 0) {
                        $result = $inputValue;
                        
                        // Apply factors sequentially
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
                        
                        $dataToSave['numeric_value'] = $result;
                    } else {
                        // No factors, just store the input value
                        $dataToSave['numeric_value'] = $inputValue;
                    }
                    
                    $dataToSave['option_id'] = null;
                    $dataToSave['text_value'] = null;
                } elseif ($question->question_type_id == 2) {
                    // MCQ type - store option_id
                    $dataToSave['option_id'] = $answerData['option_id'] ?? null;
                    $dataToSave['numeric_value'] = null;
                    $dataToSave['text_value'] = null;
                }

                // Update or create answer
                Answer::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'question_id' => $answerData['question_id'],
                    ],
                    $dataToSave
                );
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
}
