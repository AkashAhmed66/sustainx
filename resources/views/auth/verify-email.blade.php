<x-guest-layout>
    <div class="auth-container">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-500 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-neutral-900 mb-2">Verify Email</h2>
            <p class="text-neutral-600 max-w-md mx-auto">
                Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
            </p>
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-800">
                        A new verification link has been sent to your email address.
                    </p>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="auth-button">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Resend Verification Email</span>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-6 py-3 rounded-xl font-medium text-neutral-700 border border-neutral-300 hover:bg-neutral-50 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>

        <!-- Help Text -->
        <div class="mt-8 text-center">
            <p class="text-sm text-neutral-500">
                Didn't receive the email? Check your spam folder or 
                <button type="button" onclick="document.querySelector('form').submit()" class="text-primary-600 hover:text-primary-700 font-medium">
                    click here to resend
                </button>
            </p>
        </div>
    </div>
</x-guest-layout>
