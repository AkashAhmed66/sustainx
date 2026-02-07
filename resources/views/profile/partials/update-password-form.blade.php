<section>
    <header>
        <h2 class="text-lg font-semibold text-neutral-800">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-2 text-sm text-neutral-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-neutral-700 mb-2">
                {{ __('Current Password') }}
            </label>
            <input type="password" 
                   id="update_password_current_password" 
                   name="current_password"
                   autocomplete="current-password"
                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('current_password', 'updatePassword') border-red-500 @enderror">
            @error('current_password', 'updatePassword')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-neutral-700 mb-2">
                {{ __('New Password') }}
            </label>
            <input type="password" 
                   id="update_password_password" 
                   name="password"
                   autocomplete="new-password"
                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('password', 'updatePassword') border-red-500 @enderror">
            @error('password', 'updatePassword')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">
                {{ __('Confirm Password') }}
            </label>
            <input type="password" 
                   id="update_password_password_confirmation" 
                   name="password_confirmation"
                   autocomplete="new-password"
                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('password_confirmation', 'updatePassword') border-red-500 @enderror">
            @error('password_confirmation', 'updatePassword')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-primary-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
