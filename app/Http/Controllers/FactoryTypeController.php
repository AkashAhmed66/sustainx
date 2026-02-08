<?php

namespace App\Http\Controllers;

use App\Models\FactoryType;
use Illuminate\Http\Request;

class FactoryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FactoryType::withCount('factories');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);

        $columns = [
            'name' => 'Factory Type Name',
            'factories_count' => 'Factories',
            'created_at' => 'Created At',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Factory Types Management',
            'tableTitle' => 'All Factory Types',
            'createRoute' => route('factory-types.create'),
            'createText' => 'Create Factory Type',
            'editRoute' => 'factory-types.edit',
            'destroyRoute' => 'factory-types.destroy',
            'bulkDeleteRoute' => route('factory-types.bulk-delete'),
            'searchPlaceholder' => 'Search factory types...',
        ];

        return view('factory-types.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('factory-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:factory_types,name',
        ]);

        FactoryType::create($validated);

        return redirect()->route('factory-types.index')
            ->with('success', 'Factory type created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FactoryType $factoryType)
    {
        return view('factory-types.edit', compact('factoryType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FactoryType $factoryType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:factory_types,name,' . $factoryType->id,
        ]);

        $factoryType->update($validated);

        return redirect()->route('factory-types.index')
            ->with('success', 'Factory type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FactoryType $factoryType)
    {
        $factoryType->delete();

        return redirect()->route('factory-types.index')
            ->with('success', 'Factory type deleted successfully.');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('factory-types.index')
                ->with('error', 'No factory types selected.');
        }

        FactoryType::whereIn('id', $ids)->delete();

        return redirect()->route('factory-types.index')
            ->with('success', count($ids) . ' factory type(s) deleted successfully.');
    }
}
