<x-guest-layout>
    <div x-data="{ showRegister: {{ $errors->has('name') || $errors->has('password_confirmation') ? 'true' : 'false' }} }" class="auth-container">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-500 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-neutral-900 mb-2" x-text="showRegister ? 'Create Account' : 'Welcome Back'"></h2>
            <p class="text-neutral-600" x-text="showRegister ? 'Join us in making a sustainable impact' : 'Sign in to access your dashboard'"></p>
        </div>

        <!-- Tab Switcher -->
        <div class="flex bg-neutral-100 rounded-xl p-1 mb-8">
            <button 
                @click="showRegister = false" 
                :class="!showRegister ? 'bg-white shadow-sm text-primary-600' : 'text-neutral-600'"
                class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-300">
                Sign In
            </button>
            <button 
                @click="showRegister = true"
                :class="showRegister ? 'bg-white shadow-sm text-primary-600' : 'text-neutral-600'" 
                class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-300">
                Sign Up
            </button>
        </div>

        <!-- Forms Container -->
        <div class="relative overflow-hidden">
            <!-- Login Form -->
            <div x-show="!showRegister" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform -translate-x-full">
                
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="login_email" class="block text-sm font-medium text-neutral-700 mb-2">Email Address</label>
                        <div class="relative">
                            <input id="login_email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                                   class="auth-input pl-10" placeholder="your@email.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="login_password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                        <div class="relative">
                            <input id="login_password" type="password" name="password" required 
                                   class="auth-input pl-10" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                            <span class="ml-2 text-sm text-neutral-600">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="auth-button">
                        <span>Sign In</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div x-show="showRegister" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform -translate-x-full">
                
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Full Name</label>
                        <div class="relative">
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                                   class="auth-input pl-10" placeholder="John Doe">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="register_email" class="block text-sm font-medium text-neutral-700 mb-2">Email Address</label>
                        <div class="relative">
                            <input id="register_email" type="email" name="email" value="{{ old('email') }}" required 
                                   class="auth-input pl-10" placeholder="your@email.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required 
                                   class="auth-input pl-10" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">Confirm Password</label>
                        <div class="relative">
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
        </div>

        <!-- Footer Links -->
        <div class="mt-8 text-center">
            <p class="text-sm text-neutral-600">
                <span x-show="!showRegister">Don't have an account? 
                    <button @click="showRegister = true" class="text-primary-600 hover:text-primary-700 font-medium">Sign up</button>
                </span>
                <span x-show="showRegister">Already have an account? 
                    <button @click="showRegister = false" class="text-primary-600 hover:text-primary-700 font-medium">Sign in</button>
                </span>
            </p>
        </div>
    </div>
</x-guest-layout>
