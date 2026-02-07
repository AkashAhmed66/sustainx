<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Users Management') }}
        </h2>
    </x-slot>

    <div class="p-6">
        <x-data-table 
            title="All Users" 
            :createRoute="route('users.create')"
            createText="Create User"
            :paginator="$users">
            
            <x-slot name="header">
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    User
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    Roles
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    Joined
                </th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                    Actions
                </th>
            </x-slot>

            @forelse($users as $user)
            <tr class="hover:bg-neutral-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-neutral-900">{{ $user->name }}</div>
                            @if($user->id === auth()->id())
                                <span class="text-xs text-primary-600">(You)</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                    {{ $user->email }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                        @forelse($user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $role->name === 'admin' ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800' }}">
                                {{ $role->name }}
                            </span>
                        @empty
                            <span class="text-xs text-neutral-400">No roles</span>
                        @endforelse
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                    {{ $user->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('users.edit', $user) }}" 
                           class="inline-flex items-center px-3 py-1.5 bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
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
                <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-neutral-600 font-medium">No users found</p>
                        <p class="text-sm text-neutral-500 mt-1">Get started by creating a new user</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </x-data-table>
    </div>
</x-app-layout>
