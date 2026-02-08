<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Section::withCount('subsections');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'order_no');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);

        $columns = [
            'name' => 'Section Name',
            'description' => 'Description',
            'order_no' => 'Order',
            'subsections_count' => 'Subsections',
            'is_active' => 'Status',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Sections Management',
            'tableTitle' => 'All Sections',
            'createRoute' => route('sections.create'),
            'createText' => 'Create Section',
            'editRoute' => 'sections.edit',
            'destroyRoute' => 'sections.destroy',
            'bulkDeleteRoute' => route('sections.bulk-delete'),
            'searchPlaceholder' => 'Search sections...',
        ];

        return view('sections.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Section::create($validated);

        return redirect()->route('sections.index')
            ->with('success', 'Section created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        return view('sections.edit', compact('section'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $section->update($validated);

        return redirect()->route('sections.index')
            ->with('success', 'Section updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('sections.index')
            ->with('success', 'Section deleted successfully.');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('sections.index')
                ->with('error', 'No sections selected.');
        }

        Section::whereIn('id', $ids)->delete();

        return redirect()->route('sections.index')
            ->with('success', count($ids) . ' section(s) deleted successfully.');
    }
}
