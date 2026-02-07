<x-guest-layout>
    <div class="auth-container">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-500 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-neutral-900 mb-2">Forgot Password?</h2>
            <p class="text-neutral-600 max-w-sm mx-auto">
                No problem. Just let us know your email address and we'll send you a password reset link.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-6" :status="session('status')" />

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                           class="auth-input pl-10" placeholder="your@email.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <button type="submit" class="auth-button">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Email Password Reset Link</span>
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to sign in
            </a>
        </div>
    </div>
</x-guest-layout>
