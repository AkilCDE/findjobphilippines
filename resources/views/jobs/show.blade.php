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
                            <!-- Apply Form with Requirement Photo & Camera Selfie -->
                            <div class="border-b border-slate-100 pb-3 mb-4">
                                <h3 class="text-lg font-black text-slate-950">
                                    Apply for this Job
                                </h3>
                                <p class="text-xs font-semibold text-slate-600 mt-0.5">
                                    Submit your verified credentials and clear live selfie photo
                                </p>
                            </div>

                            <form method="POST" action="{{ route('worker.apply', $jobPost) }}" enctype="multipart/form-data" id="jobApplicationForm" class="space-y-4">
                                @csrf

                                <!-- Requirements Checklist / Details -->
                                <div>
                                    <label for="submitted_requirements" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                                        Requirements Checklist & Statement <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="submitted_requirements" name="submitted_requirements" rows="3" required
                                        placeholder="Confirm which required documents you are submitting (e.g. Valid Gov't ID, Barangay Clearance, NC II Certificate)..."
                                        class="w-full px-4 py-3 rounded-2xl glass-input text-xs font-semibold">{{ old('submitted_requirements') }}</textarea>
                                </div>

                                <!-- 1. Clear Photo of Requirements / Documents (Mandatory) -->
                                <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200/80 space-y-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <label for="requirements_photo" class="block text-xs font-bold uppercase tracking-wider text-slate-900">
                                                1. Clear Photo of Requirements <span class="text-red-500">*</span>
                                            </label>
                                            <p class="text-[11px] font-semibold text-slate-600">
                                                Upload a clear, legible photo or scan of your ID or required certificate.
                                            </p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-sky-200 text-sky-900">
                                            Required
                                        </span>
                                    </div>

                                    <input type="file" id="requirements_photo" name="requirements_photo" accept="image/jpeg,image/png,image/webp,application/pdf" required
                                        onchange="previewRequirementPhoto(this)"
                                        class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600 cursor-pointer">

                                    <!-- Image Preview Box -->
                                    <div id="requirementPreviewBox" class="hidden mt-2 p-2 rounded-xl bg-white border border-slate-200 flex items-center gap-3">
                                        <img id="requirementPreviewImg" src="" alt="Requirement Preview" class="w-14 h-14 object-cover rounded-lg border border-slate-200">
                                        <div class="flex-1 min-w-0 text-xs">
                                            <p id="requirementFileName" class="font-bold text-slate-900 truncate">document.jpg</p>
                                            <p class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Photo ready for verification
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Live Selfie Using Camera (Mandatory) -->
                                <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200/80 space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-900">
                                                2. Worker Selfie (Camera Verification) <span class="text-red-500">*</span>
                                            </label>
                                            <p class="text-[11px] font-semibold text-slate-600">
                                                Take a live selfie right now using your camera to confirm applicant identity.
                                            </p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-purple-200 text-purple-900">
                                            Live Camera
                                        </span>
                                    </div>

                                    <!-- Camera Interface Box -->
                                    <div class="rounded-xl border border-purple-200/80 bg-white p-3 space-y-3 text-center">
                                        
                                        <!-- Initial State: Open Camera Button -->
                                        <div id="cameraPrompt">
                                            <button type="button" onclick="startSelfieCamera()"
                                                class="w-full py-2.5 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-md shadow-purple-500/20 flex items-center justify-center gap-2 transition-all hover:scale-[1.01]">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>Open Camera & Take Selfie</span>
                                            </button>
                                        </div>

                                        <!-- Camera Streaming View (Hidden initially) -->
                                        <div id="cameraStreamContainer" class="hidden space-y-2">
                                            <div class="relative w-full max-w-xs mx-auto overflow-hidden rounded-xl bg-black border-2 border-purple-400 aspect-[4/3]">
                                                <video id="selfieVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                                                <div class="absolute inset-0 border-2 border-white/40 pointer-events-none rounded-xl m-2 border-dashed"></div>
                                            </div>
                                            <canvas id="selfieCanvas" class="hidden"></canvas>
                                            
                                            <div class="flex items-center justify-center gap-2 pt-1">
                                                <button type="button" onclick="snapSelfie()"
                                                    class="py-2 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5 transition-all">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="7"/></svg>
                                                    Capture Selfie
                                                </button>
                                                <button type="button" onclick="stopSelfieCamera()"
                                                    class="py-2 px-3.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Captured Selfie Preview (Hidden initially) -->
                                        <div id="selfiePreviewContainer" class="hidden space-y-2">
                                            <div class="relative w-full max-w-xs mx-auto overflow-hidden rounded-xl border-2 border-emerald-500 aspect-[4/3] shadow-md">
                                                <img id="selfiePreviewImg" src="" alt="Captured Selfie" class="w-full h-full object-cover">
                                                <div class="absolute top-2 right-2 bg-emerald-600 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                                    <span>✓ Captured</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-center gap-2 pt-1">
                                                <button type="button" onclick="retakeSelfie()"
                                                    class="py-1.5 px-3.5 rounded-xl border border-purple-300 bg-purple-50 hover:bg-purple-100 text-purple-900 font-bold text-xs flex items-center gap-1 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                    Retake Selfie
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Fallback File Upload (If camera denied or unsupported) -->
                                        <div class="pt-2 border-t border-slate-100 text-left">
                                            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Camera not working on your device?</p>
                                            <input type="file" id="selfie_file" name="selfie_file" accept="image/*" capture="user"
                                                onchange="handleSelfieFileFallback(this)"
                                                class="w-full px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-bold file:bg-purple-600 file:text-white">
                                        </div>

                                    </div>

                                    <!-- Hidden Input with Base64 Captured Selfie -->
                                    <input type="hidden" name="selfie_data" id="selfieData" value="">
                                    <p id="selfieErrorMsg" class="text-xs font-bold text-red-600 hidden">
                                        * Please take a live selfie with your camera (or upload a selfie file) before submitting.
                                    </p>
                                </div>

                                <!-- Optional Message -->
                                <div>
                                    <label for="cover_letter" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                                        Message to Employer (Optional)
                                    </label>
                                    <textarea id="cover_letter" name="cover_letter" rows="2"
                                        placeholder="Add an optional message or introduction..."
                                        class="w-full px-4 py-2.5 rounded-2xl glass-input text-xs font-semibold">{{ old('cover_letter') }}</textarea>
                                </div>

                                <button type="submit"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-md shadow-sky-500/20 transition-all hover:scale-[1.01]">
                                    Submit Application with Verified Selfie
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
                                <button type="button"
                                    onclick="confirmAction({
                                        title: 'Delete Job Post Permanently?',
                                        message: 'Are you sure you want to delete \'{{ addslashes($jobPost->title) }}\'? All associated worker applications will be removed. This cannot be undone.',
                                        formAction: '{{ route('employer.posts.destroy', $jobPost) }}',
                                        method: 'DELETE',
                                        confirmText: 'Yes, Delete Post',
                                        confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                    })"
                                    class="px-4 py-2.5 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs transition-colors">
                                    Delete Post
                                </button>
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
                            <button type="button"
                                onclick="confirmAction({
                                    title: 'Delete Job Post Permanently?',
                                    message: 'Are you sure you want to delete \'{{ addslashes($jobPost->title) }}\'? All associated applications will be removed.',
                                    formAction: '{{ route('employer.posts.destroy', $jobPost) }}',
                                    method: 'DELETE',
                                    confirmText: 'Yes, Delete Post',
                                    confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                })"
                                class="w-full py-2 px-3 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center transition-colors">
                                Delete Post
                            </button>
                        </div>
                    @endif
                @endauth
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    let selfieStream = null;

    function previewRequirementPhoto(input) {
        const box = document.getElementById('requirementPreviewBox');
        const img = document.getElementById('requirementPreviewImg');
        const nameEl = document.getElementById('requirementFileName');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            nameEl.textContent = file.name;
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    box.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                img.classList.add('hidden');
                box.classList.remove('hidden');
            }
        } else {
            box.classList.add('hidden');
        }
    }

    async function startSelfieCamera() {
        const prompt = document.getElementById('cameraPrompt');
        const streamContainer = document.getElementById('cameraStreamContainer');
        const previewContainer = document.getElementById('selfiePreviewContainer');
        const video = document.getElementById('selfieVideo');
        const errorMsg = document.getElementById('selfieErrorMsg');

        errorMsg.classList.add('hidden');

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Your browser does not support live camera access. Please use the file upload fallback below.');
            document.getElementById('selfie_file').focus();
            return;
        }

        try {
            selfieStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                },
                audio: false
            });
            video.srcObject = selfieStream;
            prompt.classList.add('hidden');
            previewContainer.classList.add('hidden');
            streamContainer.classList.remove('hidden');
        } catch (err) {
            console.error('Camera access error:', err);
            alert('Could not access camera (' + (err.message || 'Permission denied') + '). Please allow camera access or use the fallback selfie upload below.');
            document.getElementById('selfie_file').focus();
        }
    }

    function stopSelfieCamera() {
        if (selfieStream) {
            selfieStream.getTracks().forEach(track => track.stop());
            selfieStream = null;
        }
        document.getElementById('cameraStreamContainer').classList.add('hidden');
        document.getElementById('cameraPrompt').classList.remove('hidden');
    }

    function snapSelfie() {
        const video = document.getElementById('selfieVideo');
        const canvas = document.getElementById('selfieCanvas');
        const hiddenInput = document.getElementById('selfieData');
        const previewImg = document.getElementById('selfiePreviewImg');
        const streamContainer = document.getElementById('cameraStreamContainer');
        const previewContainer = document.getElementById('selfiePreviewContainer');
        const errorMsg = document.getElementById('selfieErrorMsg');

        if (!video.videoWidth || !video.videoHeight) {
            alert('Camera is still initializing, please wait a second...');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        hiddenInput.value = dataUrl;
        previewImg.src = dataUrl;

        // Stop video
        if (selfieStream) {
            selfieStream.getTracks().forEach(track => track.stop());
            selfieStream = null;
        }

        streamContainer.classList.add('hidden');
        previewContainer.classList.remove('hidden');
        errorMsg.classList.add('hidden');
    }

    function retakeSelfie() {
        document.getElementById('selfieData').value = '';
        startSelfieCamera();
    }

    function handleSelfieFileFallback(input) {
        const hiddenInput = document.getElementById('selfieData');
        const previewImg = document.getElementById('selfiePreviewImg');
        const previewContainer = document.getElementById('selfiePreviewContainer');
        const prompt = document.getElementById('cameraPrompt');
        const streamContainer = document.getElementById('cameraStreamContainer');
        const errorMsg = document.getElementById('selfieErrorMsg');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                hiddenInput.value = e.target.result;
                previewImg.src = e.target.result;
                prompt.classList.add('hidden');
                streamContainer.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                errorMsg.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('jobApplicationForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const selfieData = document.getElementById('selfieData').value;
                const selfieFileInput = document.getElementById('selfie_file');
                const hasFile = selfieFileInput && selfieFileInput.files && selfieFileInput.files.length > 0;
                const errorMsg = document.getElementById('selfieErrorMsg');

                if (!selfieData && !hasFile) {
                    e.preventDefault();
                    errorMsg.classList.remove('hidden');
                    errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            });
        }

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
