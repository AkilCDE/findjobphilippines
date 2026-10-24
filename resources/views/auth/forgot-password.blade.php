@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-10">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-xl border border-white/80">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-slate-950 tracking-tight">
                Reset Password
            </h1>
            <p class="text-xs font-semibold text-slate-600 mt-1">
                Enter your account email to receive a password reset link
            </p>
        </div>

        @if(session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Email
                </label>
                <input id="email" type="email" name="email" value="{{ old('email', session('email')) }}" required autofocus
                    placeholder="name@example.com"
                    class="w-full px-4 py-3 rounded-xl glass-input text-sm font-semibold">
            </div>

            <button type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-md shadow-sky-500/20 transition-all">
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center pt-4 border-t border-slate-100">
            <a href="{{ route('login') }}" class="text-xs font-bold text-sky-700 hover:text-sky-900 flex items-center justify-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Sign In
            </a>
        </div>

    </div>
</div>
@endsection
