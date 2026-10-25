<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>{{ $title ?? 'Admin Command Center - Findjobph' }}</title>

    <!-- Google Fonts & Chart.js -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
        }
        svg {
            max-width: 100%;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-100/80 text-slate-900 antialiased flex flex-col selection:bg-purple-600 selection:text-white">

    <!-- Mobile Top Bar (< lg) -->
    <header class="lg:hidden sticky top-0 z-40 bg-slate-950 text-white border-b border-slate-800 px-4 py-3 flex items-center justify-between shadow-md">
        <div class="flex items-center space-x-3">
            <button type="button" onclick="toggleAdminSidebar()" class="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-purple-600 to-sky-500 flex items-center justify-center text-white font-black text-sm shadow">
                    FJ
                </div>
                <span class="font-black text-base tracking-tight text-white">Findjob<span class="text-sky-400">ph</span></span>
                <span class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded bg-purple-500/30 text-purple-300 border border-purple-400/40">Admin</span>
            </a>
        </div>
        <div class="text-xs font-bold text-slate-400">
            {{ auth()->user()->first_name ?? 'Admin' }}
        </div>
    </header>

    <!-- Backdrop for mobile drawer -->
    <div id="adminSidebarBackdrop" onclick="toggleAdminSidebar(false)"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

    <div class="flex-1 flex min-h-screen">

        <!-- ==================== LEFT ADMIN SIDEBAR NAVBAR ==================== -->
        <aside id="adminSidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 text-white border-r border-slate-800 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
            
            <!-- Top Sidebar Content -->
            <div class="flex flex-col flex-1 overflow-y-auto">
                
                <!-- Brand Header -->
                <div class="h-20 px-6 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-sky-500 flex items-center justify-center text-white shadow-lg shadow-purple-500/25 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-lg font-black tracking-tight text-white block leading-none">
                                Findjob<span class="text-sky-400">ph</span>
                            </span>
                            <span class="text-[10px] font-black uppercase tracking-wider text-purple-400 block mt-1">
                                Admin Portal
                            </span>
                        </div>
                    </a>

                    <!-- Close button for mobile -->
                    <button type="button" onclick="toggleAdminSidebar(false)" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Navigation Links List -->
                <nav class="p-4 space-y-6">
                    
                    <!-- Main Admin Section -->
                    <div>
                        <p class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">
                            Administration
                        </p>
                        <div class="space-y-1">
                            <!-- Dashboard -->
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-purple-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <!-- Manage Users -->
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-purple-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>User Accounts</span>
                            </a>

                            <!-- Manage Posts -->
                            <a href="{{ route('admin.posts.index') }}"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.posts.*') ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.posts.*') ? 'text-white' : 'text-purple-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span>Manage Job Posts</span>
                            </a>

                            <!-- Messages -->
                            <a href="{{ route('messages.index') }}"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('messages.*') ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('messages.*') ? 'text-white' : 'text-purple-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span>Direct Messages</span>
                            </a>
                        </div>
                    </div>

                    <!-- Shortcuts / Public Portal -->
                    <div>
                        <p class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">
                            Quick Shortcuts
                        </p>
                        <div class="space-y-1">
                            <a href="{{ route('jobs.index') }}" target="_blank"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-900 hover:text-white transition-colors">
                                <span class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>Browse Jobs Portal</span>
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>

                            <a href="{{ route('jobs.map') }}" target="_blank"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-900 hover:text-white transition-colors">
                                <span class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Philippine Map</span>
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>
                        </div>
                    </div>

                </nav>
            </div>

            <!-- Bottom Profile & Logout Footer -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/80 space-y-3">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-600 text-white font-black flex items-center justify-center text-sm shadow">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">
                            {{ auth()->user()->display_name }}
                        </p>
                        <p class="text-[10px] text-purple-300 font-semibold truncate">
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
                        confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                    })"
                    class="w-full py-2 px-3 rounded-xl border border-slate-800 bg-slate-900 hover:bg-red-950/50 hover:border-red-900/60 text-slate-300 hover:text-red-400 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Sign Out</span>
                </button>
            </div>

        </aside>

        <!-- ==================== MAIN CONTENT AREA (Offset by sidebar on lg) ==================== -->
        <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
            
            <main class="flex-grow p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
                
                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-950 flex items-center gap-3 text-xs sm:text-sm font-bold shadow-sm">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                            ✓
                        </div>
                        <div class="flex-1">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-950 flex items-center gap-3 text-xs sm:text-sm font-bold shadow-sm">
                        <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center flex-shrink-0">
                            !
                        </div>
                        <div class="flex-1">{{ session('error') }}</div>
                    </div>
                @endif

                @yield('content')

            </main>

            <!-- Admin Footer -->
            <footer class="border-t border-slate-200/80 bg-white/60 py-4 px-6 text-center text-xs font-semibold text-slate-500">
                Findjobph Admin Command Center &copy; {{ date('Y') }}. High-security administrator portal.
            </footer>
        </div>

    </div>

    <!-- Universal Confirmation Modal -->
    <div id="universalConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs hidden transition-all">
        <div class="bg-white rounded-3xl border border-white/90 shadow-2xl max-w-md w-full p-6 sm:p-7 transform transition-all animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 id="confirmModalTitle" class="text-lg font-extrabold text-slate-950">
                        Confirm Action
                    </h3>
                    <p id="confirmModalMessage" class="text-sm font-semibold text-slate-800 mt-1 leading-relaxed">
                        Are you sure you want to proceed?
                    </p>
                </div>
            </div>

            <!-- Form & Actions -->
            <form id="confirmModalForm" method="POST" action="" class="mt-6 flex items-center justify-end gap-3">
                @csrf
                <input type="hidden" name="_method" id="confirmModalMethod" value="POST">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 text-sm font-bold shadow-sm transition-colors">
                    Cancel
                </button>
                <button type="submit" id="confirmModalSubmitBtn" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-md shadow-red-500/20 transition-all">
                    Confirm
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleAdminSidebar(show) {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            if (typeof show === 'boolean') {
                if (show) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                }
            } else {
                sidebar.classList.toggle('-translate-x-full');
                backdrop.classList.toggle('hidden');
            }
        }

        function confirmAction({ title, message, formAction, method = 'POST', confirmText = 'Confirm', confirmClass = 'bg-red-600 hover:bg-red-700 text-white' }) {
            const modal = document.getElementById('universalConfirmModal');
            const titleEl = document.getElementById('confirmModalTitle');
            const msgEl = document.getElementById('confirmModalMessage');
            const formEl = document.getElementById('confirmModalForm');
            const methodEl = document.getElementById('confirmModalMethod');
            const submitBtn = document.getElementById('confirmModalSubmitBtn');

            titleEl.textContent = title || 'Confirm Action';
            msgEl.textContent = message || 'Are you sure you want to continue?';
            formEl.action = formAction;
            methodEl.value = method;

            submitBtn.textContent = confirmText;
            submitBtn.className = `px-5 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all ${confirmClass}`;

            modal.classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('universalConfirmModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirmModal();
                toggleAdminSidebar(false);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
