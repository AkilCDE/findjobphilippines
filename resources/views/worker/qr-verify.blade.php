@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 text-zinc-100">

    <!-- Verified Header Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-2xl relative overflow-hidden space-y-6">
        
        <!-- Ambient decorative backdrops -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-zinc-800/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-zinc-800/80 pb-5">
            <div class="flex items-center gap-3">
                <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-10 h-10 rounded-full shadow-md">
                <div>
                    <h2 class="text-sm font-black text-white tracking-wide uppercase">
                        Findjob<span class="text-zinc-500">ph</span> Verified Pass
                    </h2>
                    <p class="text-xs text-zinc-400 font-medium">Official Worker Digital Identity & Status Verification</p>
                </div>
            </div>
            
            <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider flex items-center gap-1 bg-emerald-950/80 text-emerald-300 border border-emerald-800/80">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Live Record
            </span>
        </div>

        <!-- Worker Main Profile Info -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <div class="w-20 h-20 rounded-3xl bg-zinc-800 text-white font-black text-2xl flex items-center justify-center border-2 border-zinc-700 shadow-xl shrink-0">
                {{ strtoupper(substr($qrProfile['name'] ?? 'W', 0, 2)) }}
            </div>

            <div class="flex-1 text-center sm:text-left space-y-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                    <h1 class="text-2xl font-black text-white tracking-tight">
                        {{ $qrProfile['name'] }}
                    </h1>
                    <span class="text-xs px-2.5 py-0.5 rounded-lg bg-zinc-800 text-zinc-300 border border-zinc-700 font-bold">
                        ID #{{ str_pad($qrProfile['worker_id'], 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <p class="text-xs font-medium text-zinc-400 flex flex-wrap items-center justify-center sm:justify-start gap-x-3 gap-y-1">
                    @if(!empty($qrProfile['sex']))
                        <span>👤 {{ ucfirst($qrProfile['sex']) }}</span>
                        <span>•</span>
                    @endif
                    <span>📍 {{ $qrProfile['address'] ?: 'Philippines' }}</span>
                </p>

                <p class="text-xs font-semibold text-zinc-400 flex flex-wrap items-center justify-center sm:justify-start gap-x-3 gap-y-1 pt-1">
                    <span>📞 {{ $qrProfile['contact_number'] ?: 'N/A' }}</span>
                    <span>•</span>
                    <span>✉️ {{ $qrProfile['email'] }}</span>
                </p>
            </div>
        </div>

        <!-- Live Work Status Card -->
        <div class="rounded-2xl border p-5 transition-all
            @if($qrProfile['status'] === 'hired')
                bg-emerald-950/40 border-emerald-700/80 text-emerald-100
            @elseif($qrProfile['status'] === 'fired')
                bg-red-950/40 border-red-700/80 text-red-100
            @else
                bg-amber-950/30 border-amber-700/70 text-amber-100
            @endif">
            
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">
                        @if($qrProfile['status'] === 'hired') 💼 @elseif($qrProfile['status'] === 'fired') ⚠️ @else ⏳ @endif
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">
                        Work Status
                    </span>
                </div>

                <!-- Status Badge -->
                @if($qrProfile['status'] === 'hired')
                    <span class="px-3.5 py-1 rounded-full bg-emerald-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Hired
                    </span>
                @elseif($qrProfile['status'] === 'fired')
                    <span class="px-3.5 py-1 rounded-full bg-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Fired
                    </span>
                @else
                    <span class="px-3.5 py-1 rounded-full bg-amber-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Pending
                    </span>
                @endif
            </div>

            <!-- Content Rules:
                 If hired: show work name and employer name.
                 If fired: show fired status, work name, and employer name.
                 If pending: do NOT display employer or work name.
            -->
            @if($qrProfile['status'] === 'hired')
                <div class="space-y-2 pt-2 border-t border-emerald-800/60">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="text-xs font-semibold text-emerald-300">Active Work Position:</span>
                        <span class="text-sm font-extrabold text-white">{{ $qrProfile['work_title'] }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="text-xs font-semibold text-emerald-300">Employer:</span>
                        <span class="text-sm font-extrabold text-white">🏢 {{ $qrProfile['employer_name'] }}</span>
                    </div>
                    @if(!empty($qrProfile['hired_at']))
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-emerald-400 font-medium">
                            <span>Hired Since:</span>
                            <span>{{ $qrProfile['hired_at'] }}</span>
                        </div>
                    @endif
                </div>

            @elseif($qrProfile['status'] === 'fired')
                <div class="space-y-2 pt-2 border-t border-red-800/60">
                    <div class="p-2.5 rounded-xl bg-red-950/80 border border-red-800/80 text-xs font-semibold text-red-200">
                        Worker's previous position has ended (Fired).
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="text-xs font-semibold text-red-300">Previous Work:</span>
                        <span class="text-sm font-extrabold text-white">{{ $qrProfile['work_title'] }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="text-xs font-semibold text-red-300">Former Employer:</span>
                        <span class="text-sm font-extrabold text-white">🏢 {{ $qrProfile['employer_name'] }}</span>
                    </div>
                    @if(!empty($qrProfile['ended_at']))
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-red-400 font-medium">
                            <span>Date Terminated:</span>
                            <span>{{ $qrProfile['ended_at'] }}</span>
                        </div>
                    @endif
                    @if(!empty($qrProfile['termination_reason']))
                        <div class="text-[11px] text-zinc-400 pt-1">
                            <span class="font-bold text-zinc-300">Reason:</span> {{ $qrProfile['termination_reason'] }}
                        </div>
                    @endif
                </div>

            @else
                <!-- Pending: Do not display employer or work name -->
                <div class="pt-2 border-t border-amber-800/50 space-y-1">
                    <p class="text-sm font-bold text-white">
                        Seeking Employment / Ready for Work
                    </p>
                    <p class="text-xs font-medium text-zinc-400">
                        This worker is currently available and looking for job openings.
                    </p>
                </div>
            @endif
        </div>

        <!-- Meta & Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-zinc-800 text-xs font-medium text-zinc-400">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Connected to Findjobph Central Database
            </span>

            <div class="flex items-center gap-2">
                @auth
                    @if(auth()->user()->isEmployer())
                        <a href="{{ route('messages.index', ['user_id' => $qrProfile['worker_id']]) }}"
                            class="px-4 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all">
                            Message Worker
                        </a>
                    @endif
                @endauth
                <a href="{{ route('home') }}"
                    class="px-4 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs transition-colors">
                    Back to Portal
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
