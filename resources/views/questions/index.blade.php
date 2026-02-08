<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __($config['pageHeader']) }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6">
        <x-data-table
            :title="$config['tableTitle']"
            :createRoute="$config['createRoute']"
            :createText="$config['createText']"
            :paginator="$items"
            :columns="$columns"
            :bulkDeleteRoute="$config['bulkDeleteRoute']"
            :bulkEnabled="$bulkEnabled"
            :searchPlaceholder="$config['searchPlaceholder']">

            @forelse($items as $item)
            <tr class="hover:bg-neutral-50 transition-colors">
                @if($bulkEnabled)
                <td class="px-6 py-4 w-12">
                    <input type="checkbox"
                           class="item-checkbox w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500"
                           value="{{ $item->id }}"
                           x-model="selectedItems">
                </td>
                @endif
                @foreach($columns as $key => $label)
                    @if($key === 'question_text')
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-neutral-900">{{ Str::limit($item->question_text, 60) }}</div>
                        </td>
                    @elseif($key === 'item')
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                            {{ $item->item->name ?? '-' }}
                        </td>
                    @elseif($key === 'question_type')
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-neutral-100 text-neutral-800">
                                {{ ucfirst($item->questionType->name ?? '-') }}
                            </span>
                        </td>
                    @elseif($key === 'unit')
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                            {{ $item->unit ?: '-' }}
                        </td>
                    @elseif($key === 'is_required')
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $item->is_required ? 'bg-orange-100 text-orange-800' : 'bg-neutral-100 text-neutral-800' }}">
                                {{ $item->is_required ? 'Required' : 'Optional' }}
                            </span>
                        </td>
                    @elseif($key === 'is_active')
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    @elseif($key === 'actions')
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route($config['editRoute'], $item) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route($config['destroyRoute'], $item) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this question?');" class="inline">
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
                            </div>
                        </td>
                    @else
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                            {{ $item->$key ?? '-' }}
                        </td>
                    @endif
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($columns) + ($bulkEnabled ? 1 : 0) }}" class="px-6 py-12 text-center text-neutral-500">
                    No questions found.
                </td>
            </tr>
            @endforelse
        </x-data-table>
    </div>
</x-app-layout>
