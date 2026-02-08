<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Subsection;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with('subsection.section')->withCount('questions');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('subsection', function($q) use ($search) {
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
            'name' => 'Item Name',
            'subsection' => 'Subsection',
            'description' => 'Description',
            'order_no' => 'Order',
            'questions_count' => 'Questions',
            'is_active' => 'Status',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Items Management',
            'tableTitle' => 'All Items',
            'createRoute' => route('items.create'),
            'createText' => 'Create Item',
            'editRoute' => 'items.edit',
            'destroyRoute' => 'items.destroy',
            'bulkDeleteRoute' => route('items.bulk-delete'),
            'searchPlaceholder' => 'Search items...',
        ];

        return view('items.index', compact('items', 'columns', 'bulkEnabled', 'config'));
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
            ->orderBy('order_no')
            ->get();
        return view('items.create', compact('subsections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subsection_id' => 'required|exists:subsections,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $subsections = Subsection::with('section')
            ->whereHas('section', function($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('order_no')
            ->get();
        return view('items.edit', compact('item', 'subsections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'subsection_id' => 'required|exists:subsections,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Item deleted successfully.');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('items.index')
                ->with('error', 'No items selected.');
        }

        Item::whereIn('id', $ids)->delete();

        return redirect()->route('items.index')
            ->with('success', count($ids) . ' item(s) deleted successfully.');
    }
}
