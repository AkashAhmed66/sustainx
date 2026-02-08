<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen overflow-hidden">
            <!-- Left Side - Login Image Background (Fixed) -->
            <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative fixed-left-panel" style="background-image: url('{{ asset('images/login.jpg') }}'); background-size: cover; background-position: center;">
                <!-- Light Greenish Overlay -->
                <div class="absolute inset-0 bg-primary-500/80"></div>

                <!-- Content -->
                <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
                    <!-- Logo & Brand -->
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20">
                            <span class="text-white font-bold text-2xl">S</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">{{ config('app.name', 'SustainX') }}</h1>
                            <p class="text-sm text-white/80">Sustainable Future Platform</p>
                        </div>
                    </div>

                    <!-- Center Content -->
                    <div class="space-y-8 max-w-lg">
                        <div>
                            <h2 class="text-4xl xl:text-5xl font-bold mb-4 leading-tight">
                                Building a Sustainable Tomorrow
                            </h2>
                            <p class="text-lg text-white/90 leading-relaxed">
                                Join us in creating innovative solutions for environmental sustainability,
                                renewable energy, and a greener future for generations to come.
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center flex-shrink-0 border border-white/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg mb-1">Global Impact</h3>
                                    <p class="text-white/80 text-sm">Track and measure environmental sustainability metrics worldwide</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center flex-shrink-0 border border-white/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg mb-1">Green Energy</h3>
                                    <p class="text-white/80 text-sm">Monitor renewable energy adoption and carbon reduction</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center flex-shrink-0 border border-white/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg mb-1">Data Security</h3>
                                    <p class="text-white/80 text-sm">Enterprise-grade security for your sustainability data</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Quote -->
                    <div class="border-l-4 border-white/30 pl-6 max-w-lg">
                        <p class="text-lg italic text-white/90 mb-2">
                            "The greatest threat to our planet is the belief that someone else will save it."
                        </p>
                        <p class="text-sm text-white/70">— Robert Swan, Environmental Leader</p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Auth Form (Scrollable) -->
            <div class="w-full lg:w-1/2 xl:w-2/5 lg:ml-[50%] xl:ml-[60%] overflow-y-auto bg-neutral-50">
                <div class="flex items-center justify-center min-h-full p-4 sm:p-6 lg:p-12">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
