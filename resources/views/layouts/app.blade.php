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
        /* Sleek pure dark-mode filter for OpenStreetMap tiles (100% Free, Zero API Key Required) */
        .leaflet-tile-pane {
            filter: brightness(0.65) invert(1) contrast(2.8) hue-rotate(200deg) saturate(0.25) brightness(0.75);
            transition: filter 0.3s ease;
        }
        .light-tiles .leaflet-tile-pane {
            filter: none !important;
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

        /* Sidebar hide / show transitions */
        #appSidebar, #adminSidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #mainContentWrapper {
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Desktop Sidebar Collapsed State (>= 1024px) */
        @media (min-width: 1024px) {
            body.sidebar-collapsed #appSidebar,
            body.sidebar-collapsed #adminSidebar {
                transform: translateX(-100%) !important;
            }
            body.sidebar-collapsed #mainContentWrapper {
                padding-left: 0 !important;
            }
            body.sidebar-collapsed #desktopSidebarOpenBtn {
                display: inline-flex !important;
            }
        }

        /* Mobile Sidebar Drawer Open State (< 1024px) */
        @media (max-width: 1023px) {
            body.sidebar-mobile-open #appSidebar,
            body.sidebar-mobile-open #adminSidebar {
                transform: translateX(0) !important;
            }
            body.sidebar-mobile-open #adminSidebarBackdrop,
            body.sidebar-mobile-open #sidebarBackdrop {
                display: block !important;
            }
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

    @php
        $isBrowsingJobs = request()->routeIs('jobs.index');
    @endphp

    @if($isBrowsingJobs)
        <!-- ==================== BROWSING JOBS: NO NAVBAR (HAS IN-PAGE BACK BUTTON TO DASHBOARD) ==================== -->
        <div class="flex-1 flex flex-col min-w-0 z-10">
            <main class="flex-grow z-10 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden">
                    @include('layouts.partials.flash-messages')
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>

            <!-- Minimal Footer for Browsing Page -->
            <footer class="bg-black border-t border-zinc-850 py-4 mt-auto z-10 text-zinc-400">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold">
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-white">Findjob<span class="text-zinc-500">ph</span></span>
                        <span class="text-zinc-500">• Philippines Job Portal</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                        <a href="{{ route('jobs.map') }}" class="hover:text-white transition-colors">Map View</a>
                    </div>
                    <p class="text-zinc-500">
                        &copy; {{ date('Y') }} Findjobph.
                    </p>
                </div>
            </footer>
        </div>

    @elseif(auth()->check())
        <!-- ==================== AUTHENTICATED WORKER, EMPLOYER, ADMIN: LEFT SIDEBAR NAVBAR ==================== -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area with Desktop Left Margin & Sticky Header with Icon-Only Toggle Button -->
        <div id="mainContentWrapper" class="flex-1 lg:pl-64 flex flex-col min-w-0 z-10 transition-all duration-300 ease-in-out">
            
            <!-- Sticky Top Header with Icon-Only Button to Hide/Show Navbar -->
            <header class="sticky top-0 z-40 bg-black/90 backdrop-blur-md border-b border-zinc-800 px-4 sm:px-6 py-3 flex items-center justify-between shadow-md">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger Button (< lg screens only, removed on desktop) -->
                    <button type="button"
                        onclick="toggleAppSidebar()"
                        class="lg:hidden p-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white border border-zinc-800 transition-colors focus:outline-none"
                        title="Toggle Navigation Menu"
                        aria-label="Toggle Navigation Menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Desktop Show Navbar Button (Only visible on desktop when sidebar is hidden/collapsed) -->
                    <button type="button"
                        id="desktopSidebarOpenBtn"
                        onclick="toggleAppSidebar(true)"
                        class="hidden p-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white border border-zinc-800 transition-colors focus:outline-none"
                        title="Show Sidebar Navbar"
                        aria-label="Show Sidebar Navbar">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>

                    <a href="{{ auth()->user()->isEmployer() ? route('employer.dashboard') : (auth()->user()->isWorker() ? route('worker.applications') : route('admin.dashboard')) }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-7 h-7 rounded-full shadow-sm group-hover:scale-105 transition-transform shrink-0">
                        <span class="font-black text-sm tracking-tight text-white hidden sm:inline">Findjob<span class="text-zinc-400">ph</span></span>
                        <span class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded bg-zinc-900 text-zinc-300 border border-zinc-750">
                            {{ auth()->user()->role }}
                        </span>
                    </a>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    @if(auth()->user()->isEmployer())
                        <a href="{{ route('employer.scanner') }}"
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-750 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 text-xs font-bold transition-colors">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                            <span>Scan QR</span>
                        </a>
                        <a href="{{ route('employer.posts.create') }}"
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-zinc-200 text-black text-xs font-extrabold shadow-sm transition-all">
                            <span>+ Post Job</span>
                        </a>
                    @elseif(auth()->user()->isWorker())
                        <a href="{{ route('worker.qr-pass') }}"
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-750 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 text-xs font-bold transition-colors">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                            <span>My Pass</span>
                        </a>
                    @endif

                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2 pl-2 border-l border-zinc-800 text-xs font-bold text-zinc-300 hover:text-white transition-colors">
                        <span class="w-7 h-7 rounded-lg bg-zinc-800 text-white font-extrabold flex items-center justify-center text-xs border border-zinc-750">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline truncate max-w-[120px]">{{ auth()->user()->display_name }}</span>
                    </a>
                </div>
            </header>

            <main class="flex-grow z-10 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden">
                    @include('layouts.partials.flash-messages')
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>

            <footer class="bg-black border-t border-zinc-850 py-4 mt-auto z-10 text-zinc-400">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold">
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-white">Findjob<span class="text-zinc-500">ph</span></span>
                        <span class="text-zinc-500 font-bold">• {{ ucfirst(auth()->user()->role) }} Workspace</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                        <a href="{{ route('jobs.map') }}" class="hover:text-white transition-colors">Map View</a>
                        <a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">Jobs</a>
                    </div>
                    <p class="text-zinc-500">
                        &copy; {{ date('Y') }} Findjobph.
                    </p>
                </div>
            </footer>
        </div>

    @else
        <!-- ==================== GUEST USER TOP NAVIGATION ==================== -->
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
                        </nav>
                    </div>

                    <!-- Guest Auth Buttons -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold text-zinc-300 hover:text-white hover:bg-zinc-900 border border-zinc-800 transition-colors">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-3.5 sm:px-4 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black text-xs font-extrabold shadow-md transition-all">
                            Sign Up
                        </a>
                    </div>

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
                </nav>
            </div>

            <!-- Drawer Bottom Auth Actions -->
            <div class="pt-6 border-t border-zinc-800">
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" onclick="toggleMobileNav(false)" class="py-2.5 px-3 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-xs text-center transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" onclick="toggleMobileNav(false)" class="py-2.5 px-3 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs text-center shadow transition-all">
                        Sign Up
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Container -->
        <main class="flex-grow z-10 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden">
                @include('layouts.partials.flash-messages')
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

    <!-- Global Confirmation Modal Script & Sidebar Controller -->
    <script>
        function toggleAppSidebar(show) {
            const isDesktop = window.innerWidth >= 1024;

            if (isDesktop) {
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                const shouldShow = (typeof show === 'boolean') ? show : isCollapsed;

                if (shouldShow) {
                    document.body.classList.remove('sidebar-collapsed');
                    try { localStorage.setItem('sidebar_collapsed', '0'); } catch(e) {}
                } else {
                    document.body.classList.add('sidebar-collapsed');
                    try { localStorage.setItem('sidebar_collapsed', '1'); } catch(e) {}
                }
                document.body.classList.remove('sidebar-mobile-open');
                document.body.classList.remove('overflow-hidden');
            } else {
                const isOpen = document.body.classList.contains('sidebar-mobile-open');
                const shouldOpen = (typeof show === 'boolean') ? show : !isOpen;

                if (shouldOpen) {
                    document.body.classList.add('sidebar-mobile-open');
                    document.body.classList.add('overflow-hidden');
                } else {
                    document.body.classList.remove('sidebar-mobile-open');
                    document.body.classList.remove('overflow-hidden');
                }
            }
        }
        window.toggleAdminSidebar = toggleAppSidebar;

        // Restore sidebar state immediately on load
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth >= 1024) {
                try {
                    if (localStorage.getItem('sidebar_collapsed') === '1') {
                        document.body.classList.add('sidebar-collapsed');
                    }
                } catch(e) {}
            }
        });

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
