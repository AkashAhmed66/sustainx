<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Factory;
use Illuminate\Http\Request;

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
}
