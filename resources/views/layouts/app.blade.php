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
    <body class="font-sans antialiased" x-data="{ sidebarCollapsed: false, mobileSidebarOpen: false }">
        <!-- Mobile Overlay -->
        <div x-show="mobileSidebarOpen"
             @click="mobileSidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mobile-overlay"
             style="display: none;"></div>

        <div class="min-h-screen bg-neutral-50">
            <!-- Sidebar -->
            <aside :class="{ 'collapsed': sidebarCollapsed, 'mobile-open': mobileSidebarOpen }" class="sidebar">
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
                            <!-- Mobile Menu Button -->
                            <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-neutral-100 text-neutral-700 hover:bg-neutral-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <!-- Desktop Sidebar Toggle -->
                            <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex items-center justify-center w-10 h-10 rounded-lg bg-neutral-100 text-neutral-700 hover:bg-neutral-200 transition-colors">
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
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="relative p-2 text-neutral-600 hover:bg-neutral-100 rounded-lg transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[1rem]">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </button>

                                <!-- Notifications Dropdown -->
                                <div x-show="open" @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-neutral-200 z-50"
                                     style="display: none;">
                                    
                                    <!-- Header -->
                                    <div class="px-4 py-3 border-b border-neutral-200">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-semibold text-neutral-800">Notifications</h3>
                                            @if(auth()->user()->unreadNotifications->count() > 0)
                                                <span class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded-full font-medium">
                                                    {{ auth()->user()->unreadNotifications->count() }} new
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Notifications List -->
                                    <div class="max-h-96 overflow-y-auto">
                                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                            <a href="{{ route('notifications.read', $notification->id) }}" 
                                               class="block px-4 py-3 hover:bg-neutral-50 transition border-b border-neutral-100">
                                                <div class="flex gap-3">
                                                    <div class="flex-shrink-0 text-primary-600">
                                                        @if(isset($notification->data['icon']))
                                                            @if($notification->data['icon'] === 'check')
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            @elseif($notification->data['icon'] === 'warning')
                                                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                </svg>
                                                            @elseif($notification->data['icon'] === 'info')
                                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            @elseif($notification->data['icon'] === 'user')
                                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                </svg>
                                                            @else
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                                </svg>
                                                            @endif
                                                        @else
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-neutral-900 truncate">
                                                            {{ $notification->data['title'] ?? 'Notification' }}
                                                        </p>
                                                        <p class="text-xs text-neutral-600 truncate">
                                                            {{ Str::limit($notification->data['message'] ?? '', 60) }}
                                                        </p>
                                                        <p class="text-xs text-neutral-500 mt-1">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="px-4 py-8 text-center text-neutral-500">
                                                <svg class="w-12 h-12 mx-auto text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                                <p class="text-sm">No new notifications</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

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
        
        <!-- Scripts -->
        @stack('scripts')
    </body>
</html>
