<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('roles', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Map 'user' to 'name' for database column
        $dbSortField = $sortField === 'user' ? 'name' : $sortField;
        $query->orderBy($dbSortField, $sortDirection);
        
        // Pagination
        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);
        
        $columns = [
            'user' => 'User',
            'email' => 'Email',
            'roles' => 'Roles',
            'created_at' => 'Joined',
            'actions' => 'Actions',
        ];
        
        $bulkEnabled = false;
        
        // Table configuration
        $config = [
            'pageHeader' => 'Users Management',
            'tableTitle' => 'All Users',
            'createRoute' => route('users.create'),
            'createText' => 'Add User',
            'editRoute' => 'users.edit',
            'destroyRoute' => 'users.destroy',
            'bulkDeleteRoute' => route('users.bulk-delete'),
            'searchPlaceholder' => 'Search users by name, email or role...',
        ];
        
        return view('users.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles($request->roles ?? []);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // use Auth::id() instead of auth()->id() to avoid issues in certain contexts

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
    
    /**
     * Bulk delete users.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids, true);
        
        if (empty($ids)) {
            return redirect()->route('users.index')
                ->with('error', 'No users selected.');
        }
        
        // Prevent deletion of current user
        if (in_array(Auth::id(), $ids)) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete yourself.');
        }
        
        User::whereIn('id', $ids)->delete();
        
        return redirect()->route('users.index')
            ->with('success', count($ids) . ' user(s) deleted successfully.');
    }
}
