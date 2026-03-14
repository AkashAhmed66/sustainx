<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Item;
use App\Models\QuestionType;
use App\Models\Equation;
use App\Models\Factor;
use App\Models\Option;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Question::with(['item.subsection.section', 'questionType', 'equation']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhereHas('item', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('questionType', function($q) use ($search) {
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
            'question_text' => 'Question',
            'item' => 'Item',
            'question_type' => 'Type',
            'input_unit' => 'Input Unit',
            'output_unit' => 'Output Unit',
            'is_required' => 'Required',
            'is_active' => 'Status',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Questions Management',
            'tableTitle' => 'All Questions',
            'createRoute' => route('questions.create'),
            'createText' => 'Create Question',
            'editRoute' => 'questions.edit',
            'destroyRoute' => 'questions.destroy',
            'bulkDeleteRoute' => route('questions.bulk-delete'),
            'searchPlaceholder' => 'Search questions...',
        ];

        return view('questions.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::with('subsection.section')
            ->whereHas('subsection.section', function($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('order_no')
            ->get();
        $questionTypes = QuestionType::all();
        $countries = Country::orderBy('name')->get();
        $triggerQuestions = Question::with([
                'item.subsection.section',
                'options' => function ($query) {
                    $query->orderBy('order_no');
                },
            ])
            ->where('is_active', true)
            ->whereIn('question_type_id', [2, 3])
            ->orderBy('question_text')
            ->get();
        
        // Prepare default data for Alpine.js
        $defaultOptions = json_encode([['option_text' => '', 'option_value' => '', 'order_no' => 1]]);
        $defaultFactors = json_encode([['sn' => 1, 'operation' => 'multiply', 'factor_value' => '', 'country_id' => '']]);
        $triggerQuestionsJson = $this->formatDependencyQuestionsForForm($triggerQuestions);
        
        return view('questions.create', compact('items', 'questionTypes', 'countries', 'defaultOptions', 'defaultFactors', 'triggerQuestionsJson'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'item_id' => 'required|exists:items,id',
            'question_text' => 'required|string',
            'question_type_id' => 'required|exists:question_types,id',
            'depends_on_question_id' => 'nullable|exists:questions,id',
            'depends_on_option_id' => 'nullable|exists:options,id',
            'input_unit' => 'nullable|string|max:255',
            'output_unit' => 'nullable|string|max:255',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];

        // Add conditional validation based on question type
        if ($request->question_type_id == 1) {
            // Numeric type - validate equation and factors
            $rules['equation_name'] = 'nullable|string|max:255';
            $rules['factors'] = 'nullable|array';
            $rules['factors.*.sn'] = 'nullable|integer|min:1';
            $rules['factors.*.operation'] = 'nullable|string|in:multiply,add,subtract,divide';
            $rules['factors.*.factor_value'] = 'nullable|numeric';
            $rules['factors.*.country_id'] = 'nullable|exists:countries,id';
        } elseif ($request->question_type_id == 2 || $request->question_type_id == 3) {
            // MCQ and Multiple Select types - validate options
            $rules['options'] = 'nullable|array';
            $rules['options.*.option_text'] = 'nullable|string|max:255';
            $rules['options.*.option_value'] = 'nullable|numeric';
            $rules['options.*.order_no'] = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules);
        [$dependsOnQuestionId, $dependsOnOptionId] = $this->resolveDependencyForQuestion(
            $validated,
            (int) $validated['item_id']
        );

        DB::beginTransaction();
        try {
            // Create question
            $question = Question::create([
                'item_id' => $validated['item_id'],
                'question_text' => $validated['question_text'],
                'question_type_id' => $validated['question_type_id'],
                'depends_on_question_id' => $dependsOnQuestionId,
                'depends_on_option_id' => $dependsOnOptionId,
                'input_unit' => $validated['input_unit'] ?? null,
                'output_unit' => $validated['output_unit'] ?? null,
                'is_required' => $request->has('is_required'),
                'is_active' => $request->has('is_active'),
            ]);

            // Handle numeric type - create equation and factors
            if ($validated['question_type_id'] == 1) {
                $hasFactors = !empty($validated['factors']) && collect($validated['factors'])->filter(function($factor) {
                    return !empty($factor['factor_value']);
                })->count() > 0;
                
                if ($hasFactors || !empty($validated['equation_name'])) {
                    $equation = Equation::create([
                        'question_id' => $question->id,
                        'name' => $validated['equation_name'] ?? 'Equation',
                    ]);

                    if ($hasFactors) {
                        foreach ($validated['factors'] as $factorData) {
                            if (!empty($factorData['factor_value'])) {
                                Factor::create([
                                    'equation_id' => $equation->id,
                                    'sn' => $factorData['sn'],
                                    'operation' => $factorData['operation'],
                                    'factor_value' => $factorData['factor_value'],
                                    'country_id' => $factorData['country_id'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }

            // Handle MCQ and Multiple Select types - create options
            if ($validated['question_type_id'] == 2 || $validated['question_type_id'] == 3) {
                $hasOptions = !empty($validated['options']) && collect($validated['options'])->filter(function($option) {
                    return !empty($option['option_text']);
                })->count() > 0;
                
                if ($hasOptions) {
                    foreach ($validated['options'] as $optionData) {
                        if (!empty($optionData['option_text'])) {
                            Option::create([
                                'question_id' => $question->id,
                                'option_text' => $optionData['option_text'],
                                'option_value' => $optionData['option_value'] ?? null,
                                'order_no' => $optionData['order_no'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('questions.index')
                ->with('success', 'Question created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create question: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $items = Item::with('subsection.section')
            ->whereHas('subsection.section', function($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('order_no')
            ->get();
        $questionTypes = QuestionType::all();
        $countries = Country::orderBy('name')->get();
        $triggerQuestions = Question::with([
                'item.subsection.section',
                'options' => function ($query) {
                    $query->orderBy('order_no');
                },
            ])
            ->where('id', '!=', $question->id)
            ->where(function ($query) use ($question) {
                $query->where(function ($eligible) {
                    $eligible->where('is_active', true)
                        ->whereIn('question_type_id', [2, 3]);
                });

                // Keep existing dependency visible in edit, even if it is no longer eligible.
                if ($question->depends_on_question_id) {
                    $query->orWhere('id', $question->depends_on_question_id);
                }
            })
            ->orderBy('question_text')
            ->get();
        
        // Load relationships
        $question->load(['options' => function ($query) {
            $query->orderBy('order_no');
        }, 'equation.factors' => function ($query) {
            $query->orderBy('sn');
        }]);
        
        // Prepare data for Alpine.js
        $existingOptions = $question->options->count() > 0 
            ? $question->options->map(function($opt) {
                return [
                    'option_text' => $opt->option_text,
                    'option_value' => $opt->option_value,
                    'order_no' => $opt->order_no
                ];
            })->toArray()
            : [['option_text' => '', 'option_value' => '', 'order_no' => 1]];
            
        $existingFactors = $question->equation && $question->equation->factors->count() > 0 
            ? $question->equation->factors->map(function($fac) {
                return [
                    'sn' => $fac->sn,
                    'operation' => $fac->operation,
                    'factor_value' => $fac->factor_value,
                    'country_id' => $fac->country_id
                ];
            })->toArray()
            : [['sn' => 1, 'operation' => 'multiply', 'factor_value' => '', 'country_id' => '']];
        
        $optionsJson = json_encode($existingOptions);
        $factorsJson = json_encode($existingFactors);
        $equationName = $question->equation->name ?? '';
        $triggerQuestionsJson = $this->formatDependencyQuestionsForForm($triggerQuestions);

        return view('questions.edit', compact('question', 'items', 'questionTypes', 'countries', 'optionsJson', 'factorsJson', 'equationName', 'triggerQuestionsJson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        // Base validation rules
        $rules = [
            'item_id' => 'required|exists:items,id',
            'question_text' => 'required|string',
            'question_type_id' => 'required|exists:question_types,id',
            'depends_on_question_id' => 'nullable|exists:questions,id',
            'depends_on_option_id' => 'nullable|exists:options,id',
            'input_unit' => 'nullable|string|max:255',
            'output_unit' => 'nullable|string|max:255',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];

        // Add conditional validation based on question type
        if ($request->question_type_id == 1) {
            // Numeric type - validate equation and factors
            $rules['equation_name'] = 'nullable|string|max:255';
            $rules['factors'] = 'nullable|array';
            $rules['factors.*.sn'] = 'nullable|integer|min:1';
            $rules['factors.*.operation'] = 'nullable|string|in:multiply,add,subtract,divide';
            $rules['factors.*.factor_value'] = 'nullable|numeric';
            $rules['factors.*.country_id'] = 'nullable|exists:countries,id';
        } elseif ($request->question_type_id == 2 || $request->question_type_id == 3) {
            // MCQ and Multiple Select types - validate options
            $rules['options'] = 'nullable|array';
            $rules['options.*.option_text'] = 'nullable|string|max:255';
            $rules['options.*.option_value'] = 'nullable|numeric';
            $rules['options.*.order_no'] = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules);
        [$dependsOnQuestionId, $dependsOnOptionId] = $this->resolveDependencyForQuestion(
            $validated,
            (int) $validated['item_id'],
            $question
        );

        DB::beginTransaction();
        try {
            // Update question
            $question->update([
                'item_id' => $validated['item_id'],
                'question_text' => $validated['question_text'],
                'question_type_id' => $validated['question_type_id'],
                'depends_on_question_id' => $dependsOnQuestionId,
                'depends_on_option_id' => $dependsOnOptionId,
                'input_unit' => $validated['input_unit'] ?? null,
                'output_unit' => $validated['output_unit'] ?? null,
                'is_required' => $request->has('is_required'),
                'is_active' => $request->has('is_active'),
            ]);

            // If type changed or type is numeric
            if ($validated['question_type_id'] == 1) {
                // Delete old options if type changed to numeric
                $question->options()->delete();
                
                $hasFactors = !empty($validated['factors']) && collect($validated['factors'])->filter(function($factor) {
                    return !empty($factor['factor_value']);
                })->count() > 0;
                
                // Handle equation and factors
                if ($hasFactors || !empty($validated['equation_name'])) {
                    // Delete old equation and its factors
                    if ($question->equation) {
                        $question->equation->factors()->delete();
                        $question->equation->delete();
                    }
                    
                    // Create new equation
                    $equation = Equation::create([
                        'question_id' => $question->id,
                        'name' => $validated['equation_name'] ?? 'Equation',
                    ]);

                    // Create factors
                    if ($hasFactors) {
                        foreach ($validated['factors'] as $factorData) {
                            if (!empty($factorData['factor_value'])) {
                                Factor::create([
                                    'equation_id' => $equation->id,
                                    'sn' => $factorData['sn'],
                                    'operation' => $factorData['operation'],
                                    'factor_value' => $factorData['factor_value'],
                                    'country_id' => $factorData['country_id'] ?? null,
                                ]);
                            }
                        }
                    }
                } else {
                    // Remove equation if no name or factors provided
                    if ($question->equation) {
                        $question->equation->factors()->delete();
                        $question->equation->delete();
                    }
                }
            }

            // If type is MCQ or Multiple Select
            if ($validated['question_type_id'] == 2 || $validated['question_type_id'] == 3) {
                // Delete old equation and factors if type changed to MCQ/Multiple Select
                if ($question->equation) {
                    $question->equation->factors()->delete();
                    $question->equation->delete();
                }
                
                // Delete old options
                $question->options()->delete();
                
                $hasOptions = !empty($validated['options']) && collect($validated['options'])->filter(function($option) {
                    return !empty($option['option_text']);
                })->count() > 0;
                
                // Create new options
                if ($hasOptions) {
                    foreach ($validated['options'] as $optionData) {
                        if (!empty($optionData['option_text'])) {
                            Option::create([
                                'question_id' => $question->id,
                                'option_text' => $optionData['option_text'],
                                'option_value' => $optionData['option_value'] ?? null,
                                'order_no' => $optionData['order_no'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('questions.index')
                ->with('success', 'Question updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('questions.index')
            ->with('success', 'Question deleted successfully.');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('questions.index')
                ->with('error', 'No questions selected.');
        }

        Question::whereIn('id', $ids)->delete();

        return redirect()->route('questions.index')
            ->with('success', count($ids) . ' question(s) deleted successfully.');
    }

    /**
     * Prepare dependency question list for create/edit forms.
     */
    private function formatDependencyQuestionsForForm(Collection $questions): string
    {
        return $questions->map(function (Question $question) {
            $section = $question->item?->subsection?->section?->name;
            $subsection = $question->item?->subsection?->name;
            $item = $question->item?->name;
            $prefix = collect([$section, $subsection, $item])->filter()->implode(' -> ');

            return [
                'id' => $question->id,
                'item_id' => $question->item_id,
                'label' => trim($prefix . ' | ' . Str::limit($question->question_text, 120), ' |'),
                'options' => $question->options->map(function (Option $option) {
                    return [
                        'id' => $option->id,
                        'option_text' => $option->option_text,
                    ];
                })->values()->all(),
            ];
        })->values()->toJson();
    }

    /**
     * Validate and normalize conditional dependency fields.
     */
    private function resolveDependencyForQuestion(array $validated, int $itemId, ?Question $currentQuestion = null): array
    {
        $dependsOnQuestionId = $validated['depends_on_question_id'] ?? null;
        $dependsOnOptionId = $validated['depends_on_option_id'] ?? null;

        if (($dependsOnQuestionId && !$dependsOnOptionId) || (!$dependsOnQuestionId && $dependsOnOptionId)) {
            throw ValidationException::withMessages([
                'depends_on_question_id' => 'Both existing question and option must be selected for conditional visibility.',
                'depends_on_option_id' => 'Both existing question and option must be selected for conditional visibility.',
            ]);
        }

        if (!$dependsOnQuestionId && !$dependsOnOptionId) {
            return [null, null];
        }

        $dependsOnQuestionId = (int) $dependsOnQuestionId;
        $dependsOnOptionId = (int) $dependsOnOptionId;

        $parentQuestion = Question::with('options:id,question_id')
            ->select('id', 'item_id', 'question_type_id', 'depends_on_question_id')
            ->find($dependsOnQuestionId);

        if (!$parentQuestion) {
            throw ValidationException::withMessages([
                'depends_on_question_id' => 'Selected existing question is invalid.',
            ]);
        }

        if (!in_array((int) $parentQuestion->question_type_id, [2, 3], true)) {
            throw ValidationException::withMessages([
                'depends_on_question_id' => 'Conditional visibility can only depend on MCQ or Multiple Select questions.',
            ]);
        }

        if ((int) $parentQuestion->item_id !== $itemId) {
            throw ValidationException::withMessages([
                'depends_on_question_id' => 'Conditional dependency must be linked to an existing question from the same item.',
            ]);
        }

        if (!$parentQuestion->options->contains('id', $dependsOnOptionId)) {
            throw ValidationException::withMessages([
                'depends_on_option_id' => 'Selected option does not belong to the selected existing question.',
            ]);
        }

        if ($currentQuestion && (int) $currentQuestion->id === $dependsOnQuestionId) {
            throw ValidationException::withMessages([
                'depends_on_question_id' => 'A question cannot depend on itself.',
            ]);
        }

        if ($currentQuestion && $this->createsDependencyCycle((int) $currentQuestion->id, $dependsOnQuestionId)) {
            throw ValidationException::withMessages([
                'depends_on_question_id' => 'This dependency creates a circular chain. Please select a different existing question.',
            ]);
        }

        return [$dependsOnQuestionId, $dependsOnOptionId];
    }

    /**
     * Prevent dependency cycles when updating a question.
     */
    private function createsDependencyCycle(int $currentQuestionId, int $candidateParentQuestionId): bool
    {
        $visited = [];
        $cursorId = $candidateParentQuestionId;

        while ($cursorId) {
            if ($cursorId === $currentQuestionId) {
                return true;
            }

            if (in_array($cursorId, $visited, true)) {
                return true;
            }

            $visited[] = $cursorId;
            $cursor = Question::select('id', 'depends_on_question_id')->find($cursorId);
            $cursorId = $cursor?->depends_on_question_id ? (int) $cursor->depends_on_question_id : 0;
        }

        return false;
    }
}
