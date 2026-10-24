@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Back Navigation -->
    <div>
        <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-800 hover:text-sky-950 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Jobs
        </a>
    </div>

    <!-- Main Grid: Job Details & Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Details Column (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Main Job Card -->
            <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl space-y-6">
                
                <div>
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="text-xs font-extrabold uppercase px-2.5 py-1 rounded-xl bg-sky-100 text-sky-900 border border-sky-200">
                            {{ $jobPost->category }}
                        </span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 border border-slate-200">
                            {{ $jobPost->job_type }}
                        </span>

                        @if($jobPost->isLocked())
                            <span class="text-xs font-extrabold px-2.5 py-1 rounded-xl bg-amber-100 text-amber-900 border border-amber-200 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                Locked (Capacity Full)
                            </span>
                        @elseif($jobPost->isArchived())
                            <span class="text-xs font-extrabold px-2.5 py-1 rounded-xl bg-slate-200 text-slate-800">
                                Archived
                            </span>
                        @else
                            <span class="text-xs font-extrabold px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-900 border border-emerald-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Open
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight leading-tight">
                        {{ $jobPost->title }}
                    </h1>

                    <!-- Employer Info -->
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs font-bold text-slate-800">
                        <span class="text-sky-800">
                            {{ $jobPost->employer?->display_name ?? 'Employer' }}
                        </span>
                        <span>•</span>
                        <span>{{ $jobPost->created_at->diffForHumans() }}</span>
                        <span>•</span>
                        <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                            {{ $jobPost->salary_range ?? 'Competitive Pay' }}
                        </span>
                    </div>
                </div>

                <!-- Worker Capacity Tracker Bar -->
                <div class="p-4 rounded-2xl bg-sky-50/80 border border-sky-200/80">
                    <div class="flex items-center justify-between text-xs font-extrabold mb-2">
                        <span class="text-slate-900">Capacity:</span>
                        <span class="{{ $jobPost->isLocked() ? 'text-amber-800 font-black' : 'text-sky-900' }}">
                            {{ $jobPost->workers_hired }} of {{ $jobPost->workers_needed }} Hired
                            ({{ $jobPost->remaining_slots }} remaining)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-200 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $jobPost->isLocked() ? 'bg-amber-500' : 'bg-sky-500' }}"
                            style="width: {{ $jobPost->capacity_percentage }}%"></div>
                    </div>
                    @if($jobPost->isLocked())
                        <p class="text-xs font-bold text-amber-800 mt-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                            This post reached maximum capacity and is locked.
                        </p>
                    @endif
                </div>

                <!-- Description -->
                <div>
                    <h2 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider mb-2">
                        Description
                    </h2>
                    <div class="text-sm font-semibold text-slate-800 leading-relaxed whitespace-pre-line bg-white/60 p-4 rounded-2xl border border-white">
                        {{ $jobPost->description }}
                    </div>
                </div>

                <!-- Requirements -->
                <div>
                    <h2 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider mb-2">
                        Requirements
                    </h2>
                    <div class="text-sm font-semibold text-slate-800 leading-relaxed whitespace-pre-line bg-sky-50/50 p-4 rounded-2xl border border-sky-100">
                        {{ $jobPost->requirements }}
                    </div>
                </div>

            </div>

            <!-- Application & Employer Action Section -->
            @auth
                @if(auth()->user()->isWorker())
                    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl" id="applySection">
                        
                        @if($userApplication && $userApplication->isActiveEngagement())
                            <!-- Currently Hired or Leave Requested View -->
                            <div class="text-center py-4">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full {{ $userApplication->isLeaveRequested() ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center">
                                    @if($userApplication->isLeaveRequested())
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    @endif
                                </div>
                                <h3 class="text-lg font-black text-slate-950">
                                    {{ $userApplication->isLeaveRequested() ? 'Leave Request Pending' : 'You are Hired for this Job!' }}
                                </h3>
                                <p class="text-xs font-bold mt-1 text-slate-700">
                                    Hired on {{ $userApplication->hired_at?->format('M d, Y') ?? $userApplication->created_at->format('M d, Y') }}
                                </p>
                                
                                <div class="inline-block mt-3 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $userApplication->isLeaveRequested() ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white' }}">
                                    Status: {{ $userApplication->isLeaveRequested() ? 'Leave Requested (Awaiting Employer Confirmation)' : 'Active Worker' }}
                                </div>

                                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                                    <a href="{{ route('worker.applications') }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition-all">
                                        View in My Applications
                                    </a>
                                    <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-sky-300 bg-white hover:bg-sky-50 text-sky-800 font-bold text-xs shadow-sm transition-colors">
                                        <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        Message Employer
                                    </a>
                                </div>
                            </div>
                        @elseif($userApplication && $userApplication->isPending())
                            <!-- Already Applied View (Pending) -->
                            <div class="text-center py-4">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h3 class="text-lg font-black text-slate-950">
                                    Application Pending Review
                                </h3>
                                <p class="text-xs font-bold mt-1 text-slate-700">
                                    Submitted {{ $userApplication->created_at->format('M d, Y') }}
                                </p>
                                
                                <div class="inline-block mt-3 px-4 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-sky-500 text-white">
                                    Status: Pending Review
                                </div>

                                <div class="mt-4 p-4 rounded-2xl bg-white/70 text-left border border-slate-200/60 max-w-lg mx-auto text-xs">
                                    <p class="font-bold text-slate-900 mb-1">Submitted Requirements:</p>
                                    <p class="text-slate-700 font-semibold whitespace-pre-line">{{ $userApplication->submitted_requirements }}</p>
                                </div>

                                <div class="mt-5">
                                    <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        Message Employer
                                    </a>
                                </div>
                            </div>
                        @elseif($userApplication && $userApplication->isFired())
                            <!-- Terminated / Fired View -->
                            <div class="text-center py-4 space-y-2">
                                <div class="w-12 h-12 mx-auto rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </div>
                                <h3 class="text-lg font-black text-slate-950">Position Ended</h3>
                                <p class="text-xs font-semibold text-slate-700 max-w-md mx-auto">
                                    Your engagement for this position was terminated by the employer. You cannot re-apply to this posting.
                                </p>
                            </div>
                        @elseif(auth()->user()->hasActiveJob())
                            <!-- Restriction / Trapping: Worker already has an active hired job -->
                            <div class="p-6 rounded-3xl border border-amber-300 bg-amber-50/80 text-slate-900 space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 text-lg shadow-sm">
                                        ⚠️
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-amber-950">
                                            Application Restricted (Active Job)
                                        </h3>
                                        <p class="text-xs font-semibold text-amber-900 mt-1">
                                            You are currently hired for <strong>{{ auth()->user()->activeJobApplication()?->jobPost?->title }}</strong> at <strong>{{ auth()->user()->activeJobApplication()?->jobPost?->employer?->display_name }}</strong>.
                                        </p>
                                    </div>
                                </div>
                                <p class="text-xs font-medium text-slate-700 leading-relaxed">
                                    Workers holding an active job cannot apply for other positions. To apply for a new opportunity, you must first request to leave your current position and receive confirmation from your employer.
                                </p>
                                <div class="pt-1">
                                    <a href="{{ route('worker.applications') }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-md shadow-amber-600/20 transition-all">
                                        <span>Manage Active Job in Applications</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </div>
                            </div>
                        @elseif($jobPost->isLocked())
                            <div class="text-center py-6 text-slate-800">
                                <h3 class="text-lg font-black text-slate-950">Applications Closed</h3>
                                <p class="text-xs font-semibold text-slate-700 mt-1">This job has hired all {{ $jobPost->workers_needed }} workers and is locked.</p>
                            </div>
                        @else
                            <!-- Apply Form -->
                            <h3 class="text-lg font-black text-slate-950 mb-4">
                                Apply for this Job
                            </h3>

                            <form method="POST" action="{{ route('worker.apply', $jobPost) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="submitted_requirements" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                                        Requirements Checklist <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="submitted_requirements" name="submitted_requirements" rows="4" required
                                        placeholder="List your qualifications, valid IDs, clearances, or certificates..."
                                        class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">{{ old('submitted_requirements') }}</textarea>
                                </div>

                                <div>
                                    <label for="cover_letter" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                                        Message to Employer (Optional)
                                    </label>
                                    <textarea id="cover_letter" name="cover_letter" rows="2"
                                        placeholder="Add an optional message..."
                                        class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">{{ old('cover_letter') }}</textarea>
                                </div>

                                <div>
                                    <label for="attachment" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                                        Attachment (Optional)
                                    </label>
                                    <input type="file" id="attachment" name="attachment"
                                        class="w-full px-4 py-2 rounded-2xl glass-input text-xs font-semibold file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600">
                                </div>

                                <button type="submit"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-md shadow-sky-500/20 transition-all">
                                    Submit Application
                                </button>
                            </form>
                        @endif

                    </div>
                @elseif(auth()->user()->isEmployer())
                    <!-- Employer Viewing Post Area -->
                    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl space-y-4">
                        @if($jobPost->employer_id === auth()->id())
                            <!-- Owner Employer -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-sky-500 text-white flex items-center justify-center font-bold">
                                    📌
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-950">You Created This Job Post</h3>
                                    <p class="text-xs font-semibold text-slate-600">
                                        Quota: <strong>{{ $jobPost->workers_hired }}/{{ $jobPost->workers_needed }}</strong> Hired • Status: <strong class="capitalize">{{ $jobPost->status }}</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <a href="{{ route('employer.posts.applicants', $jobPost) }}"
                                    class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-xs shadow-md shadow-sky-500/20 transition-all">
                                    Manage Applicants ({{ $jobPost->applications->count() }})
                                </a>
                                <a href="{{ route('employer.posts.edit', $jobPost) }}"
                                    class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                                    Edit Post
                                </a>
                            </div>
                        @else
                            <!-- Other Employer Viewing This Post (Employer-to-Employer) -->
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-base flex-shrink-0">
                                    🏢
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-black text-slate-950">Employer-to-Employer Overview</h3>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">Browse Mode</span>
                                    </div>
                                    <p class="text-xs font-medium text-slate-700 leading-relaxed">
                                        Employers cannot apply for other employers' job posts. You can explore all details, requirements, workplace location, and directly message this employer for collaboration or hiring coordination.
                                    </p>
                                </div>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-xs shadow-md shadow-sky-500/20 transition-all hover:scale-[1.02]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    Direct Message Employer
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <!-- Guest Prompt -->
                <div class="glass-panel p-6 rounded-3xl border border-sky-200 text-center space-y-3">
                    <h3 class="text-base font-black text-slate-950">Sign in to apply for this job</h3>
                    <div class="flex items-center justify-center gap-3 pt-1">
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-sm">
                            Sign In
                        </a>
                        <a href="{{ route('register', ['role' => 'worker']) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-bold text-xs">
                            Sign Up
                        </a>
                    </div>
                </div>
            @endauth

        </div>

        <!-- Right Column: Location Map & Employer Card (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Workplace Location Map Pin Card -->
            <div class="glass-panel p-5 rounded-3xl border border-white/80 shadow-xl space-y-3">
                <h3 class="text-sm font-black text-slate-950 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Location Map
                </h3>

                <!-- Leaflet Location View Map -->
                <div id="jobDetailMap" class="w-full h-56 rounded-2xl border border-sky-100 z-0"></div>

                <div class="p-3 bg-white/70 rounded-xl border border-slate-100 text-xs font-semibold text-slate-800">
                    <p class="font-bold text-slate-950 mb-0.5">Address:</p>
                    <p class="text-slate-800">{{ $jobPost->location_address }}</p>
                </div>
            </div>

            <!-- Employer Details Card -->
            <div class="glass-panel p-5 rounded-3xl border border-white/80 shadow-xl space-y-4">
                <h3 class="text-sm font-black text-slate-950">Employer Details</h3>
                
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-sky-500 text-white font-black flex items-center justify-center text-base shadow-sm">
                        {{ strtoupper(substr($jobPost->employer?->display_name ?? 'E', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-950">{{ $jobPost->employer?->display_name }}</h4>
                        <p class="text-xs text-slate-600 font-semibold">{{ $jobPost->employer?->address ?? 'Philippines' }}</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-1.5 text-xs font-semibold text-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Contact:</span>
                        <span class="font-bold text-slate-900">{{ $jobPost->employer?->contact_number ?? 'Available on file' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Email:</span>
                        <span class="font-bold text-slate-900">{{ $jobPost->employer?->email }}</span>
                    </div>
                </div>

                @auth
                    @if(auth()->id() !== $jobPost->employer_id)
                        <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                            class="w-full py-2.5 px-4 rounded-xl border border-sky-300 bg-white hover:bg-sky-50 text-sky-800 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            Message Employer
                        </a>
                    @else
                        <!-- Employer Owner Quick Actions -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <a href="{{ route('employer.posts.applicants', $jobPost) }}"
                                class="w-full py-2.5 px-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                                View Applicants ({{ $jobPost->applications->count() }})
                            </a>
                            <a href="{{ route('employer.posts.edit', $jobPost) }}"
                                class="w-full py-2 px-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs flex items-center justify-center">
                                Edit Post
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $jobPost->latitude ?? 12.8797 }};
        const lng = {{ $jobPost->longitude ?? 121.7740 }};

        const map = L.map('jobDetailMap', {
            center: [lat, lng],
            zoom: 14,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap | Findjobph',
            maxZoom: 19
        }).addTo(map);

        const pinIcon = L.divIcon({
            className: 'custom-pin-detail',
            html: '<div style="background-color: #0284c7; width: 30px; height: 30px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.25); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:16px;height:16px;margin-top:4px;margin-left:4px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        });

        L.marker([lat, lng], { icon: pinIcon })
            .addTo(map)
            .bindPopup("<b>{{ addslashes($jobPost->title) }}</b><br>{{ addslashes($jobPost->location_address) }}")
            .openPopup();
    });
</script>
@endpush
@endsection
