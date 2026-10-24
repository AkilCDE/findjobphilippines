@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-10">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-xl border border-white/80">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-slate-950 tracking-tight">
                Create New Password
            </h1>
            <p class="text-xs font-semibold text-slate-600 mt-1">
                Enter your email and chosen new secure password
            </p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Email Address
                </label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                    placeholder="e.g. user@example.com"
                    class="w-full px-4 py-3 rounded-xl glass-input text-sm font-semibold">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    New Password
                </label>
                <div class="relative">
                    <input id="password" type="password" name="password" required minlength="8"
                        placeholder="Minimum 8 characters"
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

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Confirm New Password
                </label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                        placeholder="Re-enter new password"
                        class="w-full pl-4 pr-12 py-3 rounded-xl glass-input text-sm font-semibold">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
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

            <button type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-md shadow-sky-500/20 transition-all mt-2">
                Update Password & Sign In
            </button>
        </form>

        <div class="mt-6 text-center pt-4 border-t border-slate-100">
            <a href="{{ route('login') }}" class="text-xs font-bold text-sky-700 hover:text-sky-900">
                Cancel & Return to Sign In
            </a>
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
</script>
@endsection
