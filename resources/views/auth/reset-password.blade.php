@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-10">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-2xl border border-white/80">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-sky-600 to-sky-400 flex items-center justify-center text-white shadow-lg shadow-sky-500/25">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">
                Create New Password
            </h1>
            <p class="text-sm font-semibold text-slate-700 mt-1">
                Enter your email and chosen new secure password.
            </p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1.5">
                    Email Address
                </label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                    placeholder="e.g. user@example.com"
                    class="w-full px-4 py-3 rounded-xl glass-input text-sm font-semibold">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1.5">
                    New Password
                </label>
                <input id="password" type="password" name="password" required minlength="8"
                    placeholder="Minimum 8 characters"
                    class="w-full px-4 py-3 rounded-xl glass-input text-sm font-semibold">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1.5">
                    Confirm New Password
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                    placeholder="Re-enter new password"
                    class="w-full px-4 py-3 rounded-xl glass-input text-sm font-semibold">
            </div>

            <button type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-lg shadow-sky-500/25 transition-all hover:scale-[1.01]">
                Update Password & Sign In
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-xs font-bold text-sky-700 hover:text-sky-900">
                Cancel & Return to Sign In
            </a>
        </div>

    </div>
</div>
@endsection
