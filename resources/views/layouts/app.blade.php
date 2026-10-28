<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-black overflow-x-hidden dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>{{ $title ?? 'Findjobph - Philippine Job Map & Direct Hiring' }}</title>

    <!-- Circular Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Google Fonts & Leaflet CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #f4f4f5;
        }
        svg {
            max-width: 100%;
        }
        .leaflet-container {
            font-family: inherit;
            background-color: #09090b;
        }
        .leaflet-popup-content-wrapper, .leaflet-popup-tip {
            background: #141418 !important;
            color: #ffffff !important;
            border: 1px solid #27272a;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.7);
        }
        .leaflet-popup-content {
            margin: 12px 14px;
            line-height: 1.4;
        }
        /* Pulse dot for map marker */
        .pulse-marker {
            width: 14px;
            height: 14px;
            background: #ffffff;
            border-radius: 50%;
            box-shadow: 0 0 0 rgba(255, 255, 255, 0.4);
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
            70% { box-shadow: 0 0 0 14px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-[#09090b] text-zinc-100 antialiased relative selection:bg-white selection:text-black overflow-x-hidden">

    <!-- Ambient Dark Subtle Background Glows -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-zinc-800/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 -right-32 w-96 h-96 bg-zinc-900/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/3 w-[500px] h-[500px] bg-zinc-800/15 rounded-full blur-3xl"></div>
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
                        <div class="mb-5 p-4 rounded-2xl glass-card border border-emerald-500/30 bg-emerald-950/60 text-emerald-200 flex items-center gap-3 shadow-lg">
                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <div class="flex-1 text-xs font-bold">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-5 p-4 rounded-2xl glass-card border border-red-500/30 bg-red-950/60 text-red-200 flex items-center gap-3 shadow-lg">
                            <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            <div class="flex-1 text-xs font-bold">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="mb-5 p-4 rounded-2xl glass-card border border-zinc-700 bg-zinc-900/80 text-zinc-200 flex items-center gap-3 shadow-lg">
                            <svg class="w-5 h-5 text-zinc-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div class="flex-1 text-xs font-bold">
                                {{ session('info') }}
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-5 p-4 rounded-2xl glass-card border border-red-500/30 bg-red-950/60 text-red-200 shadow-lg">
                            <p class="text-xs font-bold mb-1 flex items-center gap-1.5 text-red-300">
                                Please review the following errors:
                            </p>
                            <ul class="list-disc pl-5 text-xs font-semibold space-y-0.5 text-red-200">
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
            <footer class="bg-black border-t border-zinc-850 py-4 mt-auto z-10 text-zinc-400">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold">
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-white">Findjob<span class="text-zinc-500">ph</span></span>
                        <span class="text-zinc-500 font-bold">• Admin Mode</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Admin Dashboard</a>
                        <a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">Jobs Portal</a>
                        <a href="{{ route('jobs.map') }}" class="hover:text-white transition-colors">Map View</a>
                    </div>
                    <p class="text-zinc-500">
                        &copy; {{ date('Y') }} Findjobph Admin.
                    </p>
                </div>
            </footer>
        </div>

    @else
        <!-- ==================== REGULAR USER TOP NAVIGATION (Worker / Employer / Guest) ==================== -->
        <header class="sticky top-0 z-40 bg-black/90 backdrop-blur-md border-b border-zinc-800 shadow-md w-full overflow-x-hidden text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-20">
                    
                    <!-- Logo & Brand (Matches Favicon Logo) -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                            <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-10 h-10 rounded-full shadow-md group-hover:scale-105 transition-transform shrink-0">
                            <span class="text-xl sm:text-2xl font-black tracking-tight text-white">
                                Findjob<span class="text-zinc-400">ph</span>
                            </span>
                        </a>

                        <!-- Navigation Links -->
                        <nav class="hidden md:flex items-center space-x-1 pl-6">
                            <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('home') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                Home
                            </a>
                            <a href="{{ route('jobs.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('jobs.index') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                Jobs
                            </a>
                            <a href="{{ route('jobs.map') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5 transition-colors {{ request()->routeIs('jobs.map') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Philippine Map
                            </a>

                            @auth
                                @if(auth()->user()->isWorker())
                                    <a href="{{ route('worker.applications') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('worker.applications') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                        Applications
                                    </a>
                                @endif

                                @if(auth()->user()->isEmployer())
                                    <a href="{{ route('employer.dashboard') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('employer.dashboard') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('employer.posts.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('employer.posts.*') && !request()->routeIs('employer.posts.create') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                        My Posts
                                    </a>
                                    <a href="{{ route('employer.scanner') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5 transition-colors {{ request()->routeIs('employer.scanner') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                        Scan QR
                                    </a>
                                @endif

                                <a href="{{ route('messages.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5 transition-colors {{ request()->routeIs('messages.*') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                    <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Messages
                                </a>

                                <a href="{{ route('profile.show') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5 transition-colors {{ request()->routeIs('profile.*') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    Profile
                                </a>
                            @endauth
                        </nav>
                    </div>

                    <!-- Right Side Actions & Auth Controls -->
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        @auth
                            @if(auth()->user()->isEmployer())
                                <a href="{{ route('employer.scanner') }}"
                                    class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-850 text-zinc-200 text-xs font-bold transition-colors"
                                    title="Open Worker QR Scanner">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                    Scan QR
                                </a>
                            @endif
                            <!-- User Profile Badge & Logout -->
                            <div class="flex items-center space-x-2 pl-2 border-l border-zinc-800">
                                <a href="{{ route('profile.show') }}" class="text-right hidden sm:block group" title="View Profile & Settings">
                                    <p class="text-xs font-bold text-white group-hover:text-zinc-200 leading-tight transition-colors">
                                        {{ auth()->user()->display_name }}
                                    </p>
                                    <span class="text-[10px] font-bold text-zinc-400 group-hover:text-zinc-300 uppercase tracking-wider transition-colors">
                                        {{ auth()->user()->role }}
                                    </span>
                                </a>

                                <button type="button" 
                                    onclick="confirmAction({
                                        title: 'Sign Out',
                                        message: 'Are you sure you want to sign out?',
                                        formAction: '{{ route('logout') }}',
                                        confirmText: 'Sign Out',
                                        confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                    })"
                                    class="p-2 rounded-xl text-zinc-400 hover:text-red-400 hover:bg-zinc-900 transition-colors"
                                    title="Sign Out">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </div>
                        @else
                            <!-- Guest Auth Buttons: High Contrast Dark Style -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('login') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold text-zinc-300 hover:text-white hover:bg-zinc-900 border border-zinc-800 transition-colors">
                                    Sign In
                                </a>
                                <a href="{{ route('register') }}" class="px-3.5 sm:px-4 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black text-xs font-extrabold shadow-md transition-all">
                                    Sign Up
                                </a>
                            </div>
                        @endauth

                        <!-- Mobile Hamburger Button (< md screens) -->
                        <button type="button"
                            onclick="toggleMobileNav(true)"
                            class="md:hidden p-2 rounded-xl border border-zinc-800 bg-zinc-900 text-zinc-300 hover:text-white hover:bg-zinc-800 transition-colors"
                            aria-label="Open Mobile Menu">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Slide-over Backdrop & Navigation Drawer -->
        <div id="mobileNavBackdrop" onclick="toggleMobileNav(false)" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden transition-opacity md:hidden"></div>
        <aside id="mobileNavDrawer" class="fixed inset-y-0 right-0 z-50 w-full max-w-xs bg-[#121215] border-l border-zinc-850 shadow-2xl p-6 flex flex-col justify-between transform translate-x-full transition-transform duration-300 ease-in-out md:hidden overflow-y-auto">
            <div class="space-y-6">
                <!-- Mobile Drawer Header -->
                <div class="flex items-center justify-between pb-4 border-b border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-8 h-8 rounded-full shadow shrink-0">
                        <span class="text-lg font-black tracking-tight text-white">
                            Findjob<span class="text-zinc-400">ph</span>
                        </span>
                    </div>
                    <button type="button" onclick="toggleMobileNav(false)" class="p-1.5 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors" aria-label="Close Mobile Menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                @auth
                    <!-- Authenticated User Profile Summary Pill -->
                    <a href="{{ route('profile.show') }}" onclick="toggleMobileNav(false)" class="block p-3.5 rounded-2xl bg-zinc-900/90 border border-zinc-800 hover:border-zinc-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-sm font-black text-white shrink-0">
                                {{ strtoupper(substr(auth()->user()->first_name ?: auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-white truncate">
                                    {{ auth()->user()->display_name }}
                                </p>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider mt-0.5 inline-block
                                    {{ auth()->user()->isWorker() ? 'bg-sky-950 text-sky-300 border border-sky-800' : 'bg-emerald-950 text-emerald-300 border border-emerald-800' }}">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>
                            <svg class="w-4 h-4 text-zinc-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </div>
                    </a>
                @endauth

                <!-- Navigation Links List -->
                <nav class="space-y-1 text-sm font-semibold">
                    <a href="{{ route('home') }}" onclick="toggleMobileNav(false)"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('home') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        Home
                    </a>

                    <a href="{{ route('jobs.index') }}" onclick="toggleMobileNav(false)"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('jobs.index') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Browse Jobs
                    </a>

                    <a href="{{ route('jobs.map') }}" onclick="toggleMobileNav(false)"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('jobs.map') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Philippine Map
                    </a>

                    @auth
                        @if(auth()->user()->isWorker())
                            <a href="{{ route('worker.applications') }}" onclick="toggleMobileNav(false)"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('worker.applications') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                My Applications
                            </a>

                            <a href="{{ route('worker.qr-pass') }}" onclick="toggleMobileNav(false)"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('worker.qr-pass') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                Worker Digital QR Pass
                            </a>
                        @endif

                        @if(auth()->user()->isEmployer())
                            <a href="{{ route('employer.dashboard') }}" onclick="toggleMobileNav(false)"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('employer.dashboard') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" /></svg>
                                Dashboard
                            </a>

                            <a href="{{ route('employer.posts.index') }}" onclick="toggleMobileNav(false)"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('employer.posts.*') && !request()->routeIs('employer.posts.create') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                My Posts
                            </a>

                            <a href="{{ route('employer.posts.create') }}" onclick="toggleMobileNav(false)"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('employer.posts.create') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                Post a Job
                            </a>

                            <a href="{{ route('employer.scanner') }}" onclick="toggleMobileNav(false)"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('employer.scanner') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                Scan Worker QR
                            </a>
                        @endif

                        <a href="{{ route('messages.index') }}" onclick="toggleMobileNav(false)"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('messages.*') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                            <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            Messages
                        </a>

                        <a href="{{ route('profile.show') }}" onclick="toggleMobileNav(false)"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('profile.*') ? 'bg-zinc-800 text-white font-bold border border-zinc-700' : 'text-zinc-300 hover:text-white hover:bg-zinc-900' }}">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            My Profile
                        </a>
                    @endauth
                </nav>
            </div>

            <!-- Drawer Bottom Auth Actions -->
            <div class="pt-6 border-t border-zinc-800">
                @auth
                    <button type="button"
                        onclick="toggleMobileNav(false); confirmAction({
                            title: 'Sign Out',
                            message: 'Are you sure you want to sign out?',
                            formAction: '{{ route('logout') }}',
                            confirmText: 'Sign Out',
                            confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                        })"
                        class="w-full py-2.5 px-4 rounded-xl border border-red-900/50 bg-red-950/40 text-red-300 hover:bg-red-950/80 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Sign Out
                    </button>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" onclick="toggleMobileNav(false)" class="py-2.5 px-3 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-xs text-center transition-colors">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" onclick="toggleMobileNav(false)" class="py-2.5 px-3 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs text-center shadow transition-all">
                            Sign Up
                        </a>
                    </div>
                @endauth
            </div>
        </aside>

        <!-- Main Content Container -->
        <main class="flex-grow z-10 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden">
                
                <!-- Global Flash Messages -->
                @if(session('success'))
                    <div class="mb-5 p-4 rounded-2xl glass-card border border-emerald-500/30 bg-emerald-950/60 text-emerald-200 flex items-center gap-3 shadow-lg">
                        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        <div class="flex-1 text-xs font-bold">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-4 rounded-2xl glass-card border border-red-500/30 bg-red-950/60 text-red-200 flex items-center gap-3 shadow-lg">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        <div class="flex-1 text-xs font-bold">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-5 p-4 rounded-2xl glass-card border border-zinc-700 bg-zinc-900/80 text-zinc-200 flex items-center gap-3 shadow-lg">
                        <svg class="w-5 h-5 text-zinc-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div class="flex-1 text-xs font-bold">
                            {{ session('info') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 p-4 rounded-2xl glass-card border border-red-500/30 bg-red-950/60 text-red-200 shadow-lg">
                        <p class="text-xs font-bold mb-1 flex items-center gap-1.5 text-red-300">
                            Please review the following errors:
                        </p>
                        <ul class="list-disc pl-5 text-xs font-semibold space-y-0.5 text-red-200">
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

        <!-- Clean Dark Footer -->
        <footer class="bg-black border-t border-zinc-800 py-6 mt-auto z-10 text-zinc-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold">
                <div class="flex items-center space-x-2">
                    <span class="font-extrabold text-white">Findjob<span class="text-zinc-500">ph</span></span>
                    <span class="text-zinc-500">• Philippines Job Portal</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                    <a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">Jobs</a>
                    <a href="{{ route('jobs.map') }}" class="hover:text-white transition-colors">Map View</a>
                </div>
                <p class="text-zinc-500">
                    &copy; {{ date('Y') }} Findjobph.
                </p>
            </div>
        </footer>
    @endif

    <!-- Universal Glassmorphism Confirmation Modal (Dark Mode) -->
    <div id="universalConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden transition-all">
        <div class="w-full max-w-md rounded-2xl bg-[#121215] p-6 shadow-2xl border border-zinc-800 animate-scale-up">
            <div class="flex items-start gap-4">
                <div id="confirmIconWrapper" class="w-12 h-12 rounded-xl bg-red-950/80 border border-red-800 text-red-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 id="confirmModalTitle" class="text-lg font-extrabold text-white">
                        Confirm Action
                    </h3>
                    <p id="confirmModalMessage" class="text-sm font-semibold text-zinc-300 mt-1 leading-relaxed">
                        Are you sure you want to proceed?
                    </p>
                </div>
            </div>

            <!-- Form & Actions -->
            <form id="confirmModalForm" method="POST" action="" class="mt-6 flex items-center justify-end gap-3">
                @csrf
                <input type="hidden" name="_method" id="confirmModalMethod" value="POST">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2.5 rounded-xl border border-zinc-700 bg-zinc-850 hover:bg-zinc-800 text-zinc-200 text-sm font-bold shadow-sm transition-colors">
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

        function toggleMobileNav(show) {
            const drawer = document.getElementById('mobileNavDrawer');
            const backdrop = document.getElementById('mobileNavBackdrop');
            if (!drawer || !backdrop) return;
            if (typeof show === 'boolean') {
                if (show) {
                    drawer.classList.remove('translate-x-full');
                    backdrop.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                } else {
                    drawer.classList.add('translate-x-full');
                    backdrop.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            } else {
                const isClosed = drawer.classList.contains('translate-x-full');
                toggleMobileNav(isClosed);
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
                toggleMobileNav(false);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
