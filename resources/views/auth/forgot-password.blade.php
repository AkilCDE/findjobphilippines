@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-10">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-2xl border border-white/80">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-sky-600 to-sky-400 flex items-center justify-center text-white shadow-lg shadow-sky-500/25">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">
                Reset Password
            </h1>
            <p class="text-sm font-semibold text-slate-700 mt-1">
                Enter your account email and we'll dispatch your secure password reset link.
            </p>
        </div>

        @if(session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs font-semibold">
                <p class="font-bold text-sm mb-1 text-emerald-800">✅ Reset Link Dispatched!</p>
                {{ session('status') }}

                @if(session('demo_reset_link'))
                    <div class="mt-3 pt-3 border-t border-emerald-200/80">
                        <p class="text-[11px] font-bold text-slate-800 mb-1">🔗 Direct Simulation Reset Link (Click to proceed):</p>
                        <a href="{{ session('demo_reset_link') }}" class="inline-block px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition-colors">
                            Open Password Reset Page &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1.5">
                    Account Email
                </label>
                <input id="email" type="email" name="email" value="{{ old('email', session('email')) }}" required autofocus
                    placeholder="Enter your registered email address"
                    class="w-full px-4 py-3 rounded-xl glass-input text-sm font-semibold">
            </div>

            <button type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-lg shadow-sky-500/25 transition-all hover:scale-[1.01]">
                Send Password Reset Link
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-xs font-bold text-sky-700 hover:text-sky-900 flex items-center justify-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Sign In
            </a>
        </div>

    </div>
</div>
@endsection
