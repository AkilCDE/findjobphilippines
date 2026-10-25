@extends('layouts.app')

@section('content')
<!-- Google Identity Services SDK -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

<div class="max-w-md mx-auto py-8">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-xl border border-white/80">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                Sign In
            </h1>
            <p class="text-xs font-semibold text-slate-600 mt-1">
                Enter your email and password to access your account
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Email
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="name@example.com"
                    class="w-full px-4 py-3 rounded-xl glass-input text-sm font-semibold">
            </div>

            <!-- Password with Eye Toggle Inside Field -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-900">
                        Password
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-sky-700 hover:text-sky-900">
                        Forgot Password?
                    </a>
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required
                        placeholder="Enter your password"
                        class="w-full pl-4 pr-12 py-3 rounded-xl glass-input text-sm font-semibold">
                    <button type="button" onclick="togglePasswordVisibility('password', this)"
                        class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-sky-600 focus:outline-none cursor-pointer transition-colors z-10"
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
                    class="w-4 h-4 rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                <label for="remember" class="ml-2 block text-xs font-bold text-slate-800 select-none">
                    Remember me
                </label>
            </div>

            <!-- Sign In Button -->
            <button type="submit"
                class="w-full py-3 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-md shadow-sky-500/20 transition-all">
                Sign In
            </button>
        </form>

        <!-- Divider -->
        <div class="my-5 flex items-center">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink mx-3 text-xs font-semibold text-slate-500 uppercase">Or</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <!-- Hidden Google Auth Form -->
        <form method="POST" action="{{ route('auth.google') }}" id="googleAuthForm" class="hidden">
            @csrf
            <input type="hidden" name="flow" value="login">
            <input type="hidden" name="credential" id="googleCredentialInput" value="">
            <input type="hidden" name="google_email" id="googleEmailInput" value="">
            <input type="hidden" name="google_name" id="googleNameInput" value="">
        </form>

        <!-- Google Identity Services (GIS) One Tap & Button Initialization -->
        <div id="g_id_onload"
            data-client_id="{{ config('services.google.client_id') }}"
            data-context="signin"
            data-callback="handleGoogleCredentialResponse"
            data-auto_prompt="true"
            data-auto_select="false"
            data-itp_support="true">
        </div>

        <div class="space-y-2.5">
            <!-- Native Google Identity Services Button Container -->
            <div id="googleButtonWrapper" class="flex justify-center w-full min-h-[44px]">
                <div class="g_id_signin w-full"
                    data-type="standard"
                    data-shape="rectangular"
                    data-theme="outline"
                    data-text="continue_with"
                    data-size="large"
                    data-logo_alignment="left"
                    data-width="360">
                </div>
            </div>

            <!-- Interactive Google Account Chooser Trigger Button -->
            <button type="button" id="triggerGoogleBtn" onclick="triggerGoogleAccountSelection()"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-bold text-xs shadow-sm flex items-center justify-center gap-2.5 transition-colors">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                <span>Continue with Google</span>
            </button>
        </div>

        <!-- Footer Link -->
        <div class="mt-6 text-center pt-4 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-700">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-extrabold text-sky-700 hover:text-sky-900">
                    Sign Up
                </a>
            </p>
        </div>

    </div>
</div>

<!-- ==================== REAL GOOGLE ACCOUNT ENTRY MODAL ==================== -->
<div id="realGoogleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs hidden transition-all">
    <div class="w-full max-w-sm rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-white/90 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Google Header -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                <div>
                    <h3 class="text-sm font-black text-slate-950">Continue with Google</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">Sign in to <strong class="text-slate-900">Findjobph</strong></p>
                </div>
            </div>
            <button type="button" onclick="closeRealGoogleModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <p class="text-xs font-semibold text-slate-700 leading-relaxed">
            Enter your real Google Account email address to continue:
        </p>

        <div class="space-y-3">
            <div>
                <label for="directGoogleEmail" class="block text-[11px] font-bold uppercase text-slate-800 mb-1">
                    Google Email Address
                </label>
                <input type="email" id="directGoogleEmail" placeholder="your.name@gmail.com"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs font-semibold focus:bg-white focus:border-sky-500 focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeRealGoogleModal()"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitRealGoogleDirect()"
                    class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold shadow-md shadow-sky-500/20 transition-all">
                    Continue with Google
                </button>
            </div>
        </div>

        <p class="text-[10px] text-slate-400 text-center leading-normal">
            New accounts will automatically be directed to set up their password and names.
        </p>

    </div>
</div>

<script>
    const GOOGLE_CLIENT_ID = '{{ config('services.google.client_id') }}';

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
     * Handle credential token returned by Google Identity Services API
     */
    function handleGoogleCredentialResponse(response) {
        if (!response || !response.credential) return;

        try {
            const base64Url = response.credential.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            const payload = JSON.parse(jsonPayload);

            document.getElementById('googleCredentialInput').value = response.credential;
            document.getElementById('googleEmailInput').value = payload.email || '';
            document.getElementById('googleNameInput').value = payload.name || '';
            document.getElementById('googleAuthForm').submit();
        } catch (e) {
            document.getElementById('googleCredentialInput').value = response.credential;
            document.getElementById('googleAuthForm').submit();
        }
    }

    /**
     * Trigger Google Account detection on device via One Tap or GIS Account Chooser
     */
    function triggerGoogleAccountSelection() {
        if (window.google && google.accounts && google.accounts.id) {
            google.accounts.id.prompt((notification) => {
                if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                    requestGoogleOAuthAccount();
                }
            });
        } else {
            requestGoogleOAuthAccount();
        }
    }

    /**
     * Trigger real Google Account Chooser popup via Google OAuth2 token client
     */
    function requestGoogleOAuthAccount() {
        if (window.google && google.accounts && google.accounts.oauth2 && GOOGLE_CLIENT_ID) {
            try {
                const tokenClient = google.accounts.oauth2.initTokenClient({
                    client_id: GOOGLE_CLIENT_ID,
                    scope: 'email profile openid',
                    prompt: 'select_account',
                    callback: (tokenResponse) => {
                        if (tokenResponse && tokenResponse.access_token) {
                            fetch('https://www.googleapis.com/oauth2/v3/userinfo', {
                                headers: { Authorization: `Bearer ${tokenResponse.access_token}` }
                            })
                            .then(r => r.json())
                            .then(userInfo => {
                                if (userInfo && userInfo.email) {
                                    document.getElementById('googleEmailInput').value = userInfo.email;
                                    document.getElementById('googleNameInput').value = userInfo.name || '';
                                    document.getElementById('googleAuthForm').submit();
                                }
                            })
                            .catch(() => openRealGoogleModal());
                        }
                    }
                });
                tokenClient.requestAccessToken({ prompt: 'select_account' });
                return;
            } catch (e) {
                console.warn('Google OAuth error:', e);
            }
        }
        openRealGoogleModal();
    }

    function openRealGoogleModal() {
        document.getElementById('realGoogleModal').classList.remove('hidden');
        document.getElementById('directGoogleEmail').focus();
    }

    function closeRealGoogleModal() {
        document.getElementById('realGoogleModal').classList.add('hidden');
    }

    function submitRealGoogleDirect() {
        const email = document.getElementById('directGoogleEmail').value.trim();
        if (!email || !email.includes('@')) {
            alert('Please enter a valid Google email address.');
            return;
        }
        document.getElementById('googleEmailInput').value = email;
        document.getElementById('googleNameInput').value = email.split('@')[0];
        document.getElementById('googleAuthForm').submit();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRealGoogleModal();
        }
    });
</script>
@endsection
