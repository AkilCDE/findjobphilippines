@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header & Navigation -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="{{ route('employer.posts.index') }}" class="text-xs font-bold text-sky-800 hover:text-sky-950 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to My Posts
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                Applicants: <span class="text-sky-600">{{ $jobPost->title }}</span>
            </h1>
            <p class="text-xs font-semibold text-slate-700 mt-1">
                Review submitted worker requirements, evaluate qualifications, and approve hires.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('jobs.show', $jobPost) }}" target="_blank"
                class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs flex items-center gap-1.5 shadow-sm">
                <span>View Public Post</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </a>
        </div>
    </div>

    <!-- Live Capacity & Lock Tracker Banner -->
    <div class="glass-panel p-6 rounded-3xl border {{ $jobPost->isLocked() ? 'border-amber-300 bg-amber-50/70' : 'border-sky-200 bg-sky-50/70' }} shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2">
            <div>
                <span class="text-xs font-black uppercase tracking-wider {{ $jobPost->isLocked() ? 'text-amber-900' : 'text-sky-900' }}">
                    Worker Hiring Quota
                </span>
                <h3 class="text-xl font-black text-slate-950 mt-0.5">
                    {{ $jobPost->workers_hired }} / {{ $jobPost->workers_needed }} Workers Hired
                </h3>
            </div>

            @if($jobPost->isLocked())
                <div class="px-3.5 py-1.5 rounded-full bg-amber-500 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                    AUTOMATICALLY LOCKED (Max Quota Reached)
                </div>
            @else
                <div class="px-3.5 py-1.5 rounded-full bg-emerald-500 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    {{ $jobPost->remaining_slots }} Slots Remaining (Accepting Applicants)
                </div>
            @endif
        </div>

        <!-- Progress Bar -->
        <div class="w-full h-3 rounded-full bg-slate-200/90 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $jobPost->isLocked() ? 'bg-amber-500' : 'bg-sky-500' }}"
                style="width: {{ $jobPost->capacity_percentage }}%"></div>
        </div>

        @if($jobPost->isLocked())
            <p class="text-xs font-bold text-amber-900 mt-2">
                🔒 All {{ $jobPost->workers_needed }} positions have been filled. The post is currently locked and cannot accept additional applications.
            </p>
        @endif
    </div>

    <!-- Applicants List -->
    <div class="space-y-4">
        <h2 class="text-lg font-black text-slate-950">
            Submitted Applications ({{ $jobPost->applications->count() }})
        </h2>

        @if($jobPost->applications->isEmpty())
            <div class="glass-panel p-12 text-center rounded-3xl border border-white/80">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                </div>
                <h3 class="text-base font-black text-slate-950">No applications received yet</h3>
                <p class="text-xs font-semibold text-slate-700 mt-1">
                    Workers will see your pinned location on the Philippine map and submit their requirements here.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($jobPost->applications as $app)
                    <div class="glass-card p-6 rounded-3xl border border-white/90 space-y-4">
                        
                        <!-- Top Info -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-600 to-sky-400 text-white font-black flex items-center justify-center text-base shadow-md">
                                    {{ strtoupper(substr($app->worker?->display_name ?? 'W', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-base font-black text-slate-950">
                                        {{ $app->worker?->display_name }}
                                    </h4>
                                    <p class="text-xs font-semibold text-slate-700 flex items-center gap-2">
                                        <span>Sex: <strong>{{ $app->worker?->sex ?? 'N/A' }}</strong></span>
                                        <span>•</span>
                                        <span>📍 {{ $app->worker?->address ?? 'Philippines' }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @if($app->isHired())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        HIRED WORKER
                                    </span>
                                @elseif($app->isRejected())
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-black bg-red-100 text-red-900 border border-red-300">
                                        REJECTED
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-black bg-sky-100 text-sky-900 border border-sky-300">
                                        PENDING REVIEW
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Submitted Requirements & Notes -->
                        <div class="p-4 rounded-2xl bg-white/70 border border-slate-200/60 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-slate-950 uppercase tracking-wider text-[10px]">
                                    Submitted Requirements & Credentials:
                                </span>
                                <span class="text-[11px] text-slate-600 font-semibold">
                                    Applied {{ $app->created_at->format('M d, Y h:i A') }}
                                </span>
                            </div>
                            <p class="text-slate-800 font-semibold whitespace-pre-line leading-relaxed">
                                {{ $app->submitted_requirements }}
                            </p>

                            @if($app->cover_letter)
                                <div class="pt-2 border-t border-slate-100">
                                    <span class="font-bold text-slate-900">Cover Message:</span>
                                    <p class="text-slate-700 italic mt-0.5">{{ $app->cover_letter }}</p>
                                </div>
                            @endif

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between font-semibold">
                                <span class="text-slate-700">Contact Number: <strong class="text-slate-950">{{ $app->worker?->contact_number ?? 'Not provided' }}</strong></span>
                                <span class="text-slate-700">Email: <strong class="text-slate-950">{{ $app->worker?->email }}</strong></span>
                            </div>
                        </div>

                        <!-- Action Buttons with Confirmation Popups (No Duplicates) -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
                            
                            <!-- Direct Message Button -->
                            <a href="{{ route('messages.index', ['user_id' => $app->worker_id, 'job_id' => $jobPost->id]) }}"
                                class="px-4 py-2 rounded-xl border border-sky-300 bg-white hover:bg-sky-50 text-sky-800 font-bold text-xs flex items-center gap-1.5 transition-colors">
                                <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                Message {{ $app->worker?->first_name ?? 'Worker' }}
                            </a>

                            <!-- Hire / Reject Form Actions with Pop-ups -->
                            <div class="flex items-center gap-2">
                                
                                <!-- Reject Button -->
                                @if(!$app->isRejected())
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Reject Application?',
                                            message: 'Are you sure you want to mark {{ addslashes($app->worker?->display_name) }}\'s application as Rejected?',
                                            formAction: '{{ route('employer.posts.applicants.reject', [$jobPost, $app]) }}',
                                            confirmText: 'Yes, Reject',
                                            confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                        })"
                                        class="px-3 py-2 rounded-xl border border-red-200 bg-white hover:bg-red-50 text-red-700 font-bold text-xs transition-colors">
                                        Reject
                                    </button>
                                @endif

                                <!-- Hire Button -->
                                @if(!$app->isHired())
                                    @if($jobPost->isLocked())
                                        <span class="text-xs font-bold text-slate-500 italic">
                                            Quota Full (Cannot Hire)
                                        </span>
                                    @else
                                        <button type="button"
                                            onclick="confirmAction({
                                                title: 'Hire Worker Confirmation',
                                                message: 'Are you sure you want to hire {{ addslashes($app->worker?->display_name) }}? Hired workers count will increase from {{ $jobPost->workers_hired }}/{{ $jobPost->workers_needed }} to {{ $jobPost->workers_hired + 1 }}/{{ $jobPost->workers_needed }}. If full, post will automatically lock.',
                                                formAction: '{{ route('employer.posts.applicants.hire', [$jobPost, $app]) }}',
                                                confirmText: 'Yes, Confirm Hire',
                                                confirmClass: 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                            })"
                                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02]">
                                            Hire Worker
                                        </button>
                                    @endif
                                @else
                                    <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 font-extrabold text-xs border border-emerald-200">
                                        ✓ Currently Hired
                                    </span>
                                @endif

                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
