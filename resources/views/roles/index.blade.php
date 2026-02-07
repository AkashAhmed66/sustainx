<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Roles Management') }}
        </h2>
    </x-slot>

    <div class="p-6">
        <x-data-table 
            title="All Roles" 
            :createRoute="route('roles.create')"
            createText="Create Role"
            :paginator="$roles">
            
            <x-slot name="header">
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    Permissions
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    Created At
                </th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    Actions
                </th>
            </x-slot>

            @forelse($roles as $role)
            <tr class="hover:bg-neutral-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-neutral-900">{{ $role->name }}</div>
                            <div class="text-xs text-neutral-500">{{ $role->users_count ?? 0 }} users</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                        @forelse($role->permissions->take(3) as $permission)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                {{ $permission->name }}
                            </span>
                        @empty
                            <span class="text-xs text-neutral-400">No permissions</span>
                        @endforelse
                        @if($role->permissions->count() > 3)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">
                                +{{ $role->permissions->count() - 3 }} more
                            </span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                    {{ $role->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('roles.edit', $role) }}" 
                           class="inline-flex items-center px-3 py-1.5 bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        @if($role->name !== 'admin')
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this role?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-neutral-600 font-medium">No roles found</p>
                        <p class="text-sm text-neutral-500 mt-1">Get started by creating a new role</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </x-data-table>
    </div>
</x-app-layout>
