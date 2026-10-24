@extends('layouts.app')

@section('content')
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

            <!-- Password with Eye Toggle -->
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

        <!-- Continue with Google Button -->
        <form method="POST" action="{{ route('auth.google') }}" id="googleLoginForm">
            @csrf
            <input type="hidden" name="flow" value="login">
            <input type="hidden" name="google_email" id="googleEmailInput" value="">
            <button type="button" onclick="openGoogleModal()"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-bold text-xs shadow-sm flex items-center justify-center gap-2.5 transition-colors">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                Continue with Google
            </button>
        </form>

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

<!-- Clean Google Sign-In Modal -->
<div id="googleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <h3 class="text-sm font-bold text-slate-900">Sign in with Google</h3>
        </div>
        <p class="text-xs text-slate-600">Enter your Google email to proceed with quick authentication.</p>
        <div>
            <label for="modalGoogleEmail" class="block text-xs font-bold text-slate-700 mb-1">Google Email</label>
            <input type="email" id="modalGoogleEmail" placeholder="your.name@gmail.com"
                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" onclick="closeGoogleModal()"
                class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Cancel
            </button>
            <button type="button" onclick="submitGoogleModal()"
                class="px-4 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold shadow-sm transition-colors">
                Continue
            </button>
        </div>
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

    function openGoogleModal() {
        document.getElementById('googleModal').classList.remove('hidden');
        document.getElementById('modalGoogleEmail').focus();
    }

    function closeGoogleModal() {
        document.getElementById('googleModal').classList.add('hidden');
    }

    function submitGoogleModal() {
        const email = document.getElementById('modalGoogleEmail').value.trim();
        if (email) {
            document.getElementById('googleEmailInput').value = email;
            document.getElementById('googleLoginForm').submit();
        } else {
            alert('Please enter your Google email.');
        }
    }
</script>
@endsection
