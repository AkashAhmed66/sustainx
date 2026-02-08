<?php

namespace App\Http\Controllers;

use App\Models\Subsection;
use App\Models\Section;
use Illuminate\Http\Request;

class SubsectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subsection::with('section')->withCount('items');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('section', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
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
            'name' => 'Subsection Name',
            'section' => 'Section',
            'description' => 'Description',
            'order_no' => 'Order',
            'items_count' => 'Items',
            'is_active' => 'Status',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Subsections Management',
            'tableTitle' => 'All Subsections',
            'createRoute' => route('subsections.create'),
            'createText' => 'Create Subsection',
            'editRoute' => 'subsections.edit',
            'destroyRoute' => 'subsections.destroy',
            'bulkDeleteRoute' => route('subsections.bulk-delete'),
            'searchPlaceholder' => 'Search subsections...',
        ];

        return view('subsections.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::where('is_active', true)->orderBy('order_no')->get();
        return view('subsections.create', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Subsection::create($validated);

        return redirect()->route('subsections.index')
            ->with('success', 'Subsection created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subsection $subsection)
    {
        $sections = Section::where('is_active', true)->orderBy('order_no')->get();
        return view('subsections.edit', compact('subsection', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subsection $subsection)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $subsection->update($validated);

        return redirect()->route('subsections.index')
            ->with('success', 'Subsection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subsection $subsection)
    {
        $subsection->delete();

        return redirect()->route('subsections.index')
            ->with('success', 'Subsection deleted successfully.');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('subsections.index')
                ->with('error', 'No subsections selected.');
        }

        Subsection::whereIn('id', $ids)->delete();

        return redirect()->route('subsections.index')
            ->with('success', count($ids) . ' subsection(s) deleted successfully.');
    }
}
