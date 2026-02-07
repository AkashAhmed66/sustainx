<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permission::query();
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('guard_name', 'like', "%{$search}%");
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
            'name' => 'Permission Name',
            'guard_name' => 'Guard Name',
            'created_at' => 'Created At',
            'actions' => 'Actions',
        ];
        
        $bulkEnabled = true;
        
        // Table configuration
        $config = [
            'pageHeader' => 'Permissions Management',
            'tableTitle' => 'All Permissions',
            'createRoute' => route('permissions.create'),
            'createText' => 'Create Permission',
            'editRoute' => 'permissions.edit',
            'destroyRoute' => 'permissions.destroy',
            'bulkDeleteRoute' => route('permissions.bulk-delete'),
            'searchPlaceholder' => 'Search permissions...',
        ];
        
        return view('permissions.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions,name|max:255',
        ]);

        Permission::create($validated);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
    
    /**
     * Bulk delete permissions.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids, true);
        
        if (empty($ids)) {
            return redirect()->route('permissions.index')
                ->with('error', 'No permissions selected.');
        }
        
        Permission::whereIn('id', $ids)->delete();
        
        return redirect()->route('permissions.index')
            ->with('success', count($ids) . ' permission(s) deleted successfully.');
    }
}
