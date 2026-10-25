<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-x-hidden">
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
            color: #0f172a;
        }
        svg {
            max-width: 100%;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-100/80 text-slate-900 antialiased flex flex-col selection:bg-purple-600 selection:text-white">

    @include('layouts.partials.admin-sidebar')

    <div class="flex-1 flex min-h-screen">

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
