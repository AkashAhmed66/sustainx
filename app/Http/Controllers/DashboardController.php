<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Subsection;
use App\Models\Assessment;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get factories connected to the user
        $userFactoryIds = $user->factories()->pluck('factories.id');
        
        // Get filter values (default to null to show all data)
        $selectedYear = $request->get('year');
        $selectedFactoryId = $request->get('factory_id');
        
        // Get assessments for user's factories with filters
        $assessmentsQuery = Assessment::whereIn('factory_id', $userFactoryIds)
            ->where('status', 'approved');
        
        // Only apply filters if values are provided and not empty
        if ($selectedYear && $selectedYear !== '') {
            $assessmentsQuery->where('year', $selectedYear);
        }
        
        if ($selectedFactoryId && $selectedFactoryId !== '') {
            $assessmentsQuery->where('factory_id', $selectedFactoryId);
        }
        
        $assessmentIds = $assessmentsQuery->pluck('id');
        
        // Get sections with subsections
        $sections = Section::with(['subsections' => function($q) use ($assessmentIds) {
            $q->where('is_active', true)
              ->orderBy('order_no')
              ->with([
                  'images' => function($imgQuery) {
                      $imgQuery->orderBy('order_no');
                  },
                  'items' => function($iq) use ($assessmentIds) {
                      $iq->where('is_active', true)
                         ->orderBy('order_no')
                         ->with(['questions' => function($qq) {
                             $qq->where('is_active', true)
                                ->where('question_type_id', 1); // Only numeric questions
                         }]);
                  }
              ]);
        }])->where('is_active', true)
          ->orderBy('order_no')
          ->get();
        
        // Calculate cumulative data for each subsection (first item only)
        foreach ($sections as $section) {
            foreach ($section->subsections as $subsection) {
                $firstItem = $subsection->items->first();
                
                if ($firstItem && $firstItem->questions->count() > 0) {
                    $questionIds = $firstItem->questions->pluck('id');
                    
                    // Get total of calculated answers (numeric_value) for these questions
                    $total = Answer::whereIn('assessment_id', $assessmentIds)
                        ->whereIn('question_id', $questionIds)
                        ->sum('numeric_value');
                    
                    $subsection->cumulative_total = $total;
                    
                    // Get output unit from first question if available
                    $firstQuestion = $firstItem->questions->first();
                    $subsection->unit = $firstQuestion ? $firstQuestion->output_unit : '';
                } else {
                    $subsection->cumulative_total = 0;
                    $subsection->unit = '';
                }
            }
        }
        
        // Get available years from assessments
        $availableYears = Assessment::whereIn('factory_id', $userFactoryIds)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        // Get user's factories for filter
        $factories = $user->factories;
        
        return view('dashboard', compact(
            'sections',
            'availableYears',
            'selectedYear',
            'factories',
            'selectedFactoryId'
        ));
    }
    
    /**
     * Display comparison dashboard with year-over-year data.
     */
    public function comparison(Request $request)
    {
        $user = auth()->user();
        $userFactoryIds = $user->factories()->pluck('factories.id');
        
        // Get available years
        $availableYears = Assessment::whereIn('factory_id', $userFactoryIds)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        // Get selected years from request (default to last 3 years if available)
        $selectedYears = $request->get('years', []);
        if (empty($selectedYears) && $availableYears->count() > 0) {
            $selectedYears = $availableYears->take(3)->toArray();
        }
        
        // Get selected subsection ID from request
        $selectedSubsectionId = $request->get('subsection_id');
        
        // Get sections with subsections
        $sections = Section::with(['subsections' => function($q) {
            $q->where('is_active', true)
              ->orderBy('order_no')
              ->with(['images' => function($imgQuery) {
                  $imgQuery->orderBy('order_no');
              }]);
        }])->where('is_active', true)
          ->orderBy('order_no')
          ->get();
        
        // Prepare comparison data for selected subsection
        $comparisonData = null;
        $selectedSubsection = null;
        $unit = '';
        
        if ($selectedSubsectionId) {
            $selectedSubsection = Subsection::with([
                'images' => function($imgQuery) {
                    $imgQuery->orderBy('order_no');
                },
                'items' => function($q) {
                    $q->where('is_active', true)
                      ->orderBy('order_no')
                      ->with(['questions' => function($qq) {
                          $qq->where('is_active', true)
                             ->where('question_type_id', 1); // Only numeric questions
                      }]);
                }
            ])->find($selectedSubsectionId);
            
            if ($selectedSubsection) {
                $firstItem = $selectedSubsection->items->first();
                
                if ($firstItem && $firstItem->questions->count() > 0) {
                    $questionIds = $firstItem->questions->pluck('id');
                    $comparisonData = [];
                    
                    foreach ($selectedYears as $year) {
                        $assessmentIds = Assessment::whereIn('factory_id', $userFactoryIds)
                            ->where('status', 'approved')
                            ->where('year', $year)
                            ->pluck('id');
                        
                        $total = Answer::whereIn('assessment_id', $assessmentIds)
                            ->whereIn('question_id', $questionIds)
                            ->sum('numeric_value');
                        
                        $count = Answer::whereIn('assessment_id', $assessmentIds)
                            ->whereIn('question_id', $questionIds)
                            ->count();
                        
                        $comparisonData[] = [
                            'year' => $year,
                            'total' => $total,
                            'count' => $count,
                        ];
                    }
                    
                    // Get unit from first question
                    $firstQuestion = $firstItem->questions->first();
                    $unit = $firstQuestion ? $firstQuestion->output_unit : '';
                } else {
                    $unit = '';
                }
            }
        }
        
        // Get user's factories for potential future filtering
        $factories = $user->factories;
        
        return view('dashboard.comparison', compact(
            'sections',
            'availableYears',
            'selectedYears',
            'selectedSubsection',
            'comparisonData',
            'unit',
            'factories'
        ));
    }
    
    /**
     * Display subsection details with visualizations.
     */
    public function subsectionDetails(Request $request, Subsection $subsection)
    {
        $user = auth()->user();
        $userFactoryIds = $user->factories()->pluck('factories.id');
        
        // Get filter values (default to null to show all data)
        $selectedYear = $request->get('year');
        $selectedFactoryId = $request->get('factory_id');
        
        // Get assessments for user's factories with filters
        $assessmentsQuery = Assessment::whereIn('factory_id', $userFactoryIds)
            ->where('status', 'approved')
            ->with('factory');
        
        // Only apply filters if values are provided and not empty
        if ($selectedYear && $selectedYear !== '') {
            $assessmentsQuery->where('year', $selectedYear);
        }
        
        if ($selectedFactoryId && $selectedFactoryId !== '') {
            $assessmentsQuery->where('factory_id', $selectedFactoryId);
        }
        
        $assessments = $assessmentsQuery->get();
        $assessmentIds = $assessments->pluck('id');
        
        // Load subsection with section, items, questions and images
        $subsection->load([
            'section',
            'images' => function($imgQuery) {
                $imgQuery->orderBy('order_no');
            },
            'items' => function($q) {
                $q->where('is_active', true)
                  ->orderBy('order_no')
                  ->with(['questions' => function($qq) {
                      $qq->where('is_active', true)
                         ->with(['questionType', 'options']);
                  }]);
            }
        ]);
        
        // Prepare data for visualizations - grouped by item
        $visualizationData = [];
        
        foreach ($subsection->items as $item) {
            $itemData = [
                'item' => $item,
                'numeric_questions' => [],
                'mcq_questions' => [],
                'multiple_select_questions' => [],
            ];
            
            // Group questions by type
            foreach ($item->questions as $question) {
                if ($question->question_type_id == 1) {
                    // Numeric question
                    $answers = Answer::whereIn('assessment_id', $assessmentIds)
                        ->where('question_id', $question->id)
                        ->with('assessment.factory')
                        ->get();
                    
                    if ($answers->count() > 0) {
                        $total = $answers->sum('numeric_value');
                        $itemData['numeric_questions'][] = [
                            'question' => $question,
                            'total' => $total,
                            'average' => $answers->avg('numeric_value'),
                            'count' => $answers->count(),
                        ];
                    }
                    
                } elseif ($question->question_type_id == 2) {
                    // MCQ question
                    $answers = Answer::whereIn('assessment_id', $assessmentIds)
                        ->where('question_id', $question->id)
                        ->with(['option', 'assessment.factory'])
                        ->get();
                    
                    if ($answers->count() > 0) {
                        $optionCounts = $answers->groupBy('option_id')->map(function($group) {
                            return [
                                'option' => $group->first()->option->option_text ?? 'N/A',
                                'count' => $group->count(),
                            ];
                        })->values();
                        
                        $itemData['mcq_questions'][] = [
                            'question' => $question,
                            'chart_data' => $optionCounts,
                        ];
                    }
                    
                } elseif ($question->question_type_id == 3) {
                    // Multiple Select question
                    $answers = Answer::whereIn('assessment_id', $assessmentIds)
                        ->where('question_id', $question->id)
                        ->whereNotNull('selected_options')
                        ->with('assessment.factory')
                        ->get();
                    
                    if ($answers->count() > 0) {
                        // Flatten all selected options and count occurrences
                        $allSelectedOptions = [];
                        foreach ($answers as $answer) {
                            if (is_array($answer->selected_options)) {
                                $allSelectedOptions = array_merge($allSelectedOptions, $answer->selected_options);
                            }
                        }
                        
                        $optionCounts = collect($allSelectedOptions)
                            ->countBy()
                            ->map(function($count, $optionId) use ($question) {
                                $option = $question->options->firstWhere('id', $optionId);
                                return [
                                    'option' => $option ? $option->option_text : 'N/A',
                                    'count' => $count,
                                ];
                            })
                            ->values();
                        
                        $itemData['multiple_select_questions'][] = [
                            'question' => $question,
                            'chart_data' => $optionCounts,
                        ];
                    }
                }
            }
            
            // Only add items that have data
            if (count($itemData['numeric_questions']) > 0 || 
                count($itemData['mcq_questions']) > 0 || 
                count($itemData['multiple_select_questions']) > 0) {
                $visualizationData[] = $itemData;
            }
        }
        
        // Get available years
        $availableYears = Assessment::whereIn('factory_id', $userFactoryIds)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        // Get user's factories
        $factories = $user->factories;
        
        return view('dashboard.subsection-details', compact(
            'subsection',
            'visualizationData',
            'availableYears',
            'selectedYear',
            'factories',
            'selectedFactoryId'
        ));
    }
}
