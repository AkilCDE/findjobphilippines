@php
    $user = auth()->user();
    $role = $user?->role ?? 'guest';
    $sidebarId = ($user && $user->isAdmin()) ? 'adminSidebar' : 'appSidebar';
    $backdropId = ($user && $user->isAdmin()) ? 'adminSidebarBackdrop' : 'sidebarBackdrop';
@endphp

<!-- Backdrop for mobile drawer (< lg) -->
<div id="{{ $backdropId }}" onclick="toggleAppSidebar(false)"
    class="fixed inset-0 bg-black/80 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

<!-- ==================== LEFT SIDEBAR NAVBAR ==================== -->
<aside id="{{ $sidebarId }}"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-black text-white border-r border-zinc-850 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
    
    <!-- Top Sidebar Content -->
    <div class="flex flex-col flex-1 overflow-y-auto">
        
        <!-- Brand Header with Icon-Only Collapse Button -->
        <div class="h-20 px-5 border-b border-zinc-850 flex items-center justify-between">
            <a href="{{ $role === 'employer' ? route('employer.dashboard') : ($role === 'worker' ? route('worker.applications') : route('admin.dashboard')) }}" class="flex items-center space-x-3 group">
                <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-9 h-9 rounded-full shadow-md group-hover:scale-105 transition-transform shrink-0">
                <div>
                    <span class="text-lg font-black tracking-tight text-white block leading-none">
                        Findjob<span class="text-zinc-400">ph</span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500 block mt-1">
                        {{ ucfirst($role) }} Portal
                    </span>
                </div>
            </a>

            <!-- Icon-only Button to Hide/Collapse Navbar -->
            <button type="button"
                onclick="toggleAppSidebar(false)"
                class="p-2 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-900 border border-transparent hover:border-zinc-800 transition-colors focus:outline-none"
                title="Hide Sidebar"
                aria-label="Hide Sidebar">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Navigation Links List -->
        <nav class="p-4 space-y-6">
            
            @if($user && $user->isAdmin())
                <!-- ==================== ADMIN NAVIGATION ==================== -->
                <div>
                    <p class="px-3 text-[10px] font-black uppercase tracking-wider text-zinc-500 mb-2">
                        Administration
                    </p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>User Accounts</span>
                        </a>

                        <a href="{{ route('admin.posts.index') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.posts.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.posts.*') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Manage Job Posts</span>
                        </a>

                        <a href="{{ route('messages.index') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('messages.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('messages.*') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Direct Messages</span>
                        </a>
                    </div>
                </div>

            @elseif($user && $user->isEmployer())
                <!-- ==================== EMPLOYER NAVIGATION ==================== -->
                <div>
                    <p class="px-3 text-[10px] font-black uppercase tracking-wider text-zinc-500 mb-2">
                        Employer Workspace
                    </p>
                    <div class="space-y-1">
                        <a href="{{ route('employer.dashboard') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.dashboard') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('employer.dashboard') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('employer.posts.index') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.posts.index') || request()->routeIs('employer.posts.edit') || request()->routeIs('employer.posts.applicants') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('employer.posts.index') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>My Job Posts</span>
                        </a>

                        <a href="{{ route('employer.posts.create') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.posts.create') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('employer.posts.create') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Post a New Job</span>
                        </a>

                        <a href="{{ route('employer.scanner') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('employer.scanner') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span>Scan Worker QR</span>
                        </a>

                        <a href="{{ route('messages.index') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('messages.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('messages.*') ? 'text-black' : 'text-sky-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Direct Messages</span>
                        </a>

                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('profile.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('profile.*') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Company Profile</span>
                        </a>
                    </div>
                </div>

            @elseif($user && $user->isWorker())
                <!-- ==================== WORKER NAVIGATION ==================== -->
                <div>
                    <p class="px-3 text-[10px] font-black uppercase tracking-wider text-zinc-500 mb-2">
                        Worker Workspace
                    </p>
                    <div class="space-y-1">
                        <a href="{{ route('worker.applications') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('worker.applications') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('worker.applications') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>My Applications</span>
                        </a>

                        <a href="{{ route('worker.qr-pass') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('worker.qr-pass') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span>Digital Worker Pass</span>
                        </a>

                        <a href="{{ route('messages.index') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('messages.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('messages.*') ? 'text-black' : 'text-sky-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Direct Messages</span>
                        </a>

                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('profile.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('profile.*') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>My Profile & QR</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Discovery & Shortcuts Section (All Roles) -->
            <div>
                <p class="px-3 text-[10px] font-black uppercase tracking-wider text-zinc-500 mb-2">
                    Discovery
                </p>
                <div class="space-y-1">
                    <a href="{{ route('jobs.index') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-zinc-400 hover:bg-zinc-900 hover:text-white">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Browse Jobs</span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>

                    <a href="{{ route('jobs.map') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('jobs.map') ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Philippine Map</span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>

                    <a href="{{ route('home') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('home') ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Landing Page</span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

        </nav>
    </div>

    <!-- Bottom Profile & Logout Footer -->
    <div class="p-4 border-t border-zinc-850 bg-black space-y-3">
        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-zinc-900 transition-colors group">
            <div class="w-9 h-9 rounded-xl bg-zinc-900 text-white border border-zinc-800 font-black flex items-center justify-center text-xs shadow group-hover:border-zinc-700">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-white truncate group-hover:text-zinc-200">
                    {{ $user->display_name }}
                </p>
                <p class="text-[10px] text-zinc-500 font-semibold truncate capitalize">
                    {{ $user->role }} Account
                </p>
            </div>
        </a>

        <button type="button"
            onclick="confirmAction({
                title: 'Sign Out',
                message: 'Are you sure you want to sign out?',
                formAction: '{{ route('logout') }}',
                confirmText: 'Sign Out',
                confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
            })"
            class="w-full py-2 px-3 rounded-xl border border-zinc-800 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white font-bold text-xs flex items-center justify-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Sign Out</span>
        </button>
    </div>

</aside>

