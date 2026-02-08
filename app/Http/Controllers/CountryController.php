<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Country::withCount('factories');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iso_code', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);

        $columns = [
            'name' => 'Country Name',
            'iso_code' => 'ISO Code',
            'factories_count' => 'Factories',
            'created_at' => 'Created At',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Countries Management',
            'tableTitle' => 'All Countries',
            'createRoute' => route('countries.create'),
            'createText' => 'Create Country',
            'editRoute' => 'countries.edit',
            'destroyRoute' => 'countries.destroy',
            'bulkDeleteRoute' => route('countries.bulk-delete'),
            'searchPlaceholder' => 'Search countries...',
        ];

        return view('countries.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|max:3|unique:countries,iso_code',
        ]);

        Country::create($validated);

        return redirect()->route('countries.index')
            ->with('success', 'Country created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|max:3|unique:countries,iso_code,' . $country->id,
        ]);

        $country->update($validated);

        return redirect()->route('countries.index')
            ->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('countries.index')
            ->with('success', 'Country deleted successfully.');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('countries.index')
                ->with('error', 'No countries selected.');
        }

        Country::whereIn('id', $ids)->delete();

        return redirect()->route('countries.index')
            ->with('success', count($ids) . ' country(ies) deleted successfully.');
    }
}
