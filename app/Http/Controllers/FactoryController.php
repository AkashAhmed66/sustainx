<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use App\Models\FactoryType;
use App\Models\Country;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Factory::with(['factoryType', 'country'])->withCount('assessments');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('factoryType', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('country', function($q) use ($search) {
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
            'name' => 'Factory Name',
            'factory_type' => 'Type',
            'country' => 'Country',
            'address' => 'Address',
            'assessments_count' => 'Assessments',
            'is_active' => 'Status',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Factories Management',
            'tableTitle' => 'All Factories',
            'createRoute' => route('factories.create'),
            'createText' => 'Create Factory',
            'editRoute' => 'factories.edit',
            'destroyRoute' => 'factories.destroy',
            'bulkDeleteRoute' => route('factories.bulk-delete'),
            'searchPlaceholder' => 'Search factories...',
        ];

        return view('factories.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $factoryTypes = FactoryType::all();
        $countries = Country::orderBy('name')->get();
        return view('factories.create', compact('factoryTypes', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'factory_type_id' => 'required|exists:factory_types,id',
            'country_id' => 'required|exists:countries,id',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Factory::create($validated);

        return redirect()->route('factories.index')
            ->with('success', 'Factory created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factory $factory)
    {
        $factoryTypes = FactoryType::all();
        $countries = Country::orderBy('name')->get();
        return view('factories.edit', compact('factory', 'factoryTypes', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factory $factory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'factory_type_id' => 'required|exists:factory_types,id',
            'country_id' => 'required|exists:countries,id',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $factory->update($validated);

        return redirect()->route('factories.index')
            ->with('success', 'Factory updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factory $factory)
    {
        $factory->delete();

        return redirect()->route('factories.index')
            ->with('success', 'Factory deleted successfully.');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('factories.index')
                ->with('error', 'No factories selected.');
        }

        Factory::whereIn('id', $ids)->delete();

        return redirect()->route('factories.index')
            ->with('success', count($ids) . ' factory(ies) deleted successfully.');
    }
}
