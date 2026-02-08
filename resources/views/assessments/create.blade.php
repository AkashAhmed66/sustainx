<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Create Assessment') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 max-w-4xl">
        <div class="dashboard-card">
            <div class="p-6">
                <form action="{{ route('assessments.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Factory -->
                        <div class="md:col-span-2">
                            <label for="factory_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                Factory <span class="text-red-500">*</span>
                            </label>
                            <select name="factory_id"
                                    id="factory_id"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('factory_id') border-red-500 @enderror">
                                <option value="">Select Factory</option>
                                @foreach($factories as $factory)
                                    <option value="{{ $factory->id }}" {{ old('factory_id') == $factory->id ? 'selected' : '' }}>
                                        {{ $factory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('factory_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Year -->
                        <div>
                            <label for="year" class="block text-sm font-medium text-neutral-700 mb-2">
                                Year <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="year"
                                   id="year"
                                   value="{{ old('year', date('Y')) }}"
                                   required
                                   min="2000"
                                   max="2100"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('year') border-red-500 @enderror"
                                   placeholder="{{ date('Y') }}">
                            @error('year')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Period -->
                        <div>
                            <label for="period" class="block text-sm font-medium text-neutral-700 mb-2">
                                Period <span class="text-red-500">*</span>
                            </label>
                            <select name="period"
                                    id="period"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('period') border-red-500 @enderror">
                                <option value="annual" {{ old('period', 'annual') == 'annual' ? 'selected' : '' }}>Annual</option>
                                <option value="quarterly" {{ old('period') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            </select>
                            @error('period')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-medium text-neutral-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    id="status"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t">
                        <a href="{{ route('assessments.index') }}"
                           class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium">
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn-primary">
                            Create Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
