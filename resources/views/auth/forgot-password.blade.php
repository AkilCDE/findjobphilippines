@extends('layouts.app')

@section('content')
<div class="relative max-w-md mx-auto py-10 text-zinc-100">

    <!-- Atmospheric Fullscreen Background Picture -->
    <div class="fixed inset-0 z-0 pointer-events-none select-none overflow-hidden" aria-hidden="true">
        <img src="{{ asset('images/auth-bg.jpg') }}" alt="FindJobPH Auth Background" class="w-full h-full object-cover object-center filter blur-[2px] brightness-[0.35] contrast-[1.15] scale-105">
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-[#09090b]/60 to-[#09090b]/90"></div>
    </div>

    <div class="relative z-10 p-8 sm:p-10 rounded-3xl shadow-xl bg-[#121215] border border-zinc-800 backdrop-blur-md">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-white tracking-tight">
                Reset Password
            </h1>
            <p class="text-xs font-semibold text-zinc-300 mt-1">
                Enter your account email to receive a password reset link
            </p>
        </div>

        @if(session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-950/80 border border-emerald-800 text-emerald-200 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                    Email Address
                </label>
                <input id="email" type="email" name="email" value="{{ old('email', session('email')) }}" required autofocus
                    placeholder="name@example.com"
                    class="w-full px-4 py-3 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all">
            </div>

            <button type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-sm shadow-md transition-all">
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center pt-4 border-t border-zinc-800">
            <a href="{{ route('login') }}" class="text-xs font-bold text-zinc-300 hover:text-white flex items-center justify-center gap-1.5 transition-colors">
                <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Sign In
            </a>
        </div>

    </div>
</div>
@endsection
