<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Subsection;
use App\Models\QuestionType;
use App\Models\Equation;
use App\Models\Factor;
use App\Models\Option;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    private const TYPE_NUMERIC = 1;
    private const TYPE_MCQ = 2;
    private const TYPE_MULTIPLE_SELECT = 3;
    private const TYPE_MULTIPLE_NUMERIC = 4;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Question::with(['subsection.section', 'questionType', 'equation'])
            ->whereNull('parent_question_id');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                                $q->where('question_text', 'like', "%{$search}%")
                                    ->orWhere('sl_no', 'like', "%{$search}%")
                  ->orWhereHas('subsection', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subsection.section', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('questionType', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'sl_no');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);

        $columns = [
            'sl_no' => 'Sl No',
            'question_text' => 'Question',
            'subsection' => 'Subsection',
            'question_type' => 'Type',
            'main_question' => 'Main Question',
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
        $subsections = Subsection::with('section')
            ->whereHas('section', function($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('section_id')
            ->orderBy('order_no')
            ->get();
        $questionTypes = QuestionType::all();
        $countries = Country::orderBy('name')->get();
        $triggerQuestions = Question::with([
                'subsection.section',
                'options' => function ($query) {
                    $query->orderBy('order_no');
                },
            ])
            ->whereNull('parent_question_id')
            ->where('is_active', true)
            ->whereIn('question_type_id', [self::TYPE_MCQ, self::TYPE_MULTIPLE_SELECT])
            ->orderBy('question_text')
            ->get();
        
        // Prepare default data for Alpine.js
        $defaultOptions = json_encode([['option_text' => '', 'option_value' => '', 'order_no' => 1]]);
        $defaultFactors = json_encode([['sn' => 1, 'operation' => 'multiply', 'factor_value' => '', 'country_id' => '']]);
        $defaultChildQuestions = json_encode([
            [
                'id' => null,
                'question_text' => '',
                'input_unit' => '',
                'equation_name' => '',
                'factors' => [
                    ['sn' => 1, 'operation' => 'multiply', 'factor_value' => '', 'country_id' => ''],
                ],
            ],
        ]);
        $triggerQuestionsJson = $this->formatDependencyQuestionsForForm($triggerQuestions);
        
        return view('questions.create', compact('subsections', 'questionTypes', 'countries', 'defaultOptions', 'defaultFactors', 'defaultChildQuestions', 'triggerQuestionsJson'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'sl_no' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('questions', 'sl_no'),
            ],
            'subsection_id' => 'required|exists:subsections,id',
            'question_text' => 'required|string',
            'question_type_id' => 'required|exists:question_types,id',
            'depends_on_question_id' => 'nullable|exists:questions,id',
            'depends_on_option_id' => 'nullable|exists:options,id',
            'input_unit' => 'nullable|string|max:255',
            'output_unit' => 'nullable|string|max:255',
            'is_main_question' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];

        if ((int) $request->question_type_id === self::TYPE_NUMERIC) {
            $rules['equation_name'] = 'nullable|string|max:255';
            $rules['factors'] = 'nullable|array';
            $rules['factors.*.sn'] = 'nullable|integer|min:1';
            $rules['factors.*.operation'] = 'nullable|string|in:multiply,add,subtract,divide';
            $rules['factors.*.factor_value'] = 'nullable|numeric';
            $rules['factors.*.country_id'] = 'nullable|exists:countries,id';
        } elseif (in_array((int) $request->question_type_id, [self::TYPE_MCQ, self::TYPE_MULTIPLE_SELECT], true)) {
            $rules['options'] = 'nullable|array';
            $rules['options.*.option_text'] = 'nullable|string|max:255';
            $rules['options.*.option_value'] = 'nullable|numeric';
            $rules['options.*.order_no'] = 'nullable|integer|min:1';
        } elseif ((int) $request->question_type_id === self::TYPE_MULTIPLE_NUMERIC) {
            $rules['child_questions'] = 'required|array|min:1';
            $rules['child_questions.*.question_text'] = 'required|string|max:1000';
            $rules['child_questions.*.input_unit'] = 'nullable|string|max:255';
            $rules['child_questions.*.equation_name'] = 'nullable|string|max:255';
            $rules['child_questions.*.factors'] = 'nullable|array';
            $rules['child_questions.*.factors.*.sn'] = 'nullable|integer|min:1';
            $rules['child_questions.*.factors.*.operation'] = 'nullable|string|in:multiply,add,subtract,divide';
            $rules['child_questions.*.factors.*.factor_value'] = 'nullable|numeric';
            $rules['child_questions.*.factors.*.country_id'] = 'nullable|exists:countries,id';
        }

        $validated = $request->validate($rules);

        [$dependsOnQuestionId, $dependsOnOptionId] = $this->resolveDependencyForQuestion(
            $validated,
            (int) $validated['subsection_id']
        );

        $isMainQuestion = $request->has('is_main_question');
        $isRequired = $request->has('is_required');
        $isActive = $request->has('is_active');
        $questionTypeId = (int) $validated['question_type_id'];

        DB::beginTransaction();
        try {
            $question = Question::create([
                'sl_no' => (int) $validated['sl_no'],
                'item_id' => null,
                'subsection_id' => (int) $validated['subsection_id'],
                'parent_question_id' => null,
                'child_order_no' => null,
                'question_text' => $validated['question_text'],
                'question_type_id' => $questionTypeId,
                'is_main_question' => $isMainQuestion,
                'depends_on_question_id' => $dependsOnQuestionId,
                'depends_on_option_id' => $dependsOnOptionId,
                'input_unit' => $questionTypeId === self::TYPE_MULTIPLE_NUMERIC ? null : ($validated['input_unit'] ?? null),
                'output_unit' => $validated['output_unit'] ?? null,
                'is_required' => $isRequired,
                'is_active' => $isActive,
            ]);

            $this->syncQuestionPayloadByType($question, $validated, $isMainQuestion, $isRequired, $isActive);

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
        if ($question->parent_question_id) {
            return redirect()->route('questions.edit', $question->parent_question_id)
                ->with('error', 'Child questions can be edited from the mother question form.');
        }

        $subsections = Subsection::with('section')
            ->whereHas('section', function($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('section_id')
            ->orderBy('order_no')
            ->get();
        $questionTypes = QuestionType::all();
        $countries = Country::orderBy('name')->get();
        $triggerQuestions = Question::with([
                'subsection.section',
                'options' => function ($query) {
                    $query->orderBy('order_no');
                },
            ])
            ->whereNull('parent_question_id')
            ->where('id', '!=', $question->id)
            ->where(function ($query) use ($question) {
                $query->where(function ($eligible) {
                    $eligible->where('is_active', true)
                        ->whereIn('question_type_id', [self::TYPE_MCQ, self::TYPE_MULTIPLE_SELECT]);
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
        }, 'childQuestions' => function ($query) {
            $query->orderBy('child_order_no')->orderBy('id');
        }, 'childQuestions.equation.factors' => function ($query) {
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
        $childQuestionsJson = json_encode(
            $question->childQuestions->map(function (Question $child) {
                return [
                    'id' => $child->id,
                    'question_text' => $child->question_text,
                    'input_unit' => $child->input_unit,
                    'equation_name' => $child->equation->name ?? '',
                    'factors' => $child->equation && $child->equation->factors->count() > 0
                        ? $child->equation->factors->map(function (Factor $factor) {
                            return [
                                'sn' => $factor->sn,
                                'operation' => $factor->operation,
                                'factor_value' => $factor->factor_value,
                                'country_id' => $factor->country_id,
                            ];
                        })->values()->all()
                        : [
                            ['sn' => 1, 'operation' => 'multiply', 'factor_value' => '', 'country_id' => ''],
                        ],
                ];
            })->values()->all()
        );
        $triggerQuestionsJson = $this->formatDependencyQuestionsForForm($triggerQuestions);

        return view('questions.edit', compact('question', 'subsections', 'questionTypes', 'countries', 'optionsJson', 'factorsJson', 'equationName', 'childQuestionsJson', 'triggerQuestionsJson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        if ($question->parent_question_id) {
            return redirect()->route('questions.edit', $question->parent_question_id)
                ->with('error', 'Child questions can be edited from the mother question form.');
        }

        $rules = [
            'sl_no' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('questions', 'sl_no')->ignore($question->id),
            ],
            'subsection_id' => 'required|exists:subsections,id',
            'question_text' => 'required|string',
            'question_type_id' => 'required|exists:question_types,id',
            'depends_on_question_id' => 'nullable|exists:questions,id',
            'depends_on_option_id' => 'nullable|exists:options,id',
            'input_unit' => 'nullable|string|max:255',
            'output_unit' => 'nullable|string|max:255',
            'is_main_question' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];

        if ((int) $request->question_type_id === self::TYPE_NUMERIC) {
            $rules['equation_name'] = 'nullable|string|max:255';
            $rules['factors'] = 'nullable|array';
            $rules['factors.*.sn'] = 'nullable|integer|min:1';
            $rules['factors.*.operation'] = 'nullable|string|in:multiply,add,subtract,divide';
            $rules['factors.*.factor_value'] = 'nullable|numeric';
            $rules['factors.*.country_id'] = 'nullable|exists:countries,id';
        } elseif (in_array((int) $request->question_type_id, [self::TYPE_MCQ, self::TYPE_MULTIPLE_SELECT], true)) {
            $rules['options'] = 'nullable|array';
            $rules['options.*.option_text'] = 'nullable|string|max:255';
            $rules['options.*.option_value'] = 'nullable|numeric';
            $rules['options.*.order_no'] = 'nullable|integer|min:1';
        } elseif ((int) $request->question_type_id === self::TYPE_MULTIPLE_NUMERIC) {
            $rules['child_questions'] = 'required|array|min:1';
            $rules['child_questions.*.id'] = 'nullable|integer|exists:questions,id';
            $rules['child_questions.*.question_text'] = 'required|string|max:1000';
            $rules['child_questions.*.input_unit'] = 'nullable|string|max:255';
            $rules['child_questions.*.equation_name'] = 'nullable|string|max:255';
            $rules['child_questions.*.factors'] = 'nullable|array';
            $rules['child_questions.*.factors.*.sn'] = 'nullable|integer|min:1';
            $rules['child_questions.*.factors.*.operation'] = 'nullable|string|in:multiply,add,subtract,divide';
            $rules['child_questions.*.factors.*.factor_value'] = 'nullable|numeric';
            $rules['child_questions.*.factors.*.country_id'] = 'nullable|exists:countries,id';
        }

        $validated = $request->validate($rules);

        [$dependsOnQuestionId, $dependsOnOptionId] = $this->resolveDependencyForQuestion(
            $validated,
            (int) $validated['subsection_id'],
            $question
        );

        $isMainQuestion = $request->has('is_main_question');
        $isRequired = $request->has('is_required');
        $isActive = $request->has('is_active');
        $questionTypeId = (int) $validated['question_type_id'];

        DB::beginTransaction();
        try {
            $question->update([
                'sl_no' => (int) $validated['sl_no'],
                'item_id' => null,
                'subsection_id' => (int) $validated['subsection_id'],
                'parent_question_id' => null,
                'child_order_no' => null,
                'question_text' => $validated['question_text'],
                'question_type_id' => $questionTypeId,
                'is_main_question' => $isMainQuestion,
                'depends_on_question_id' => $dependsOnQuestionId,
                'depends_on_option_id' => $dependsOnOptionId,
                'input_unit' => $questionTypeId === self::TYPE_MULTIPLE_NUMERIC ? null : ($validated['input_unit'] ?? null),
                'output_unit' => $validated['output_unit'] ?? null,
                'is_required' => $isRequired,
                'is_active' => $isActive,
            ]);

            $this->syncQuestionPayloadByType($question, $validated, $isMainQuestion, $isRequired, $isActive);

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
     * Sync related payload (options/equation/children) based on question type.
     */
    private function syncQuestionPayloadByType(Question $question, array $validated, bool $isMainQuestion, bool $isRequired, bool $isActive): void
    {
        $typeId = (int) $validated['question_type_id'];

        if ($typeId === self::TYPE_NUMERIC) {
            $question->options()->delete();
            $question->childQuestions()->delete();
            $this->syncEquationForQuestion(
                $question,
                $validated['equation_name'] ?? null,
                $validated['factors'] ?? []
            );

            return;
        }

        if (in_array($typeId, [self::TYPE_MCQ, self::TYPE_MULTIPLE_SELECT], true)) {
            $question->childQuestions()->delete();
            $this->syncEquationForQuestion($question, null, []);
            $this->syncOptionsForQuestion($question, $validated['options'] ?? []);

            return;
        }

        if ($typeId === self::TYPE_MULTIPLE_NUMERIC) {
            $question->options()->delete();
            $this->syncEquationForQuestion($question, null, []);
            $this->syncChildQuestionsForMother(
                $question,
                $validated['child_questions'] ?? [],
                $validated['output_unit'] ?? null,
                $isMainQuestion,
                $isRequired,
                $isActive
            );
        }
    }

    /**
     * Replace all options for a question.
     */
    private function syncOptionsForQuestion(Question $question, array $options): void
    {
        $question->options()->delete();

        $validOptions = collect($options)
            ->filter(fn ($option) => !empty($option['option_text']))
            ->values();

        foreach ($validOptions as $index => $optionData) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionData['option_text'],
                'option_value' => $optionData['option_value'] ?? null,
                'order_no' => isset($optionData['order_no']) ? (int) $optionData['order_no'] : ($index + 1),
            ]);
        }
    }

    /**
     * Replace equation/factors for a numeric question.
     */
    private function syncEquationForQuestion(Question $question, ?string $equationName, array $factors): void
    {
        $validFactors = collect($factors)
            ->filter(fn ($factor) => isset($factor['factor_value']) && $factor['factor_value'] !== '' && $factor['factor_value'] !== null)
            ->values();

        $hasEquationData = !empty($equationName) || $validFactors->count() > 0;

        if (!$hasEquationData) {
            if ($question->equation) {
                $question->equation->factors()->delete();
                $question->equation->delete();
            }

            return;
        }

        if ($question->equation) {
            $question->equation->factors()->delete();
            $question->equation->delete();
        }

        $equation = Equation::create([
            'question_id' => $question->id,
            'name' => $equationName ?: 'Equation',
        ]);

        foreach ($validFactors as $index => $factorData) {
            Factor::create([
                'equation_id' => $equation->id,
                'sn' => isset($factorData['sn']) ? (int) $factorData['sn'] : ($index + 1),
                'operation' => $factorData['operation'] ?? 'multiply',
                'factor_value' => $factorData['factor_value'],
                'country_id' => $factorData['country_id'] ?? null,
            ]);
        }
    }

    /**
     * Upsert child numeric questions for a multiple_numeric mother question.
     */
    private function syncChildQuestionsForMother(Question $motherQuestion, array $childRows, ?string $sharedOutputUnit, bool $isMainQuestion, bool $isRequired, bool $isActive): void
    {
        $existingChildren = $motherQuestion->childQuestions()->with('equation.factors')->get()->keyBy('id');
        $keptChildIds = [];

        foreach (array_values($childRows) as $index => $childData) {
            $childText = trim((string) ($childData['question_text'] ?? ''));
            if ($childText === '') {
                continue;
            }

            $childId = isset($childData['id']) && $childData['id'] !== '' ? (int) $childData['id'] : null;

            if ($childId && !$existingChildren->has($childId)) {
                throw ValidationException::withMessages([
                    'child_questions' => 'Invalid child question payload submitted.',
                ]);
            }

            if ($childId && $existingChildren->has($childId)) {
                $child = $existingChildren->get($childId);
                $child->update([
                    'sl_no' => null,
                    'item_id' => null,
                    'subsection_id' => $motherQuestion->subsection_id,
                    'parent_question_id' => $motherQuestion->id,
                    'child_order_no' => $index + 1,
                    'question_text' => $childText,
                    'question_type_id' => self::TYPE_NUMERIC,
                    'is_main_question' => $isMainQuestion,
                    'depends_on_question_id' => null,
                    'depends_on_option_id' => null,
                    'input_unit' => $childData['input_unit'] ?? null,
                    'output_unit' => $sharedOutputUnit,
                    'is_required' => $isRequired,
                    'is_active' => $isActive,
                ]);
            } else {
                $child = Question::create([
                    'sl_no' => null,
                    'item_id' => null,
                    'subsection_id' => $motherQuestion->subsection_id,
                    'parent_question_id' => $motherQuestion->id,
                    'child_order_no' => $index + 1,
                    'question_text' => $childText,
                    'question_type_id' => self::TYPE_NUMERIC,
                    'is_main_question' => $isMainQuestion,
                    'depends_on_question_id' => null,
                    'depends_on_option_id' => null,
                    'input_unit' => $childData['input_unit'] ?? null,
                    'output_unit' => $sharedOutputUnit,
                    'is_required' => $isRequired,
                    'is_active' => $isActive,
                ]);
            }

            $this->syncEquationForQuestion(
                $child,
                $childData['equation_name'] ?? null,
                $childData['factors'] ?? []
            );

            $keptChildIds[] = (int) $child->id;
        }

        if (count($keptChildIds) === 0) {
            throw ValidationException::withMessages([
                'child_questions' => 'At least one child question is required for Multiple Numeric type.',
            ]);
        }

        $existingChildren
            ->filter(fn (Question $child) => !in_array((int) $child->id, $keptChildIds, true))
            ->each
            ->delete();
    }

    /**
     * Prepare dependency question list for create/edit forms.
     */
    private function formatDependencyQuestionsForForm(Collection $questions): string
    {
        return $questions->map(function (Question $question) {
            $section = $question->subsection?->section?->name;
            $subsection = $question->subsection?->name;
            $prefix = collect([$section, $subsection])->filter()->implode(' -> ');

            return [
                'id' => $question->id,
                'subsection_id' => $question->subsection_id,
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
    private function resolveDependencyForQuestion(array $validated, int $subsectionId, ?Question $currentQuestion = null): array
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
            ->select('id', 'subsection_id', 'question_type_id', 'depends_on_question_id')
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

        if ((int) $parentQuestion->subsection_id !== $subsectionId) {
            throw ValidationException::withMessages([
                'depends_on_question_id' => 'Conditional dependency must be linked to an existing question from the same subsection.',
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
