<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('permissions', function($q) use ($search) {
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
            'name' => 'Role Name',
            'permissions' => 'Permissions',
            'created_at' => 'Created At',
            'actions' => 'Actions',
        ];
        
        $bulkEnabled = true;
        
        // Table configuration
        $config = [
            'pageHeader' => 'Roles Management',
            'tableTitle' => 'All Roles',
            'createRoute' => route('roles.create'),
            'createText' => 'Create Role',
            'editRoute' => 'roles.edit',
            'destroyRoute' => 'roles.destroy',
            'bulkDeleteRoute' => route('roles.bulk-delete'),
            'searchPlaceholder' => 'Search roles...',
        ];
        
        return view('roles.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions(array_map('intval', $validated['permissions']));
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions(array_map('intval', $request->permissions ?? []));

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete admin role.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
    
    /**
     * Bulk delete roles.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids, true);
        
        if (empty($ids)) {
            return redirect()->route('roles.index')
                ->with('error', 'No roles selected.');
        }
        
        // Prevent deletion of admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && in_array($adminRole->id, $ids)) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete admin role.');
        }
        
        Role::whereIn('id', $ids)->delete();
        
        return redirect()->route('roles.index')
            ->with('success', count($ids) . ' role(s) deleted successfully.');
    }
}
