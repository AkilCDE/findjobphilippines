@extends('layouts.app')

@section('content')
<div class="space-y-6 text-zinc-100">

    <!-- Back Navigation -->
    <div>
        <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Jobs
        </a>
    </div>

    <!-- Main Grid: Job Details & Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Details Column (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Main Job Card -->
            <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-6">
                
                <div>
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="text-xs font-bold uppercase px-2.5 py-1 rounded-xl bg-zinc-800 text-zinc-200 border border-zinc-700">
                            {{ $jobPost->category }}
                        </span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-xl bg-zinc-900 text-zinc-300 border border-zinc-800">
                            {{ $jobPost->job_type }}
                        </span>

                        @if($jobPost->isLocked())
                            <span class="text-xs font-bold px-2.5 py-1 rounded-xl bg-amber-950/80 text-amber-300 border border-amber-800/80 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                Locked (Capacity Full)
                            </span>
                        @elseif($jobPost->isArchived())
                            <span class="text-xs font-bold px-2.5 py-1 rounded-xl bg-zinc-800 text-zinc-400 border border-zinc-700">
                                Archived
                            </span>
                        @else
                            <span class="text-xs font-bold px-2.5 py-1 rounded-xl bg-emerald-950/80 text-emerald-300 border border-emerald-800/80 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Open
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                        {{ $jobPost->title }}
                    </h1>

                    <!-- Employer Info -->
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs font-medium text-zinc-400">
                        <span class="text-zinc-200 font-bold">
                            {{ $jobPost->employer?->display_name ?? 'Employer' }}
                        </span>
                        <span>•</span>
                        <span>{{ $jobPost->created_at->diffForHumans() }}</span>
                        <span>•</span>
                        <span class="text-emerald-400 font-bold bg-zinc-900 px-2 py-0.5 rounded-lg border border-zinc-800">
                            {{ $jobPost->salary_range ?? 'Competitive Pay' }}
                        </span>
                    </div>
                </div>

                <!-- Worker Capacity Tracker Bar -->
                <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-800">
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span class="text-white">Capacity:</span>
                        <span class="{{ $jobPost->isLocked() ? 'text-amber-400 font-black' : 'text-zinc-200' }}">
                            {{ $jobPost->workers_hired }} of {{ $jobPost->workers_needed }} Hired
                            ({{ $jobPost->remaining_slots }} remaining)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-zinc-800 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $jobPost->isLocked() ? 'bg-amber-400' : 'bg-white' }}"
                            style="width: {{ $jobPost->capacity_percentage }}%"></div>
                    </div>
                    @if($jobPost->isLocked())
                        <p class="text-xs font-bold text-amber-400 mt-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                            This post reached maximum capacity and is locked.
                        </p>
                    @endif
                </div>

                <!-- Description -->
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider mb-2">
                        Description
                    </h2>
                    <div class="text-sm font-medium text-zinc-300 leading-relaxed whitespace-pre-line bg-zinc-900/80 p-4 rounded-2xl border border-zinc-800">
                        {{ $jobPost->description }}
                    </div>
                </div>

                <!-- Requirements -->
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider mb-2">
                        Requirements
                    </h2>
                    <div class="text-sm font-medium text-zinc-300 leading-relaxed whitespace-pre-line bg-zinc-900/80 p-4 rounded-2xl border border-zinc-800">
                        {{ $jobPost->requirements }}
                    </div>
                </div>

            </div>

            <!-- Application & Employer Action Section -->
            @auth
                @if(auth()->user()->isWorker())
                    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl" id="applySection">
                        
                        @if($userApplication && $userApplication->isActiveEngagement())
                            <!-- Currently Hired or Leave Requested View -->
                            <div class="text-center py-4">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full {{ $userApplication->isLeaveRequested() ? 'bg-amber-950/80 text-amber-400 border border-amber-800' : 'bg-emerald-950/80 text-emerald-400 border border-emerald-800' }} flex items-center justify-center">
                                    @if($userApplication->isLeaveRequested())
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    @endif
                                </div>
                                <h3 class="text-lg font-bold text-white">
                                    {{ $userApplication->isLeaveRequested() ? 'Leave Request Pending' : 'You are Hired for this Job!' }}
                                </h3>
                                <p class="text-xs font-medium mt-1 text-zinc-400">
                                    Hired on {{ $userApplication->hired_at?->format('M d, Y') ?? $userApplication->created_at->format('M d, Y') }}
                                </p>
                                
                                <div class="inline-block mt-3 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $userApplication->isLeaveRequested() ? 'bg-amber-950 text-amber-300 border border-amber-800' : 'bg-emerald-950 text-emerald-300 border border-emerald-800' }}">
                                    Status: {{ $userApplication->isLeaveRequested() ? 'Leave Requested (Awaiting Employer Confirmation)' : 'Active Worker' }}
                                </div>

                                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                                    <a href="{{ route('worker.applications') }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all">
                                        View in My Applications
                                    </a>
                                    <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs transition-colors">
                                        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        Message Employer
                                    </a>
                                </div>
                            </div>
                        @elseif($userApplication && $userApplication->isPending())
                            <!-- Already Applied View (Pending) -->
                            <div class="text-center py-4">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-zinc-800 text-zinc-200 flex items-center justify-center border border-zinc-700">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h3 class="text-lg font-bold text-white">
                                    Application Pending Review
                                </h3>
                                <p class="text-xs font-medium mt-1 text-zinc-400">
                                    Submitted {{ $userApplication->created_at->format('M d, Y') }}
                                </p>
                                
                                <div class="inline-block mt-3 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-zinc-800 text-zinc-200 border border-zinc-700">
                                    Status: Pending Review
                                </div>

                                <div class="mt-4 p-4 rounded-2xl bg-zinc-900 text-left border border-zinc-800 max-w-lg mx-auto text-xs">
                                    <p class="font-bold text-white mb-1">Submitted Requirements:</p>
                                    <p class="text-zinc-300 whitespace-pre-line">{{ $userApplication->submitted_requirements }}</p>
                                </div>

                                <div class="mt-5">
                                    <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all">
                                        <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        Message Employer
                                    </a>
                                </div>
                            </div>
                        @elseif($userApplication && $userApplication->isFired())
                            <!-- Terminated / Fired View -->
                            <div class="text-center py-4 space-y-2">
                                <div class="w-12 h-12 mx-auto rounded-full bg-red-950/80 border border-red-800 text-red-400 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </div>
                                <h3 class="text-lg font-bold text-white">Position Ended</h3>
                                <p class="text-xs font-medium text-zinc-400 max-w-md mx-auto">
                                    Your engagement for this position was terminated by the employer. You cannot re-apply to this posting.
                                </p>
                            </div>
                        @elseif(auth()->user()->hasActiveJob())
                            <!-- Restriction: Worker already has an active hired job -->
                            <div class="p-6 rounded-3xl border border-amber-800/80 bg-amber-950/40 text-amber-200 space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-amber-500/20 border border-amber-600 text-amber-400 flex items-center justify-center shrink-0 text-lg shadow-sm">
                                        ⚠️
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-white">
                                            Application Restricted (Active Job)
                                        </h3>
                                        <p class="text-xs font-medium text-amber-300 mt-1">
                                            You are currently hired for <strong>{{ auth()->user()->activeJobApplication()?->jobPost?->title }}</strong> at <strong>{{ auth()->user()->activeJobApplication()?->jobPost?->employer?->display_name }}</strong>.
                                        </p>
                                    </div>
                                </div>
                                <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                    Workers holding an active job cannot apply for other positions. To apply for a new opportunity, you must first request to leave your current position and receive confirmation from your employer.
                                </p>
                                <div class="pt-1">
                                    <a href="{{ route('worker.applications') }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all">
                                        <span>Manage Active Job in Applications</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </div>
                            </div>
                        @elseif($jobPost->isLocked())
                            <div class="text-center py-6 text-zinc-300">
                                <h3 class="text-lg font-bold text-white">Applications Closed</h3>
                                <p class="text-xs font-medium text-zinc-400 mt-1">This job has hired all {{ $jobPost->workers_needed }} workers and is locked.</p>
                            </div>
                        @else
                            <!-- Apply Form with Requirement Photo & Camera Selfie -->
                            <div class="border-b border-zinc-800 pb-3 mb-4">
                                <h3 class="text-lg font-bold text-white">
                                    Apply for this Job
                                </h3>
                                <p class="text-xs font-medium text-zinc-400 mt-0.5">
                                    Submit your verified credentials and clear live selfie photo
                                </p>
                            </div>

                            <form method="POST" action="{{ route('worker.apply', $jobPost) }}" enctype="multipart/form-data" id="jobApplicationForm" class="space-y-4">
                                @csrf

                                <!-- Requirements Checklist / Details -->
                                <div>
                                    <label for="submitted_requirements" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                                        Requirements Checklist & Statement <span class="text-red-400">*</span>
                                    </label>
                                    <textarea id="submitted_requirements" name="submitted_requirements" rows="3" required
                                        placeholder="Confirm which required documents you are submitting (e.g. Valid Gov't ID, Barangay Clearance, NC II Certificate)..."
                                        class="w-full px-4 py-3 rounded-2xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-500 text-xs font-semibold focus:outline-none focus:border-zinc-500">{{ old('submitted_requirements') }}</textarea>
                                </div>

                                <!-- 1. Clear Photo of Requirements / Documents (Mandatory) -->
                                <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-800 space-y-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <label for="requirements_photo" class="block text-xs font-bold uppercase tracking-wider text-white">
                                                1. Clear Photo of Requirements <span class="text-red-400">*</span>
                                            </label>
                                            <p class="text-[11px] font-medium text-zinc-400">
                                                Upload a clear, legible photo or scan of your ID or required certificate.
                                            </p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-zinc-800 text-zinc-300 border border-zinc-700">
                                            Required
                                        </span>
                                    </div>

                                    <input type="file" id="requirements_photo" name="requirements_photo" accept="image/jpeg,image/png,image/webp,application/pdf" required
                                        onchange="previewRequirementPhoto(this)"
                                        class="w-full px-3 py-2 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-white file:text-black hover:file:bg-zinc-200 cursor-pointer">

                                    <!-- Image Preview Box -->
                                    <div id="requirementPreviewBox" class="hidden mt-2 p-2 rounded-xl bg-zinc-850 border border-zinc-750 flex items-center gap-3">
                                        <img id="requirementPreviewImg" src="" alt="Requirement Preview" class="w-14 h-14 object-cover rounded-lg border border-zinc-700">
                                        <div class="flex-1 min-w-0 text-xs">
                                            <p id="requirementFileName" class="font-bold text-white truncate">document.jpg</p>
                                            <p class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Photo ready for verification
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Live Selfie Using Camera (Mandatory) -->
                                <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-800 space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-white">
                                                2. Worker Selfie (Camera Verification) <span class="text-red-400">*</span>
                                            </label>
                                            <p class="text-[11px] font-medium text-zinc-400">
                                                Take a live selfie right now using your camera to confirm applicant identity.
                                            </p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-zinc-800 text-zinc-300 border border-zinc-700">
                                            Live Camera
                                        </span>
                                    </div>

                                    <!-- Camera Interface Box -->
                                    <div class="rounded-xl border border-zinc-800 bg-[#141418] p-3 space-y-3 text-center">
                                        
                                        <!-- Initial State: Open Camera Button -->
                                        <div id="cameraPrompt">
                                            <button type="button" onclick="startSelfieCamera()"
                                                class="w-full py-2.5 px-4 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md flex items-center justify-center gap-2 transition-all hover:scale-[1.01]">
                                                <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>Open Camera & Take Selfie</span>
                                            </button>
                                        </div>

                                        <!-- Camera Streaming View (Hidden initially) -->
                                        <div id="cameraStreamContainer" class="hidden space-y-2">
                                            <div class="relative w-full max-w-xs mx-auto overflow-hidden rounded-xl bg-black border-2 border-zinc-500 aspect-[4/3]">
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
                                                    class="py-2 px-3.5 rounded-xl border border-zinc-700 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-xs">
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
                                                    class="py-1.5 px-3.5 rounded-xl border border-zinc-700 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold text-xs flex items-center gap-1 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                    Retake Selfie
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Fallback File Upload (If camera denied or unsupported) -->
                                        <div class="pt-2 border-t border-zinc-800 text-left">
                                            <p class="text-[10px] font-bold text-zinc-400 uppercase mb-1">Camera not working on your device?</p>
                                            <input type="file" id="selfie_file" name="selfie_file" accept="image/*" capture="user"
                                                onchange="handleSelfieFileFallback(this)"
                                                class="w-full px-2.5 py-1.5 rounded-lg bg-zinc-900 border border-zinc-700 text-zinc-300 text-xs font-semibold file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-bold file:bg-white file:text-black">
                                        </div>

                                    </div>

                                    <!-- Hidden Input with Base64 Captured Selfie -->
                                    <input type="hidden" name="selfie_data" id="selfieData" value="">
                                    <p id="selfieErrorMsg" class="text-xs font-bold text-red-400 hidden">
                                        * Please take a live selfie with your camera (or upload a selfie file) before submitting.
                                    </p>
                                </div>

                                <!-- Optional Message -->
                                <div>
                                    <label for="cover_letter" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                                        Message to Employer (Optional)
                                    </label>
                                    <textarea id="cover_letter" name="cover_letter" rows="2"
                                        placeholder="Add an optional message or introduction..."
                                        class="w-full px-4 py-2.5 rounded-2xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-500 text-xs font-semibold focus:outline-none focus:border-zinc-500">{{ old('cover_letter') }}</textarea>
                                </div>

                                <button type="submit"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-white hover:bg-zinc-200 text-black font-extrabold text-sm shadow-md transition-all hover:scale-[1.01]">
                                    Submit Application with Verified Selfie
                                </button>
                            </form>
                        @endif

                    </div>
                @elseif(auth()->user()->isEmployer())
                    <!-- Employer Viewing Post Area -->
                    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-4">
                        @if($jobPost->employer_id === auth()->id())
                            <!-- Owner Employer -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-zinc-800 text-white flex items-center justify-center font-bold border border-zinc-700">
                                    📌
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-white">You Created This Job Post</h3>
                                    <p class="text-xs font-medium text-zinc-400">
                                        Quota: <strong>{{ $jobPost->workers_hired }}/{{ $jobPost->workers_needed }}</strong> Hired • Status: <strong class="capitalize">{{ $jobPost->status }}</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <a href="{{ route('employer.posts.applicants', $jobPost) }}"
                                    class="px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md flex items-center gap-1.5 transition-all hover:scale-[1.01]">
                                    <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    <span>Manage Applicants ({{ $jobPost->applications->count() }})</span>
                                </a>
                                <a href="{{ route('employer.posts.edit', $jobPost) }}"
                                    class="px-4 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-1.5 transition-colors">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span>Edit Post</span>
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
                                    class="px-4 py-2.5 rounded-xl border border-red-800/60 bg-red-950/40 hover:bg-red-900/60 text-red-300 font-bold text-xs flex items-center gap-1.5 transition-colors">
                                    <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    <span>Delete Post</span>
                                </button>
                            </div>
                        @else
                            <!-- Other Employer Viewing This Post (Employer-to-Employer) -->
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-zinc-800 text-zinc-300 flex items-center justify-center font-bold text-base shrink-0 border border-zinc-700">
                                    🏢
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-white">Employer-to-Employer Overview</h3>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-800 text-zinc-300 border border-zinc-700">Browse Mode</span>
                                    </div>
                                    <p class="text-xs font-medium text-zinc-400 leading-relaxed">
                                        Employers cannot apply for other employers' job posts. You can explore all details, requirements, workplace location, and directly message this employer for collaboration or hiring coordination.
                                    </p>
                                </div>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all hover:scale-[1.02]">
                                    <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    Direct Message Employer
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <!-- Guest State: Cannot Apply Without Signing In -->
                <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl text-center space-y-4">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-zinc-800 text-zinc-200 flex items-center justify-center border border-zinc-700 shadow-inner">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    <div class="space-y-1.5 max-w-md mx-auto">
                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-zinc-800 text-zinc-300 border border-zinc-700">
                            Authentication Required
                        </span>
                        <h3 class="text-xl font-bold text-white">
                            Sign In to Apply for this Job
                        </h3>
                        <p class="text-xs font-medium text-zinc-400 leading-relaxed">
                            You are previewing this job posting as a guest. To protect workers and employers from spam and fraud, you must be signed in with a <strong>Worker account</strong> and submit a live camera selfie to apply.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2 max-w-sm mx-auto">
                        <a href="{{ route('login') }}" 
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                            <span>Sign In to Apply</span>
                            <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </a>
                        <a href="{{ route('register', ['role' => 'worker']) }}" 
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-xs transition-colors text-center">
                            Create Worker Account
                        </a>
                    </div>
                </div>
            @endauth

        </div>

        <!-- Right Column: Location Map & Employer Card (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Workplace Location Map Pin Card -->
            <div class="p-5 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Location Map
                </h3>

                <!-- Leaflet Location View Map -->
                <div id="jobDetailMap" class="w-full h-56 rounded-2xl border border-zinc-800 z-0 bg-[#09090b]"></div>

                <div class="p-3 bg-zinc-900 rounded-xl border border-zinc-800 text-xs font-medium text-zinc-300">
                    <p class="font-bold text-white mb-0.5">Address:</p>
                    <p class="text-zinc-400">{{ $jobPost->location_address }}</p>
                </div>
            </div>

            <!-- Employer Details Card -->
            <div class="p-5 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-4">
                <h3 class="text-sm font-bold text-white">Employer Details</h3>
                
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-zinc-800 text-white font-bold flex items-center justify-center text-base shadow-sm border border-zinc-700">
                        {{ strtoupper(substr($jobPost->employer?->display_name ?? 'E', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white">{{ $jobPost->employer?->display_name }}</h4>
                        <p class="text-xs text-zinc-400 font-medium">{{ $jobPost->employer?->address ?? 'Philippines' }}</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-800 space-y-1.5 text-xs font-medium text-zinc-300">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-400">Contact:</span>
                        <span class="font-bold text-white">{{ $jobPost->employer?->contact_number ?? 'Available on file' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-400">Email:</span>
                        <span class="font-bold text-white">{{ $jobPost->employer?->email }}</span>
                    </div>
                </div>

                @auth
                    @if(auth()->id() !== $jobPost->employer_id)
                        <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                            class="w-full py-2.5 px-4 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            Message Employer
                        </a>
                    @else
                        <!-- Employer Owner Quick Actions -->
                        <div class="space-y-2 pt-2 border-t border-zinc-800">
                            <a href="{{ route('employer.posts.applicants', $jobPost) }}"
                                class="w-full py-2.5 px-3 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-sm transition-colors">
                                View Applicants ({{ $jobPost->applications->count() }})
                            </a>
                            <a href="{{ route('employer.posts.edit', $jobPost) }}"
                                class="w-full py-2 px-3 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                <span>Edit Post</span>
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
                                class="w-full py-2 px-3 rounded-xl border border-red-800/60 bg-red-950/40 hover:bg-red-900/60 text-red-300 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                <span>Delete Post</span>
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
        const previewBox = document.getElementById('requirementPreviewBox');
        const previewImg = document.getElementById('requirementPreviewImg');
        const fileName = document.getElementById('requirementFileName');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            fileName.textContent = file.name;
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                // PDF or non-image
                previewImg.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23ffffff'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'/%3E%3C/svg%3E";
            }
            previewBox.classList.remove('hidden');
        }
    }

    async function startSelfieCamera() {
        const prompt = document.getElementById('cameraPrompt');
        const streamContainer = document.getElementById('cameraStreamContainer');
        const previewContainer = document.getElementById('selfiePreviewContainer');
        const video = document.getElementById('selfieVideo');
        const errorMsg = document.getElementById('selfieErrorMsg');

        try {
            selfieStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = selfieStream;
            prompt.classList.add('hidden');
            previewContainer.classList.add('hidden');
            streamContainer.classList.remove('hidden');
            errorMsg.classList.add('hidden');
        } catch (err) {
            console.error("Camera access error:", err);
            alert("Camera access was not granted or is unsupported. Please use the file upload option below to upload your selfie.");
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

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        hiddenInput.value = dataUrl;
        previewImg.src = dataUrl;

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

        // 100% Free OpenStreetMap standard tiles (Zero API Key required)
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors | Findjobph',
            maxZoom: 19
        }).addTo(map);

        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 200);

        const pinIcon = L.divIcon({
            className: 'custom-pin-detail',
            html: '<div style="background-color: #10b981; width: 30px; height: 30px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.5); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:16px;height:16px;margin-top:4px;margin-left:4px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        });

        L.marker([lat, lng], { icon: pinIcon })
            .addTo(map)
            .bindPopup("<b style='color:#ffffff;'>{{ addslashes($jobPost->title) }}</b><br><span style='color:#d4d4d8;'>{{ addslashes($jobPost->location_address) }}</span>")
            .openPopup();
    });
</script>
@endpush
@endsection
