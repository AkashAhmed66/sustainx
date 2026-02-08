<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Item;
use App\Models\QuestionType;
use Illuminate\Http\Request;

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
            'unit' => 'Unit',
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
        return view('questions.create', compact('items', 'questionTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'question_text' => 'required|string',
            'question_type_id' => 'required|exists:question_types,id',
            'unit' => 'nullable|string|max:255',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        Question::create($validated);

        return redirect()->route('questions.index')
            ->with('success', 'Question created successfully.');
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
        return view('questions.edit', compact('question', 'items', 'questionTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'question_text' => 'required|string',
            'question_type_id' => 'required|exists:question_types,id',
            'unit' => 'nullable|string|max:255',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $question->update($validated);

        return redirect()->route('questions.index')
            ->with('success', 'Question updated successfully.');
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
}
