<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Dashboard Overview') }}
        </h2>
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Stat Card 1 -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="stat-value">2,543</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">+12.5%</span>
                <span class="text-neutral-500 ml-2">from last month</span>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="stat-value">$45,231</div>
                    <div class="stat-label">Revenue</div>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">+8.2%</span>
                <span class="text-neutral-500 ml-2">from last month</span>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="stat-value">1,234</div>
                    <div class="stat-label">Active Projects</div>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">+4.3%</span>
                <span class="text-neutral-500 ml-2">from last month</span>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="stat-value">98.5%</div>
                    <div class="stat-label">Success Rate</div>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">+0.5%</span>
                <span class="text-neutral-500 ml-2">from last month</span>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2">
            <div class="dashboard-card">
                <h3 class="card-title">Recent Activity</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-4 pb-4 border-b border-neutral-200">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-neutral-800">New project created</p>
                            <p class="text-sm text-neutral-500">Project "SustainX 2.0" was created by John Doe</p>
                            <p class="text-xs text-neutral-400 mt-1">2 hours ago</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 pb-4 border-b border-neutral-200">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-neutral-800">Task completed</p>
                            <p class="text-sm text-neutral-500">Design review completed for Dashboard UI</p>
                            <p class="text-xs text-neutral-400 mt-1">5 hours ago</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 pb-4 border-b border-neutral-200">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-neutral-800">New team member</p>
                            <p class="text-sm text-neutral-500">Sarah Smith joined the development team</p>
                            <p class="text-xs text-neutral-400 mt-1">1 day ago</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-neutral-800">New comment</p>
                            <p class="text-sm text-neutral-500">Mike commented on "API Integration" task</p>
                            <p class="text-xs text-neutral-400 mt-1">2 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="lg:col-span-1">
            <div class="dashboard-card">
                <h3 class="card-title">Quick Actions</h3>
                <div class="space-y-3">
                    <button class="btn-primary w-full flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New Project
                    </button>
                    <button class="btn-secondary w-full flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Task
                    </button>
                    <button class="btn-secondary w-full flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Schedule Meeting
                    </button>
                </div>
            </div>

            <div class="dashboard-card mt-6">
                <h3 class="card-title">Team Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-neutral-600">Available</span>
                        <span class="text-sm font-semibold text-green-600">12</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-neutral-600">In Meeting</span>
                        <span class="text-sm font-semibold text-yellow-600">3</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-neutral-600">Away</span>
                        <span class="text-sm font-semibold text-neutral-600">2</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="mt-6">
        <div class="dashboard-card">
            <h3 class="card-title">Project Progress</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-neutral-700">Website Redesign</span>
                        <span class="text-sm font-semibold text-primary-600">75%</span>
                    </div>
                    <div class="w-full bg-neutral-200 rounded-full h-2">
                        <div class="bg-primary-500 h-2 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-neutral-700">Mobile App Development</span>
                        <span class="text-sm font-semibold text-primary-600">60%</span>
                    </div>
                    <div class="w-full bg-neutral-200 rounded-full h-2">
                        <div class="bg-primary-500 h-2 rounded-full" style="width: 60%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-neutral-700">API Integration</span>
                        <span class="text-sm font-semibold text-primary-600">90%</span>
                    </div>
                    <div class="w-full bg-neutral-200 rounded-full h-2">
                        <div class="bg-primary-500 h-2 rounded-full" style="width: 90%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
