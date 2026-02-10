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
              ->with(['items' => function($iq) use ($assessmentIds) {
                  $iq->where('is_active', true)
                     ->orderBy('order_no')
                     ->with(['questions' => function($qq) {
                         $qq->where('is_active', true)
                            ->where('question_type_id', 1); // Only numeric questions
                     }]);
              }]);
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
                    
                    // Get unit from first question if available
                    $firstQuestion = $firstItem->questions->first();
                    $subsection->unit = $firstQuestion ? $firstQuestion->unit : '';
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
        
        // Load subsection with section, items and questions
        $subsection->load(['section', 'items' => function($q) {
            $q->where('is_active', true)
              ->orderBy('order_no')
              ->with(['questions' => function($qq) {
                  $qq->where('is_active', true)
                     ->with(['questionType', 'options']);
              }]);
        }]);
        
        // Prepare data for visualizations
        $visualizationData = [];
        
        foreach ($subsection->items as $item) {
            foreach ($item->questions as $question) {
                $questionData = [
                    'question' => $question,
                    'item' => $item,
                    'type' => $question->question_type_id,
                ];
                
                if ($question->question_type_id == 1) {
                    // Numeric question - get data for line/bar chart
                    $answers = Answer::whereIn('assessment_id', $assessmentIds)
                        ->where('question_id', $question->id)
                        ->with('assessment.factory')
                        ->get();
                    
                    $chartData = $answers->map(function($answer) {
                        return [
                            'factory' => $answer->assessment->factory->name,
                            'actual_answer' => (float) $answer->actual_answer,
                            'calculated_answer' => (float) $answer->numeric_value,
                            'year' => $answer->assessment->year,
                        ];
                    });
                    
                    $questionData['chart_data'] = $chartData;
                    $questionData['total'] = $answers->sum('numeric_value');
                    $questionData['average'] = $answers->count() > 0 ? $answers->avg('numeric_value') : 0;
                    
                } elseif ($question->question_type_id == 2) {
                    // MCQ question - get data for pie/donut chart
                    $answers = Answer::whereIn('assessment_id', $assessmentIds)
                        ->where('question_id', $question->id)
                        ->with(['option', 'assessment.factory'])
                        ->get();
                    
                    $optionCounts = $answers->groupBy('option_id')->map(function($group) {
                        return [
                            'option' => $group->first()->option->option_text ?? 'N/A',
                            'count' => $group->count(),
                        ];
                    })->values();
                    
                    $questionData['chart_data'] = $optionCounts;
                }
                
                $visualizationData[] = $questionData;
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
