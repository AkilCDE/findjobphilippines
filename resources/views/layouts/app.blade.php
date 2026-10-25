<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>{{ $title ?? 'Findjobph - Philippine Job Map & Direct Hiring' }}</title>

    <!-- Google Fonts & Leaflet CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
        }
        svg {
            max-width: 100%;
        }
        .leaflet-container {
            font-family: inherit;
        }
        /* Pulse dot for map marker */
        .pulse-marker {
            width: 14px;
            height: 14px;
            background: #0284c7;
            border-radius: 50%;
            box-shadow: 0 0 0 rgba(2, 132, 199, 0.4);
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7); }
            70% { box-shadow: 0 0 0 14px rgba(2, 132, 199, 0); }
            100% { box-shadow: 0 0 0 0 rgba(2, 132, 199, 0); }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-gradient-to-br from-sky-50 via-white to-blue-50/70 text-slate-900 antialiased relative selection:bg-sky-500 selection:text-white overflow-x-hidden">

    <!-- Ambient Blurry Background Spheres for Authentic Glassmorphism -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-sky-200/50 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 -right-32 w-96 h-96 bg-blue-200/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/3 w-[500px] h-[500px] bg-sky-100/60 rounded-full blur-3xl"></div>
    </div>

    @if(auth()->check() && auth()->user()->isAdmin())
        <!-- ==================== ADMIN VIEW: PERSISTENT LEFT SIDEBAR NAVBAR ==================== -->
        @include('layouts.partials.admin-sidebar')

        <!-- Main Wrapper Offset by Left Admin Sidebar on Desktop -->
        <div class="flex-1 lg:pl-64 flex flex-col min-w-0 z-10">
            
            <!-- Main Content Container -->
            <main class="flex-grow z-10 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden">
                    
                    <!-- Global Flash Messages -->
                    @if(session('success'))
                        <div class="mb-5 p-4 rounded-2xl glass-card border border-emerald-200 bg-emerald-50/90 text-emerald-950 flex items-center gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <div class="flex-1 text-xs font-bold">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-5 p-4 rounded-2xl glass-card border border-red-200 bg-red-50/90 text-red-950 flex items-center gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            <div class="flex-1 text-xs font-bold">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="mb-5 p-4 rounded-2xl glass-card border border-sky-200 bg-sky-50/90 text-sky-950 flex items-center gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-sky-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div class="flex-1 text-xs font-bold">
                                {{ session('info') }}
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-5 p-4 rounded-2xl glass-card border border-red-200 bg-red-50/95 text-red-950 shadow-sm">
                            <p class="text-xs font-bold mb-1 flex items-center gap-1.5 text-red-800">
                                Please review the following errors:
                            </p>
                            <ul class="list-disc pl-5 text-xs font-semibold space-y-0.5">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>

            <!-- Admin Footer -->
            <footer class="bg-white/80 border-t border-slate-200 py-4 mt-auto z-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-slate-700">
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-slate-950">Findjob<span class="text-purple-600">ph</span></span>
                        <span class="text-purple-600 font-bold">• Admin Management Mode</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-purple-600">Admin Dashboard</a>
                        <a href="{{ route('jobs.index') }}" class="hover:text-purple-600">Jobs Portal</a>
                        <a href="{{ route('jobs.map') }}" class="hover:text-purple-600">Map View</a>
                    </div>
                    <p class="text-slate-500">
                        &copy; {{ date('Y') }} Findjobph Admin.
                    </p>
                </div>
            </footer>
        </div>

    @else
        <!-- ==================== REGULAR USER TOP NAVIGATION (Worker / Employer / Guest) ==================== -->
        <header class="sticky top-0 z-40 bg-white border-b border-slate-200/90 shadow-sm w-full overflow-x-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-20">
                    
                    <!-- Logo & Brand -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('jobs.index') }}" class="flex items-center space-x-2.5 group">
                            <div class="w-10 h-10 rounded-xl bg-sky-500 flex items-center justify-center text-white shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform shrink-0">
                                <svg class="w-6 h-6 shrink-0" width="24" height="24" style="width: 24px; height: 24px; max-width: 24px; max-height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-xl sm:text-2xl font-black tracking-tight text-slate-950">
                                Findjob<span class="text-sky-600">ph</span>
                            </span>
                        </a>

                        <!-- Navigation Links -->
                        <nav class="hidden md:flex items-center space-x-1 pl-6">
                            <a href="{{ route('jobs.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('jobs.index') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-800 hover:text-sky-600' }}">
                                Jobs
                            </a>
                            <a href="{{ route('jobs.map') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5 transition-colors {{ request()->routeIs('jobs.map') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-800 hover:text-sky-600' }}">
                                <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Philippine Map
                            </a>

                            @auth
                                @if(auth()->user()->isWorker())
                                    <a href="{{ route('worker.applications') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('worker.applications') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-800 hover:text-sky-600' }}">
                                        Applications
                                    </a>
                                @endif

                                @if(auth()->user()->isEmployer())
                                    <a href="{{ route('employer.dashboard') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('employer.dashboard') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-800 hover:text-sky-600' }}">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('employer.posts.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('employer.posts.*') && !request()->routeIs('employer.posts.create') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-800 hover:text-sky-600' }}">
                                        My Posts
                                    </a>
                                @endif

                                <a href="{{ route('messages.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5 transition-colors {{ request()->routeIs('messages.*') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-800 hover:text-sky-600' }}">
                                    <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Messages
                                </a>
                            @endauth
                        </nav>
                    </div>

                    <!-- Right Side Actions & Auth Controls -->
                    <div class="flex items-center space-x-3">
                        @auth
                            <!-- User Profile Badge & Logout -->
                            <div class="flex items-center space-x-2 pl-2 border-l border-slate-200">
                                <div class="text-right hidden sm:block">
                                    <p class="text-xs font-bold text-slate-900 leading-tight">
                                        {{ auth()->user()->display_name }}
                                    </p>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                        {{ auth()->user()->role }}
                                    </span>
                                </div>

                                <button type="button" 
                                    onclick="confirmAction({
                                        title: 'Sign Out',
                                        message: 'Are you sure you want to sign out?',
                                        formAction: '{{ route('logout') }}',
                                        confirmText: 'Sign Out',
                                        confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                    })"
                                    class="p-2 rounded-xl text-slate-600 hover:text-red-600 hover:bg-red-50 transition-colors"
                                    title="Sign Out">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </div>
                        @else
                            <!-- Guest Auth Buttons: Only Sign In and Sign Up -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-800 hover:text-sky-600 transition-colors">
                                    Sign In
                                </a>
                                <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold shadow-md shadow-sky-500/20 transition-all">
                                    Sign Up
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Container -->
        <main class="flex-grow z-10 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden">
                
                <!-- Global Flash Messages -->
                @if(session('success'))
                    <div class="mb-5 p-4 rounded-2xl glass-card border border-emerald-200 bg-emerald-50/90 text-emerald-950 flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        <div class="flex-1 text-xs font-bold">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-4 rounded-2xl glass-card border border-red-200 bg-red-50/90 text-red-950 flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        <div class="flex-1 text-xs font-bold">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-5 p-4 rounded-2xl glass-card border border-sky-200 bg-sky-50/90 text-sky-950 flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-sky-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div class="flex-1 text-xs font-bold">
                            {{ session('info') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 p-4 rounded-2xl glass-card border border-red-200 bg-red-50/95 text-red-950 shadow-sm">
                        <p class="text-xs font-bold mb-1 flex items-center gap-1.5 text-red-800">
                            Please review the following errors:
                        </p>
                        <ul class="list-disc pl-5 text-xs font-semibold space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>

        <!-- Clean Footer -->
        <footer class="bg-white border-t border-slate-200 py-6 mt-auto z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-slate-700">
                <div class="flex items-center space-x-2">
                    <span class="font-extrabold text-slate-950">Findjob<span class="text-sky-600">ph</span></span>
                    <span>• Philippines Job Portal</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('jobs.index') }}" class="hover:text-sky-600">Jobs</a>
                    <a href="{{ route('jobs.map') }}" class="hover:text-sky-600">Map View</a>
                </div>
                <p class="text-slate-500">
                    &copy; {{ date('Y') }} Findjobph.
                </p>
            </div>
        </footer>
    @endif

    <!-- Universal Glassmorphism Confirmation Modal (No Native Alerts) -->
    <div id="universalConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden transition-all">
        <div class="w-full max-w-md rounded-2xl glass-modal p-6 shadow-2xl border border-white/80 animate-scale-up">
            <div class="flex items-start gap-4">
                <div id="confirmIconWrapper" class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Global Confirmation Modal Script & Admin Sidebar Drawer Controller -->
    <script>
        function toggleAdminSidebar(show) {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            if (!sidebar || !backdrop) return;
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

        // Close on ESC
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
