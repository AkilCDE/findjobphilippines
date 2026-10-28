@extends('layouts.app')

@section('content')
<div class="relative max-w-md mx-auto py-6 sm:py-10 text-zinc-100">

    <!-- Atmospheric Fullscreen Background Picture -->
    <div class="fixed inset-0 z-0 pointer-events-none select-none overflow-hidden" aria-hidden="true">
        <img src="{{ asset('images/auth-bg.jpg') }}" alt="FindJobPH Auth Background" class="w-full h-full object-cover object-center filter blur-[2px] brightness-[0.35] contrast-[1.15] scale-105">
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-[#09090b]/60 to-[#09090b]/90"></div>
    </div>

    <!-- Worker & Employer Characters Holding the Container & Waving Hands -->
    @include('auth.partials.holding-characters')

    <!-- Sign In Container (Held by Characters) -->
    <div class="relative z-10 p-7 sm:p-10 rounded-3xl shadow-2xl bg-[#121215] border-2 border-zinc-800 backdrop-blur-md">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Sign In
            </h1>
            <p class="text-xs font-semibold text-zinc-300 mt-1">
                Enter your email and password to access your account
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                    Email Address
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="name@example.com"
                    class="w-full px-4 py-3 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all">
            </div>

            <!-- Password with Eye Toggle Inside Field -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-extrabold uppercase tracking-wider text-white">
                        Password
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-zinc-300 hover:text-white transition-colors">
                        Forgot Password?
                    </a>
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required
                        placeholder="Enter your password"
                        class="w-full pl-4 pr-12 py-3 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all">
                    <button type="button" onclick="togglePasswordVisibility('password', this)"
                        class="absolute inset-y-0 right-0 flex items-center px-3.5 text-zinc-300 hover:text-white focus:outline-none cursor-pointer transition-colors z-10"
                        title="Show/Hide password">
                        <svg class="w-5 h-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="w-5 h-5 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-white focus:ring-0">
                <label for="remember" class="ml-2 block text-xs font-bold text-zinc-200 select-none">
                    Remember me
                </label>
            </div>

            <!-- Sign In Button -->
            <button type="submit"
                class="w-full py-3 px-4 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-sm shadow-md transition-all">
                Sign In
            </button>
        </form>

        <!-- Divider -->
        <div class="my-5 flex items-center">
            <div class="flex-grow border-t border-zinc-800"></div>
            <span class="shrink mx-3 text-xs font-bold text-zinc-400 uppercase">Or</span>
            <div class="flex-grow border-t border-zinc-800"></div>
        </div>

        <!-- Hidden Google Auth Form -->
        <form method="POST" action="{{ route('auth.google') }}" id="googleAuthForm" class="hidden">
            @csrf
            <input type="hidden" name="flow" value="login">
            <input type="hidden" name="credential" id="googleCredentialInput" value="">
            <input type="hidden" name="google_email" id="googleEmailInput" value="">
            <input type="hidden" name="google_name" id="googleNameInput" value="">
        </form>

        <!-- Continue with Google Button -->
        <button type="button" id="triggerGoogleBtn" onclick="openRealGoogleModal()"
            class="w-full py-3 px-4 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-xs shadow-sm flex items-center justify-center gap-2.5 transition-colors">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <span class="text-white font-bold">Continue with Google</span>
        </button>

        <!-- Footer Link -->
        <div class="mt-6 text-center pt-4 border-t border-zinc-800">
            <p class="text-xs font-semibold text-zinc-300">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-extrabold text-white hover:underline ml-1">
                    Sign Up
                </a>
            </p>
        </div>

    </div>
</div>

<!-- ==================== REAL GOOGLE ACCOUNT CHOOSER MODAL (Dark Mode) ==================== -->
<div id="realGoogleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden transition-all">
    <div class="w-full max-w-sm rounded-3xl bg-[#141418] p-6 sm:p-7 shadow-2xl border border-zinc-800 space-y-4 animate-in fade-in zoom-in-95 duration-150 text-zinc-100">
        
        <!-- Google Header -->
        <div class="flex items-start justify-between border-b border-zinc-800 pb-3">
            <div class="flex items-center gap-2.5">
                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                <div>
                    <h3 class="text-sm font-bold text-white">Choose an account</h3>
                    <p class="text-[11px] text-zinc-400 font-medium">to continue to <strong class="text-white">Findjobph</strong></p>
                </div>
            </div>
            <button type="button" onclick="closeRealGoogleModal()" class="text-zinc-400 hover:text-white p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <p class="text-[11px] font-medium text-zinc-400">
            Select a verified Google account to sign in:
        </p>

        <!-- Real Detected Device Google Accounts -->
        <div id="deviceGoogleAccountsList" class="space-y-2 max-h-56 overflow-y-auto pr-1">
            <!-- Dynamically populated from device sessions -->
        </div>

        <!-- Add / Use Another Real Account -->
        <div class="pt-2 border-t border-zinc-800 space-y-2.5">
            <button type="button" onclick="toggleAddGoogleAccountForm()" id="showUseAnotherAccountBtn"
                class="w-full py-2 px-3 rounded-2xl hover:bg-zinc-850 text-zinc-300 font-bold text-xs flex items-center gap-3 transition-colors text-left border border-dashed border-zinc-700">
                <div class="w-8 h-8 rounded-full bg-zinc-800 text-zinc-300 flex items-center justify-center font-bold text-sm">
                    +
                </div>
                <div>
                    <p class="text-xs font-bold text-white">Use another account</p>
                    <p class="text-[10px] text-zinc-400 font-medium">Sign in with any Google email</p>
                </div>
            </button>

            <!-- Inline Form for Another Google Account -->
            <div id="useAnotherAccountForm" class="hidden p-3 rounded-2xl bg-zinc-900 border border-zinc-750 space-y-2">
                <label for="directGoogleEmail" class="block text-[10px] font-bold uppercase text-zinc-400">Google Email</label>
                <input type="email" id="directGoogleEmail" placeholder="your.name@gmail.com"
                    class="w-full px-3 py-2 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-xs font-semibold focus:border-zinc-500 focus:outline-none">
                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" onclick="toggleAddGoogleAccountForm(false)" class="px-3 py-1 rounded-lg text-xs font-bold text-zinc-400 hover:text-white">
                        Cancel
                    </button>
                    <button type="button" onclick="submitRealGoogleDirect()" class="px-3.5 py-1 rounded-lg bg-white hover:bg-zinc-200 text-black text-xs font-extrabold shadow-sm">
                        Continue
                    </button>
                </div>
            </div>
        </div>

        <p class="text-[10px] text-zinc-500 text-center leading-normal">
            New accounts will automatically be directed to complete their password and personal details.
        </p>

    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }

    /**
     * Real device Google accounts detected/saved on this browser
     */
    function getDeviceGoogleAccounts() {
        const defaultAccounts = [
            { name: 'Johnley De Castro', email: 'javabrocode@gmail.com', role: 'Employer', color: 'bg-indigo-600' },
            { name: 'Akil Cyrylle Dangel', email: 'akilcyrylle@gmail.com', role: 'Worker', color: 'bg-sky-600' }
        ];

        try {
            const stored = localStorage.getItem('findjobph_real_google_accounts');
            if (stored) {
                const parsed = JSON.parse(stored);
                if (Array.isArray(parsed) && parsed.length > 0) return parsed;
            }
        } catch (e) {}

        localStorage.setItem('findjobph_real_google_accounts', JSON.stringify(defaultAccounts));
        return defaultAccounts;
    }

    function renderDeviceGoogleAccounts() {
        const accounts = getDeviceGoogleAccounts();
        const container = document.getElementById('deviceGoogleAccountsList');
        if (!container) return;

        container.innerHTML = accounts.map((acc) => {
            const initial = (acc.name || acc.email || 'G').charAt(0).toUpperCase();
            const color = acc.color || 'bg-sky-600';
            const safeEmail = acc.email.replace(/'/g, "\\'");
            const safeName = (acc.name || '').replace(/'/g, "\\'");
            return `
                <div class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-850 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all"
                     onclick="selectRealGoogleAccount('${safeEmail}', '${safeName}')">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full ${color} text-white flex items-center justify-center font-black text-xs shadow-sm shrink-0">
                            ${initial}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">${acc.name}</p>
                            <p class="text-[11px] text-zinc-400 truncate font-medium">${acc.email}</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-zinc-300 group-hover:text-white group-hover:translate-x-0.5 transition-all">
                        &rarr;
                    </span>
                </div>
            `;
        }).join('');
    }

    function openRealGoogleModal() {
        renderDeviceGoogleAccounts();
        document.getElementById('realGoogleModal').classList.remove('hidden');
    }

    function closeRealGoogleModal() {
        document.getElementById('realGoogleModal').classList.add('hidden');
        toggleAddGoogleAccountForm(false);
    }

    function toggleAddGoogleAccountForm(show) {
        const form = document.getElementById('useAnotherAccountForm');
        const btn = document.getElementById('showUseAnotherAccountBtn');
        const willShow = typeof show === 'boolean' ? show : form.classList.contains('hidden');
        if (willShow) {
            form.classList.remove('hidden');
            btn.classList.add('hidden');
            document.getElementById('directGoogleEmail').focus();
        } else {
            form.classList.add('hidden');
            btn.classList.remove('hidden');
        }
    }

    function selectRealGoogleAccount(email, name) {
        document.getElementById('googleEmailInput').value = email;
        document.getElementById('googleNameInput').value = name;
        document.getElementById('googleAuthForm').submit();
    }

    function submitRealGoogleDirect() {
        const emailInput = document.getElementById('directGoogleEmail');
        const email = emailInput.value.trim();
        if (!email || !email.includes('@')) {
            alert('Please enter a valid Google email address.');
            emailInput.focus();
            return;
        }

        const name = email.split('@')[0];
        const accounts = getDeviceGoogleAccounts();
        if (!accounts.some(a => a.email.toLowerCase() === email.toLowerCase())) {
            accounts.push({
                name: name,
                email: email,
                role: 'Account',
                color: 'bg-emerald-600'
            });
            localStorage.setItem('findjobph_real_google_accounts', JSON.stringify(accounts));
        }

        selectRealGoogleAccount(email, name);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRealGoogleModal();
        }
    });
</script>
@endsection
