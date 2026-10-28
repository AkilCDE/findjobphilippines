<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-black overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>{{ $title ?? 'Admin Command Center - Findjobph' }}</title>

    <!-- Circular Favicon (No Background) -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Google Fonts & Chart.js -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #09090b;
            color: #fafafa;
        }
        svg {
            max-width: 100%;
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
<body class="min-h-full bg-[#09090b] text-zinc-100 antialiased flex flex-col selection:bg-white selection:text-black">

    @include('layouts.partials.admin-sidebar')

    <div class="flex-1 flex min-h-screen">

        <!-- ==================== MAIN CONTENT AREA (Offset by sidebar on lg) ==================== -->
        <div id="mainContentWrapper" class="flex-1 lg:pl-64 flex flex-col min-w-0 bg-[#09090b]">
            
            <main class="flex-grow p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
                
                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-700 text-white flex items-center gap-3 text-xs sm:text-sm font-bold shadow-lg">
                        <div class="w-8 h-8 rounded-xl bg-white text-black font-black flex items-center justify-center flex-shrink-0">
                            ✓
                        </div>
                        <div class="flex-1">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-700 text-white flex items-center gap-3 text-xs sm:text-sm font-bold shadow-lg">
                        <div class="w-8 h-8 rounded-xl bg-zinc-800 text-white border border-zinc-600 font-black flex items-center justify-center flex-shrink-0">
                            !
                        </div>
                        <div class="flex-1">{{ session('error') }}</div>
                    </div>
                @endif

                @yield('content')

            </main>

            <!-- Admin Footer -->
            <footer class="border-t border-zinc-800/80 bg-black py-4 px-6 text-center text-xs font-semibold text-zinc-500">
                Findjobph Admin Command Center &copy; {{ date('Y') }}. High-security administrator portal.
            </footer>
        </div>

    </div>

    <!-- Universal Confirmation Modal (Monochrome Dark Mode) -->
    <div id="universalConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden transition-all">
        <div class="bg-[#121215] rounded-3xl border border-zinc-800 shadow-2xl max-w-md w-full p-6 sm:p-7 transform transition-all animate-in fade-in zoom-in-95 duration-150 text-white">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-zinc-700 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 id="confirmModalTitle" class="text-lg font-extrabold text-white">
                        Confirm Action
                    </h3>
                    <p id="confirmModalMessage" class="text-sm font-semibold text-zinc-400 mt-1 leading-relaxed">
                        Are you sure you want to proceed?
                    </p>
                </div>
            </div>

            <!-- Form & Actions -->
            <form id="confirmModalForm" method="POST" action="" class="mt-6 flex items-center justify-end gap-3">
                @csrf
                <input type="hidden" name="_method" id="confirmModalMethod" value="POST">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white text-sm font-bold shadow-sm transition-colors">
                    Cancel
                </button>
                <button type="submit" id="confirmModalSubmitBtn" class="px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black text-sm font-black shadow-md transition-all">
                    Confirm
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleAdminSidebar(show) {
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
        window.toggleAppSidebar = toggleAdminSidebar;

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

        function confirmAction({ title, message, formAction, method = 'POST', confirmText = 'Confirm', confirmClass = 'bg-white hover:bg-zinc-200 text-black' }) {
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
            submitBtn.className = `px-5 py-2.5 rounded-xl text-sm font-black shadow-md transition-all ${confirmClass}`;

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
