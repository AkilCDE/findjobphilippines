@extends('layouts.app')

@section('content')
<div class="space-y-6 text-zinc-100">

    <!-- Header & Navigation -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="{{ route('employer.posts.index') }}" class="text-xs font-bold text-zinc-400 hover:text-white flex items-center gap-1 mb-2 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to My Posts
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Applicants: <span class="text-zinc-300">{{ $jobPost->title }}</span>
            </h1>
            <p class="text-xs font-medium text-zinc-400 mt-1">
                Review submitted worker requirements, evaluate qualifications, and approve hires.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('jobs.show', $jobPost) }}" target="_blank"
                class="px-3.5 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                <span>View Public Post</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </a>
            <a href="{{ route('employer.posts.edit', $jobPost) }}"
                class="px-3.5 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                <span>Edit Post</span>
            </a>
            <button type="button"
                onclick="confirmAction({
                    title: 'Delete Job Post Permanently?',
                    message: 'Are you sure you want to permanently delete \'{{ addslashes($jobPost->title) }}\'? All associated worker applications will also be deleted. This cannot be undone.',
                    formAction: '{{ route('employer.posts.destroy', $jobPost) }}',
                    method: 'DELETE',
                    confirmText: 'Yes, Delete Post',
                    confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                })"
                class="px-3.5 py-2 rounded-xl border border-red-800/60 bg-red-950/40 hover:bg-red-900/60 text-red-300 font-bold text-xs flex items-center gap-1.5 transition-colors">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                <span>Delete Post</span>
            </button>
        </div>
    </div>

    <!-- Live Capacity & Lock Tracker Banner -->
    <div class="p-6 rounded-3xl bg-[#121215] border {{ $jobPost->isLocked() ? 'border-amber-800/80 bg-amber-950/40' : 'border-zinc-800' }} shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider {{ $jobPost->isLocked() ? 'text-amber-400' : 'text-zinc-400' }}">
                    Worker Hiring Quota
                </span>
                <h3 class="text-xl font-bold text-white mt-0.5">
                    {{ $jobPost->workers_hired }} / {{ $jobPost->workers_needed }} Workers Hired
                </h3>
            </div>

            @if($jobPost->isLocked())
                <div class="px-3.5 py-1.5 rounded-full bg-amber-500 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                    AUTOMATICALLY LOCKED (Max Quota Reached)
                </div>
            @else
                <div class="px-3.5 py-1.5 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    {{ $jobPost->remaining_slots }} Slots Remaining (Accepting Applicants)
                </div>
            @endif
        </div>

        <!-- Progress Bar -->
        <div class="w-full h-3 rounded-full bg-zinc-800 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $jobPost->isLocked() ? 'bg-amber-400' : 'bg-white' }}"
                style="width: {{ $jobPost->capacity_percentage }}%"></div>
        </div>

        @if($jobPost->isLocked())
            <p class="text-xs font-bold text-amber-300 mt-2">
                🔒 All {{ $jobPost->workers_needed }} positions have been filled. The post is currently locked and cannot accept additional applications.
            </p>
        @endif
    </div>

    <!-- Applicants List -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-white">
            Submitted Applications ({{ $jobPost->applications->count() }})
        </h2>

        @if($jobPost->applications->isEmpty())
            <div class="p-12 text-center rounded-3xl bg-[#121215] border border-zinc-800">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-zinc-800 text-zinc-300 flex items-center justify-center border border-zinc-700">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                </div>
                <h3 class="text-base font-bold text-white">No applications received yet</h3>
                <p class="text-xs font-medium text-zinc-400 mt-1">
                    Workers will see your pinned location on the Philippine map and submit their requirements here.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($jobPost->applications as $app)
                    <div class="p-6 rounded-3xl bg-[#121215] border border-zinc-800 space-y-4">
                        
                        <!-- Top Info -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-800 text-white font-bold flex items-center justify-center text-base shadow-md border border-zinc-700">
                                    {{ strtoupper(substr($app->worker?->display_name ?? 'W', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-white">
                                        {{ $app->worker?->display_name }}
                                    </h4>
                                    <p class="text-xs font-medium text-zinc-400 flex items-center gap-2">
                                        <span>Sex: <strong class="text-zinc-200">{{ $app->worker?->sex ?? 'N/A' }}</strong></span>
                                        <span>•</span>
                                        <span>📍 {{ $app->worker?->address ?? 'Philippines' }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @if($app->isHired())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-950/80 text-emerald-300 border border-emerald-800/80">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        HIRED WORKER
                                    </span>
                                @elseif($app->isLeaveRequested())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-950/80 text-amber-300 border border-amber-800/80 animate-pulse">
                                        ⚠️ LEAVE REQUESTED
                                    </span>
                                @elseif($app->isFired())
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-950/80 text-red-300 border border-red-800">
                                        FIRED / KICKED
                                    </span>
                                @elseif($app->isResigned())
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-zinc-800 text-zinc-300 border border-zinc-700">
                                        LEFT JOB (RESIGNED)
                                    </span>
                                @elseif($app->isRejected())
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-950/80 text-red-300 border border-red-800">
                                        REJECTED
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-zinc-800 text-zinc-200 border border-zinc-700">
                                        PENDING REVIEW
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Leave Request Notice (if requested) -->
                        @if($app->isLeaveRequested())
                            <div class="p-3.5 rounded-2xl bg-amber-950/80 border border-amber-800 text-xs text-amber-200 font-medium space-y-1">
                                <div class="flex items-center gap-1.5 font-bold text-amber-300">
                                    <span>⚠️ Worker Submitted a Leave Request:</span>
                                </div>
                                <p class="text-zinc-200"><strong>Reason:</strong> "{{ $app->leave_reason }}"</p>
                                <p class="text-[11px] text-amber-300">Requested {{ $app->leave_requested_at?->format('M d, Y h:i A') ?? 'recently' }}. Confirming will release this worker and reopen the hiring slot.</p>
                            </div>
                        @endif

                        <!-- Submitted Requirements & Notes -->
                        <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-800 space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white uppercase tracking-wider text-[10px]">
                                    Submitted Requirements & Credentials:
                                </span>
                                <span class="text-[11px] text-zinc-400 font-medium">
                                    Applied {{ $app->created_at->format('M d, Y h:i A') }}
                                </span>
                            </div>
                            <p class="text-zinc-300 font-medium whitespace-pre-line leading-relaxed">
                                {{ $app->submitted_requirements }}
                            </p>

                            <!-- Verification Photos: Requirement Document & Live Camera Selfie -->
                            <div class="pt-2 border-t border-zinc-800 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- 1. Requirement Document Photo -->
                                <div class="p-3 rounded-xl bg-zinc-850 border border-zinc-750 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                                            📄 Clear Photo of Requirements
                                        </span>
                                        @if($app->attachment_path)
                                            <span class="text-[10px] font-bold text-emerald-300 bg-emerald-950 px-1.5 py-0.5 rounded border border-emerald-800">Uploaded</span>
                                        @else
                                            <span class="text-[10px] font-medium text-zinc-500">None</span>
                                        @endif
                                    </div>
                                    @if($app->attachment_path)
                                        <div class="flex items-center gap-3">
                                            @php
                                                $isReqImage = preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $app->attachment_path);
                                            @endphp
                                            @if($isReqImage)
                                                <a href="{{ asset('storage/' . $app->attachment_path) }}" target="_blank" class="block shrink-0 group relative overflow-hidden rounded-lg border border-zinc-700 w-16 h-16 bg-zinc-800">
                                                    <img src="{{ asset('storage/' . $app->attachment_path) }}" alt="Requirement Photo" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                                </a>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-zinc-200 font-bold truncate">{{ basename($app->attachment_path) }}</p>
                                                <a href="{{ asset('storage/' . $app->attachment_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-400 hover:text-sky-300 mt-0.5">
                                                    <span>View Full Document</span>
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-zinc-500 text-[11px] italic">No document file attached.</p>
                                    @endif
                                </div>

                                <!-- 2. Camera Selfie Verification -->
                                <div class="p-3 rounded-xl bg-zinc-850 border border-zinc-750 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                                            📸 Live Camera Selfie
                                        </span>
                                        @if($app->selfie_path)
                                            <span class="text-[10px] font-bold text-purple-300 bg-purple-950 px-1.5 py-0.5 rounded border border-purple-800">Verified Selfie</span>
                                        @else
                                            <span class="text-[10px] font-medium text-zinc-500">None</span>
                                        @endif
                                    </div>
                                    @if($app->selfie_path)
                                        <div class="flex items-center gap-3">
                                            <a href="{{ asset('storage/' . $app->selfie_path) }}" target="_blank" class="block shrink-0 group relative overflow-hidden rounded-lg border-2 border-purple-500 w-16 h-16 bg-zinc-800">
                                                <img src="{{ asset('storage/' . $app->selfie_path) }}" alt="Worker Selfie" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                            </a>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-zinc-200 font-bold text-xs">{{ $app->worker?->display_name }}'s Selfie</p>
                                                <a href="{{ asset('storage/' . $app->selfie_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-400 hover:text-purple-300 mt-0.5">
                                                    <span>View Full Selfie</span>
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-zinc-500 text-[11px] italic">No selfie captured.</p>
                                    @endif
                                </div>
                            </div>

                            @if($app->cover_letter)
                                <div class="pt-2 border-t border-zinc-800">
                                    <span class="font-bold text-white">Cover Message:</span>
                                    <p class="text-zinc-300 italic mt-0.5">{{ $app->cover_letter }}</p>
                                </div>
                            @endif

                            <div class="pt-2 border-t border-zinc-800 flex items-center justify-between font-medium flex-wrap gap-2 text-zinc-400">
                                <span>Contact Number: <strong class="text-white">{{ $app->worker?->contact_number ?? 'Not provided' }}</strong></span>
                                <span>Email: <strong class="text-white">{{ $app->worker?->email }}</strong></span>
                            </div>
                        </div>

                        <!-- Action Buttons with Confirmation Popups -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-zinc-800">
                            
                            <!-- Direct Message Button -->
                            <a href="{{ route('messages.index', ['user_id' => $app->worker_id, 'job_id' => $jobPost->id]) }}"
                                class="px-4 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-1.5 transition-colors">
                                <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                Message {{ $app->worker?->first_name ?? 'Worker' }}
                            </a>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2 flex-wrap">
                                
                                @if($app->isActiveEngagement())
                                    
                                    @if($app->isLeaveRequested())
                                        <!-- Approve Leave Button -->
                                        <button type="button"
                                            onclick="confirmAction({
                                                title: 'Approve Worker Leave?',
                                                message: 'Approve {{ addslashes($app->worker?->display_name) }}\'s request to leave? The worker will be released from this position and the quota slot will immediately reopen ({{ max(0, $jobPost->workers_hired - 1) }}/{{ $jobPost->workers_needed }}).',
                                                formAction: '{{ route('employer.posts.applicants.confirm-leave', [$jobPost, $app]) }}',
                                                confirmText: 'Yes, Approve Leave',
                                                confirmClass: 'bg-amber-600 hover:bg-amber-700 text-white'
                                            })"
                                            class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-md transition-all hover:scale-[1.02]">
                                            Approve Leave
                                        </button>

                                        <!-- Decline Leave Button -->
                                        <button type="button"
                                            onclick="confirmAction({
                                                title: 'Decline Leave Request?',
                                                message: 'Decline {{ addslashes($app->worker?->display_name) }}\'s leave request? Worker will remain active.',
                                                formAction: '{{ route('employer.posts.applicants.reject-leave', [$jobPost, $app]) }}',
                                                confirmText: 'Decline Request',
                                                confirmClass: 'bg-zinc-700 hover:bg-zinc-600 text-white'
                                            })"
                                            class="px-3 py-2 rounded-xl border border-zinc-700 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold text-xs transition-colors">
                                            Decline Leave
                                        </button>
                                    @endif

                                    <!-- Fire Worker Button (Kicks worker and reopens slot) -->
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Fire Worker Confirmation',
                                            message: 'Are you sure you want to fire {{ addslashes($app->worker?->display_name) }}? They will be automatically kicked from this job, and the slot will immediately reopen ({{ max(0, $jobPost->workers_hired - 1) }}/{{ $jobPost->workers_needed }}).',
                                            formAction: '{{ route('employer.posts.applicants.fire', [$jobPost, $app]) }}',
                                            confirmText: 'Yes, Fire Worker',
                                            confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                        })"
                                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs shadow-md transition-all hover:scale-[1.02]">
                                        Fire Worker
                                    </button>

                                @elseif($app->isPending())
                                    
                                    <!-- Reject Button -->
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Reject Application?',
                                            message: 'Are you sure you want to mark {{ addslashes($app->worker?->display_name) }}\'s application as Rejected?',
                                            formAction: '{{ route('employer.posts.applicants.reject', [$jobPost, $app]) }}',
                                            confirmText: 'Yes, Reject',
                                            confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                        })"
                                        class="px-3 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-red-400 font-bold text-xs transition-colors">
                                        Reject
                                    </button>

                                    <!-- Hire Button -->
                                    @if($jobPost->isLocked())
                                        <span class="text-xs font-bold text-zinc-500 italic">
                                            Quota Full (Cannot Hire)
                                        </span>
                                    @elseif($app->worker && $app->worker->hasActiveJob())
                                        <span class="text-xs font-bold text-amber-300 italic px-2 py-1 bg-amber-950/60 rounded-lg border border-amber-800">
                                            Worker Hired Elsewhere
                                        </span>
                                    @else
                                        <button type="button"
                                            onclick="confirmAction({
                                                title: 'Hire Worker Confirmation',
                                                message: 'Are you sure you want to hire {{ addslashes($app->worker?->display_name) }}? Hired workers count will increase from {{ $jobPost->workers_hired }}/{{ $jobPost->workers_needed }} to {{ $jobPost->workers_hired + 1 }}/{{ $jobPost->workers_needed }}. If full, post will automatically lock.',
                                                formAction: '{{ route('employer.posts.applicants.hire', [$jobPost, $app]) }}',
                                                confirmText: 'Yes, Confirm Hire',
                                                confirmClass: 'bg-white hover:bg-zinc-200 text-black'
                                            })"
                                            class="px-5 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all hover:scale-[1.02]">
                                            Hire Worker
                                        </button>
                                    @endif

                                @elseif($app->isFired())
                                    <span class="text-xs font-bold text-red-400 italic">
                                        Worker Fired / Terminated
                                    </span>
                                @elseif($app->isResigned())
                                    <span class="text-xs font-bold text-zinc-500 italic">
                                        Worker Resigned / Released
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
