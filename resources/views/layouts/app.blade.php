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
    <body class="font-sans antialiased" x-data="{ sidebarCollapsed: false }">
        <div class="min-h-screen bg-neutral-50">
            <!-- Sidebar -->
            <aside :class="sidebarCollapsed ? 'collapsed' : ''" class="sidebar">
                <!-- Sidebar Header -->
                <div class="sidebar-header">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-xl">S</span>
                            </div>
                        </div>
                        <div x-show="!sidebarCollapsed" x-transition class="text-white">
                            <h1 class="font-bold text-lg">{{ config('app.name', 'SustainX') }}</h1>
                            <p class="text-xs text-white/60">Dashboard</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Navigation -->
                <nav class="sidebar-nav overflow-y-auto scrollbar-thin" style="max-height: calc(100vh - 64px);">
                    @foreach(config('navigation.sidebar') as $item)
                        @if($item['type'] === 'divider')
                            <div class="my-4 border-t border-primary-700"></div>
                        
                        @elseif($item['type'] === 'heading')
                            @if(!isset($item['permission']) || auth()->user()->can($item['permission']))
                                <div x-show="!sidebarCollapsed" class="px-4 py-2">
                                    <span class="text-xs font-semibold text-primary-300 uppercase tracking-wider">{{ $item['name'] }}</span>
                                </div>
                            @endif
                        
                        @elseif($item['type'] === 'logout')
                            @if(!isset($item['permission']) || auth()->user()->can($item['permission']))
                                <form method="POST" action="{{ route($item['route']) }}">
                                    @csrf
                                    <button type="submit" class="sidebar-link w-full text-left">
                                        <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $item['icon'] !!}
                                        </svg>
                                        <span class="sidebar-link-text">{{ $item['name'] }}</span>
                                    </button>
                                </form>
                            @endif
                        
                        @elseif($item['type'] === 'link')
                            @if(!isset($item['permission']) || auth()->user()->can($item['permission']))
                                <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}" 
                                   class="sidebar-link {{ $item['active_pattern'] && request()->routeIs($item['active_pattern']) ? 'active' : '' }}">
                                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    <span class="sidebar-link-text">{{ $item['name'] }}</span>
                                </a>
                            @endif
                        @endif
                    @endforeach
                </nav>
            </aside>

            <!-- Main Content Wrapper -->
            <div :class="sidebarCollapsed ? 'expanded' : ''" class="main-wrapper">
                <!-- Top Bar -->
                <header :class="sidebarCollapsed ? 'expanded' : ''" class="topbar">
                    <div class="h-full px-6 flex items-center justify-between">
                        <!-- Left Side -->
                        <div class="flex items-center gap-4">
                            <!-- Sidebar Toggle Button -->
                            <button @click="sidebarCollapsed = !sidebarCollapsed" class="sidebar-toggle bg-neutral-100 text-neutral-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4 6h16M4 12h16M4 18h16" />
                                    <path x-show="sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4 6h16M4 12h8m-8 6h16" />
                                </svg>
                            </button>

                            <!-- Page Title -->
                            @isset($header)
                                <div>
                                    {{ $header }}
                                </div>
                            @endisset
                        </div>

                        <!-- Right Side -->
                        <div class="flex items-center gap-4">
                            <!-- Search -->
                            <div class="hidden md:block">
                                <input type="search" placeholder="Search..." 
                                       class="px-4 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>

                            <!-- Notifications -->
                            <button class="relative p-2 text-neutral-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>

                            <!-- User Menu -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-neutral-100 rounded-lg transition-colors">
                                    <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="hidden lg:block font-medium text-sm text-neutral-700">{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown -->
                                <div x-show="open" @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-200" 
                                     x-transition:enter-start="opacity-0 scale-95" 
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 py-1 z-50">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Profile</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Account Settings</a>
                                    <hr class="my-1 border-neutral-200">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-neutral-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Content Area -->
                <main class="content-wrapper">
                    <div class="p-6">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
