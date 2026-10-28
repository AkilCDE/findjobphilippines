<!-- Mobile Top Bar (< lg) for Admin -->
<header class="lg:hidden sticky top-0 z-40 bg-black text-white border-b border-zinc-800 px-4 py-3 flex items-center justify-between shadow-md">
    <div class="flex items-center space-x-3">
        <button type="button" onclick="toggleAdminSidebar()" class="p-2 rounded-xl bg-zinc-900 text-zinc-300 hover:text-white hover:bg-zinc-800 border border-zinc-800 focus:outline-none">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-8 h-8 rounded-full shadow-sm shrink-0">
            <span class="font-black text-base tracking-tight text-white">Findjob<span class="text-zinc-400">ph</span></span>
            <span class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded bg-zinc-900 text-zinc-300 border border-zinc-700">Admin</span>
        </a>
    </div>
    <div class="text-xs font-bold text-zinc-400">
        {{ auth()->user()->first_name ?? 'Admin' }}
    </div>
</header>

<!-- Backdrop for mobile drawer -->
<div id="adminSidebarBackdrop" onclick="toggleAdminSidebar(false)"
    class="fixed inset-0 bg-black/80 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

<!-- ==================== LEFT ADMIN SIDEBAR NAVBAR ==================== -->
<aside id="adminSidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-black text-white border-r border-zinc-850 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
    
    <!-- Top Sidebar Content -->
    <div class="flex flex-col flex-1 overflow-y-auto">
        
        <!-- Brand Header -->
        <div class="h-20 px-6 border-b border-zinc-850 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-10 h-10 rounded-full shadow-md group-hover:scale-105 transition-transform shrink-0">
                <div>
                    <span class="text-lg font-black tracking-tight text-white block leading-none">
                        Findjob<span class="text-zinc-400">ph</span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500 block mt-1">
                        Admin Portal
                    </span>
                </div>
            </a>

            <!-- Close button for mobile -->
            <button type="button" onclick="toggleAdminSidebar(false)" class="lg:hidden text-zinc-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Navigation Links List -->
        <nav class="p-4 space-y-6">
            
            <!-- Main Admin Section -->
            <div>
                <p class="px-3 text-[10px] font-black uppercase tracking-wider text-zinc-500 mb-2">
                    Administration
                </p>
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Manage Users -->
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>User Accounts</span>
                    </a>

                    <!-- Manage Posts -->
                    <a href="{{ route('admin.posts.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.posts.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.posts.*') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Manage Job Posts</span>
                    </a>

                    <!-- Direct Messages -->
                    <a href="{{ route('messages.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('messages.*') ? 'bg-white text-black font-black shadow-sm' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('messages.*') ? 'text-black' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Direct Messages</span>
                    </a>
                </div>
            </div>

            <!-- Public View Shortcuts -->
            <div>
                <p class="px-3 text-[10px] font-black uppercase tracking-wider text-zinc-500 mb-2">
                    Job Portal & Map
                </p>
                <div class="space-y-1">
                    <a href="{{ route('jobs.index') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('jobs.index') || request()->routeIs('jobs.show') ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Browse Jobs Portal</span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>

                    <a href="{{ route('jobs.map') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('jobs.map') ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        <div class="flex items-center gap-3 px-2">
            <div class="w-10 h-10 rounded-xl bg-zinc-900 text-white border border-zinc-800 font-black flex items-center justify-center text-sm shadow">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-white truncate">
                    {{ auth()->user()->display_name }}
                </p>
                <p class="text-[10px] text-zinc-400 font-semibold truncate">
                    {{ auth()->user()->email }}
                </p>
            </div>
        </div>

        <button type="button"
            onclick="confirmAction({
                title: 'Sign Out of Admin',
                message: 'Are you sure you want to log out of the admin panel?',
                formAction: '{{ route('logout') }}',
                confirmText: 'Sign Out',
                confirmClass: 'bg-white hover:bg-zinc-200 text-black'
            })"
            class="w-full py-2 px-3 rounded-xl border border-zinc-800 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white font-bold text-xs flex items-center justify-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Sign Out</span>
        </button>
    </div>

</aside>
