<x-guest-layout>
    <div x-data="{ showRegister: true }" class="auth-container">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-500 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-neutral-900 mb-2" x-text="showRegister ? 'Create Account' : 'Welcome Back'"></h2>
            <p class="text-neutral-600" x-text="showRegister ? 'Join us in making a sustainable impact' : 'Sign in to access your dashboard'"></p>
        </div>

        <!-- Tab Switcher -->
        <div class="flex bg-neutral-100 rounded-xl p-1 mb-8">
            <button 
                @click="showRegister = false; window.location.href = '{{ route('login') }}'" 
                :class="!showRegister ? 'bg-white shadow-sm text-primary-600' : 'text-neutral-600'"
                class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-300">
                Sign In
            </button>
            <button 
                :class="showRegister ? 'bg-white shadow-sm text-primary-600' : 'text-neutral-600'" 
                class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-300">
                Sign Up
            </button>
        </div>

        <!-- Register Form -->
        <div>
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                               class="auth-input pl-10" placeholder="John Doe">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                               class="auth-input pl-10" placeholder="your@email.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required 
                               class="auth-input pl-10" placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required 
                               class="auth-input pl-10" placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Terms -->
                <div class="flex items-start">
                    <input type="checkbox" required class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500 mt-1">
                    <label class="ml-2 text-sm text-neutral-600">
                        I agree to the <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Terms of Service</a> 
                        and <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Privacy Policy</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-button">
                    <span>Create Account</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Footer Links -->
        <div class="mt-8 text-center">
            <p class="text-sm text-neutral-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">Sign in</a>
            </p>
        </div>
    </div>
</x-guest-layout>
