@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                My Applications
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1">
                Track your active job applications and hiring status
            </p>
        </div>

        <div>
            <a href="{{ route('jobs.index') }}"
                class="px-5 py-3 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-xs shadow-lg shadow-sky-500/25 flex items-center gap-2 transition-all hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Find More Jobs
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-card p-5 rounded-3xl border border-white/80">
            <p class="text-xs font-bold uppercase text-slate-600">Total Applied</p>
            <p class="text-2xl sm:text-3xl font-black text-slate-950 mt-1">{{ $totalApplications }}</p>
            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Opportunities</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-emerald-200 bg-emerald-50/60">
            <p class="text-xs font-bold uppercase text-emerald-800">Hired Status</p>
            <p class="text-2xl sm:text-3xl font-black text-emerald-950 mt-1">{{ $hiredCount }}</p>
            <p class="text-[11px] font-semibold text-emerald-700 mt-0.5">🎉 Successful Hires</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-white/80">
            <p class="text-xs font-bold uppercase text-slate-600">Under Review</p>
            <p class="text-2xl sm:text-3xl font-black text-sky-950 mt-1">{{ $pendingCount }}</p>
            <p class="text-[11px] font-semibold text-sky-700 mt-0.5">Pending Employer</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-white/80">
            <p class="text-xs font-bold uppercase text-slate-600">Rejected</p>
            <p class="text-2xl sm:text-3xl font-black text-slate-950 mt-1">{{ $rejectedCount }}</p>
            <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Closed Out</p>
        </div>
    </div>

    <!-- Active Employment Banner (if currently hired or requesting leave) -->
    @if($activeEngagement)
        <div class="glass-panel p-6 sm:p-7 rounded-3xl border-2 {{ $activeEngagement->isLeaveRequested() ? 'border-amber-400 bg-amber-50/80' : 'border-emerald-400 bg-emerald-50/80' }} shadow-xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl {{ $activeEngagement->isLeaveRequested() ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white' }} flex items-center justify-center text-xl font-black shadow-md flex-shrink-0">
                        {{ $activeEngagement->isLeaveRequested() ? '⏳' : '💼' }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded {{ $activeEngagement->isLeaveRequested() ? 'bg-amber-200 text-amber-950 font-black' : 'bg-emerald-200 text-emerald-950 font-black' }}">
                                {{ $activeEngagement->isLeaveRequested() ? 'Leave Request Pending' : 'Current Active Position' }}
                            </span>
                            <span class="text-xs font-bold text-slate-600">
                                Since {{ $activeEngagement->hired_at?->format('M d, Y') ?? $activeEngagement->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-slate-950 mt-0.5">
                            <a href="{{ route('jobs.show', $activeEngagement->jobPost) }}" class="hover:text-sky-700 transition-colors">
                                {{ $activeEngagement->jobPost?->title }}
                            </a>
                        </h3>
                        <p class="text-xs font-semibold text-slate-700">
                            🏢 {{ $activeEngagement->jobPost?->employer?->display_name }} • 📍 {{ $activeEngagement->jobPost?->location_address }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('messages.index', ['user_id' => $activeEngagement->jobPost?->employer_id, 'job_id' => $activeEngagement->job_post_id]) }}"
                        class="px-4 py-2 rounded-xl border border-sky-300 bg-white hover:bg-sky-50 text-sky-800 font-bold text-xs flex items-center gap-1.5 transition-colors shadow-xs">
                        <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        Message Employer
                    </a>

                    @if($activeEngagement->isHired())
                        <!-- Request Leave / Quit Button -->
                        <button type="button"
                            onclick="confirmAction({
                                title: 'Request to Leave / Quit Job?',
                                message: 'Are you sure you want to request to leave this job at {{ addslashes($activeEngagement->jobPost?->employer?->display_name) }}? Your employer will review and confirm your release. Once confirmed, you can apply for new jobs.',
                                formAction: '{{ route('worker.applications.request-leave', $activeEngagement) }}',
                                confirmText: 'Yes, Submit Leave Request',
                                confirmClass: 'bg-amber-600 hover:bg-amber-700 text-white'
                            })"
                            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-md shadow-amber-500/20 transition-all hover:scale-[1.02]">
                            Request Leave / Quit Job
                        </button>
                    @elseif($activeEngagement->isLeaveRequested())
                        <!-- Cancel Leave Request Button -->
                        <button type="button"
                            onclick="confirmAction({
                                title: 'Cancel Leave Request?',
                                message: 'Do you want to cancel your leave request and remain actively hired at {{ addslashes($activeEngagement->jobPost?->employer?->display_name) }}?',
                                formAction: '{{ route('worker.applications.cancel-leave', $activeEngagement) }}',
                                confirmText: 'Yes, Keep Job',
                                confirmClass: 'bg-emerald-600 hover:bg-emerald-700 text-white'
                            })"
                            class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                            Cancel Leave Request
                        </button>
                    @endif
                </div>
            </div>

            @if($activeEngagement->isLeaveRequested())
                <div class="p-3.5 rounded-2xl bg-amber-100/70 border border-amber-300/80 text-xs text-amber-950 font-medium">
                    ⚠️ <strong>Leave Request Submitted:</strong> Awaiting employer confirmation to release you from this job. You will be free to apply for other opportunities as soon as the employer confirms.
                </div>
            @endif
        </div>
    @endif

    <!-- Applications List -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl space-y-4">
        <h2 class="text-lg font-black text-slate-950 border-b border-sky-100 pb-3">
            Application History
        </h2>

        @if($applications->isEmpty())
            <div class="text-center py-12">
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-base font-black text-slate-950">You haven't applied for any jobs yet</h3>
                <p class="text-xs font-semibold text-slate-700 mt-1 max-w-sm mx-auto">
                    Browse active job postings and pinned locations on the Philippine map to submit your qualifications.
                </p>
                <a href="{{ route('jobs.index') }}" class="mt-4 inline-block px-5 py-2.5 rounded-xl bg-sky-500 text-white font-bold text-xs">
                    Browse Available Jobs
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($applications as $app)
                    <div class="glass-card p-5 rounded-3xl border border-white/90 space-y-3">
                        
                        <!-- Top Meta & Status -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-sky-100 text-sky-900 border border-sky-200">
                                        {{ $app->jobPost?->category }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-600">
                                        Applied {{ $app->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                <h3 class="text-base sm:text-lg font-black text-slate-950">
                                    <a href="{{ route('jobs.show', $app->jobPost) }}" class="hover:text-sky-700 transition-colors">
                                        {{ $app->jobPost?->title }}
                                    </a>
                                </h3>
                                <p class="text-xs font-semibold text-slate-700 flex items-center gap-2 mt-0.5">
                                    <span>🏢 <strong>{{ $app->jobPost?->employer?->display_name }}</strong></span>
                                    <span>•</span>
                                    <span>📍 {{ $app->jobPost?->location_address }}</span>
                                </p>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @if($app->isHired())
                                    <div class="px-4 py-1.5 rounded-full bg-emerald-500 text-white font-black text-xs flex items-center gap-1.5 shadow-md shadow-emerald-500/20">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        HIRED!
                                    </div>
                                @elseif($app->isLeaveRequested())
                                    <div class="px-3.5 py-1.5 rounded-full bg-amber-500 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm">
                                        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                        Leave Requested
                                    </div>
                                @elseif($app->isFired())
                                    <div class="px-3.5 py-1.5 rounded-full bg-red-100 text-red-900 border border-red-300 font-extrabold text-xs">
                                        Position Ended (Fired)
                                    </div>
                                @elseif($app->isResigned())
                                    <div class="px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300 font-extrabold text-xs">
                                        Left Job (Resigned)
                                    </div>
                                @elseif($app->isRejected())
                                    <div class="px-3.5 py-1.5 rounded-full bg-red-100 text-red-900 border border-red-200 font-extrabold text-xs">
                                        Rejected
                                    </div>
                                @else
                                    <div class="px-3.5 py-1.5 rounded-full bg-sky-100 text-sky-900 border border-sky-200 font-extrabold text-xs flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                        Pending Review
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Requirements Snippet -->
                        <div class="p-4 bg-white/70 rounded-2xl border border-slate-200/60 text-xs text-slate-800 space-y-2">
                            <span class="font-bold text-slate-950 block">Your Submitted Requirements:</span>
                            <p class="whitespace-pre-line leading-relaxed font-semibold text-slate-800">{{ $app->submitted_requirements }}</p>

                            <!-- Document & Selfie Previews -->
                            <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-4">
                                @if($app->attachment_path)
                                    <div class="flex items-center gap-2">
                                        @if(preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $app->attachment_path))
                                            <a href="{{ asset('storage/' . $app->attachment_path) }}" target="_blank" class="block w-10 h-10 rounded-lg overflow-hidden border border-slate-300 flex-shrink-0">
                                                <img src="{{ asset('storage/' . $app->attachment_path) }}" alt="Requirement Photo" class="w-full h-full object-cover">
                                            </a>
                                        @endif
                                        <a href="{{ asset('storage/' . $app->attachment_path) }}" target="_blank" class="text-[11px] font-bold text-sky-700 hover:text-sky-900 underline">
                                            📄 View Requirement Photo
                                        </a>
                                    </div>
                                @endif

                                @if($app->selfie_path)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $app->selfie_path) }}" target="_blank" class="block w-10 h-10 rounded-lg overflow-hidden border-2 border-purple-400 flex-shrink-0">
                                            <img src="{{ asset('storage/' . $app->selfie_path) }}" alt="Worker Selfie" class="w-full h-full object-cover">
                                        </a>
                                        <a href="{{ asset('storage/' . $app->selfie_path) }}" target="_blank" class="text-[11px] font-bold text-purple-700 hover:text-purple-900 underline">
                                            📸 View Camera Selfie
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($app->termination_reason)
                            <div class="p-3 bg-red-50 rounded-2xl border border-red-200 text-xs text-red-900 font-semibold">
                                <strong>Employer Termination Note:</strong> {{ $app->termination_reason }}
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                            <a href="{{ route('messages.index', ['user_id' => $app->jobPost?->employer_id, 'job_id' => $app->job_post_id]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-sky-200 bg-white hover:bg-sky-50 text-sky-800 font-bold transition-colors">
                                <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                Direct Message Employer
                            </a>

                            <div class="flex items-center gap-2">
                                @if($app->isHired())
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Request to Leave / Quit Job?',
                                            message: 'Are you sure you want to request to leave this job at {{ addslashes($app->jobPost?->employer?->display_name) }}? Your employer will review and confirm your release.',
                                            formAction: '{{ route('worker.applications.request-leave', $app) }}',
                                            confirmText: 'Yes, Submit Leave Request',
                                            confirmClass: 'bg-amber-600 hover:bg-amber-700 text-white'
                                        })"
                                        class="px-3 py-1.5 rounded-xl border border-amber-300 bg-white hover:bg-amber-50 text-amber-800 font-bold text-xs transition-colors">
                                        Request Leave / Quit
                                    </button>
                                @elseif($app->isLeaveRequested())
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Cancel Leave Request?',
                                            message: 'Cancel your leave request and remain actively hired?',
                                            formAction: '{{ route('worker.applications.cancel-leave', $app) }}',
                                            confirmText: 'Yes, Keep Job',
                                            confirmClass: 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                        })"
                                        class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors">
                                        Cancel Leave
                                    </button>
                                @elseif($app->isPending())
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Withdraw Application?',
                                            message: 'Are you sure you want to withdraw your application for {{ addslashes($app->jobPost?->title) }}?',
                                            formAction: '{{ route('worker.applications.cancel', $app) }}',
                                            method: 'DELETE',
                                            confirmText: 'Yes, Withdraw',
                                            confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                        })"
                                        class="text-xs font-bold text-slate-600 hover:text-red-600 transition-colors">
                                        Withdraw Application
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
