@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Back Navigation -->
    <div>
        <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-800 hover:text-sky-950 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Job Feed
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
                        <span class="text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-xl bg-sky-100 text-sky-900 border border-sky-200">
                            {{ $jobPost->category }}
                        </span>
                        <span class="text-xs font-bold px-3 py-1 rounded-xl bg-slate-100 text-slate-800 border border-slate-200">
                            {{ $jobPost->job_type }}
                        </span>

                        @if($jobPost->isLocked())
                            <span class="text-xs font-extrabold px-3 py-1 rounded-xl bg-amber-100 text-amber-900 border border-amber-200 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                Post Locked (100% Filled)
                            </span>
                        @elseif($jobPost->isArchived())
                            <span class="text-xs font-extrabold px-3 py-1 rounded-xl bg-slate-200 text-slate-800">
                                Archived
                            </span>
                        @else
                            <span class="text-xs font-extrabold px-3 py-1 rounded-xl bg-emerald-100 text-emerald-900 border border-emerald-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Actively Hiring Workers
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight leading-tight">
                        {{ $jobPost->title }}
                    </h1>

                    <!-- Employer Info -->
                    <div class="mt-3 flex flex-wrap items-center gap-4 text-xs font-bold text-slate-800">
                        <span class="flex items-center gap-1.5 text-sky-800">
                            <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            {{ $jobPost->employer?->display_name ?? 'Verified Employer' }}
                        </span>
                        <span>•</span>
                        <span>Posted {{ $jobPost->created_at->diffForHumans() }}</span>
                        <span>•</span>
                        <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                            {{ $jobPost->salary_range ?? 'Competitive Pay' }}
                        </span>
                    </div>
                </div>

                <!-- Worker Capacity Tracker Bar -->
                <div class="p-4 rounded-2xl bg-sky-50/80 border border-sky-200/80">
                    <div class="flex items-center justify-between text-xs font-extrabold mb-2">
                        <span class="text-slate-900">Worker Capacity Status:</span>
                        <span class="{{ $jobPost->isLocked() ? 'text-amber-800 font-black' : 'text-sky-900' }}">
                            {{ $jobPost->workers_hired }} of {{ $jobPost->workers_needed }} Workers Hired
                            ({{ $jobPost->remaining_slots }} slots remaining)
                        </span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-200 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $jobPost->isLocked() ? 'bg-amber-500' : 'bg-sky-500' }}"
                            style="width: {{ $jobPost->capacity_percentage }}%"></div>
                    </div>
                    @if($jobPost->isLocked())
                        <p class="text-[11px] font-bold text-amber-800 mt-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                            This job post has reached 100% capacity and is automatically locked. No further applications can be submitted.
                        </p>
                    @endif
                </div>

                <!-- Description -->
                <div>
                    <h2 class="text-base font-extrabold text-slate-950 mb-2">
                        Job Description
                    </h2>
                    <div class="text-sm font-semibold text-slate-800 leading-relaxed whitespace-pre-line bg-white/50 p-4 rounded-2xl border border-white">
                        {{ $jobPost->description }}
                    </div>
                </div>

                <!-- Requirements Checklist -->
                <div>
                    <h2 class="text-base font-extrabold text-slate-950 mb-2">
                        Job Requirements & Qualifications
                    </h2>
                    <div class="text-sm font-semibold text-slate-800 leading-relaxed whitespace-pre-line bg-sky-50/50 p-4 rounded-2xl border border-sky-100">
                        {{ $jobPost->requirements }}
                    </div>
                </div>

            </div>

            <!-- Worker Application Submission Container -->
            @auth
                @if(auth()->user()->isWorker())
                    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl" id="applySection">
                        
                        @if($userApplication)
                            <!-- Already Applied View -->
                            <div class="text-center py-4">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full {{ $userApplication->isHired() ? 'bg-emerald-100 text-emerald-600' : ($userApplication->isRejected() ? 'bg-red-100 text-red-600' : 'bg-sky-100 text-sky-600') }} flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <h3 class="text-lg font-black text-slate-950">
                                    You have applied for this position
                                </h3>
                                <p class="text-xs font-bold mt-1 text-slate-700">
                                    Submitted on {{ $userApplication->created_at->format('M d, Y') }}
                                </p>
                                
                                <div class="inline-block mt-3 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $userApplication->isHired() ? 'bg-emerald-500 text-white' : ($userApplication->isRejected() ? 'bg-red-500 text-white' : 'bg-sky-500 text-white') }}">
                                    Application Status: {{ $userApplication->status }}
                                </div>

                                <div class="mt-4 p-4 rounded-2xl bg-white/70 text-left border border-slate-200/60 max-w-lg mx-auto text-xs">
                                    <p class="font-bold text-slate-900 mb-1">Your Submitted Requirements:</p>
                                    <p class="text-slate-700 font-semibold whitespace-pre-line">{{ $userApplication->submitted_requirements }}</p>
                                </div>

                                <div class="mt-6">
                                    <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        Direct Message Employer
                                    </a>
                                </div>
                            </div>
                        @elseif($jobPost->isLocked())
                            <div class="text-center py-6 text-slate-800">
                                <h3 class="text-lg font-black text-slate-950">Applications Closed</h3>
                                <p class="text-xs font-semibold text-slate-700 mt-1">This job post has hired all {{ $jobPost->workers_needed }} requested workers and is currently locked.</p>
                            </div>
                        @else
                            <!-- Apply Form -->
                            <h3 class="text-lg font-black text-slate-950 mb-1">
                                Apply for this Work Opportunity
                            </h3>
                            <p class="text-xs font-semibold text-slate-700 mb-5">
                                Submit your requirements directly to {{ $jobPost->employer?->display_name }} based on the post qualifications.
                            </p>

                            <form method="POST" action="{{ route('worker.apply', $jobPost) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="submitted_requirements" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                                        Submitted Requirements Checklist & Qualifications <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="submitted_requirements" name="submitted_requirements" rows="4" required
                                        placeholder="List what documents or certifications you have ready (e.g. Valid ID, Barangay Clearance, Driver's License, TESDA Certificate, Years of Experience)..."
                                        class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">{{ old('submitted_requirements') }}</textarea>
                                </div>

                                <div>
                                    <label for="cover_letter" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                                        Cover Message / Note to Employer (Optional)
                                    </label>
                                    <textarea id="cover_letter" name="cover_letter" rows="2"
                                        placeholder="Add a polite message introducing yourself and your availability..."
                                        class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">{{ old('cover_letter') }}</textarea>
                                </div>

                                <div>
                                    <label for="attachment" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                                        Attach Resume or Documents (PDF, DOC, Images up to 5MB)
                                    </label>
                                    <input type="file" id="attachment" name="attachment"
                                        class="w-full px-4 py-2.5 rounded-2xl glass-input text-xs font-semibold file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600">
                                </div>

                                <button type="submit"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-lg shadow-sky-500/25 transition-all hover:scale-[1.01]">
                                    Submit Application & Requirements
                                </button>
                            </form>
                        @endif

                    </div>
                @endif
            @else
                <!-- Guest Prompt -->
                <div class="glass-panel p-6 rounded-3xl border border-sky-200 text-center space-y-3">
                    <h3 class="text-lg font-black text-slate-950">Want to apply for this job?</h3>
                    <p class="text-xs font-semibold text-slate-700">Sign in with your Worker account or create an account in seconds.</p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-md">
                            Sign In to Apply
                        </a>
                        <a href="{{ route('register', ['role' => 'worker']) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-bold text-xs">
                            Register as Worker
                        </a>
                    </div>
                </div>
            @endauth

        </div>

        <!-- Right Column: Location Map & Employer Card (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Workplace Location Map Pin Card -->
            <div class="glass-panel p-5 rounded-3xl border border-white/80 shadow-xl space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-950 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Pinned Workplace Map
                    </h3>
                    <span class="text-[10px] font-extrabold text-sky-800 bg-sky-100 px-2 py-0.5 rounded-full">
                        Philippines GPS
                    </span>
                </div>

                <!-- Leaflet Location View Map -->
                <div id="jobDetailMap" class="w-full h-56 rounded-2xl border border-sky-100 z-0"></div>

                <div class="p-3 bg-white/70 rounded-xl border border-slate-100 text-xs font-semibold text-slate-800 space-y-1">
                    <p class="font-bold text-slate-950">Address:</p>
                    <p class="text-slate-800">{{ $jobPost->location_address }}</p>
                    <p class="text-[11px] text-slate-600 font-mono pt-1">
                        Coordinates: {{ number_format($jobPost->latitude, 4) }}° N, {{ number_format($jobPost->longitude, 4) }}° E
                    </p>
                </div>
            </div>

            <!-- Employer Details Card -->
            <div class="glass-panel p-5 rounded-3xl border border-white/80 shadow-xl space-y-4">
                <h3 class="text-sm font-black text-slate-950">Employer Information</h3>
                
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-sky-500 text-white font-black flex items-center justify-center text-lg shadow-md shadow-sky-500/20">
                        {{ strtoupper(substr($jobPost->employer?->display_name ?? 'E', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-950">{{ $jobPost->employer?->display_name }}</h4>
                        <p class="text-[11px] text-slate-600 font-semibold">{{ $jobPost->employer?->address ?? 'Philippines' }}</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2 text-xs font-semibold text-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Contact:</span>
                        <span class="font-bold text-slate-900">{{ $jobPost->employer?->contact_number ?? 'Verified on file' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Verified Email:</span>
                        <span class="font-bold text-slate-900">{{ $jobPost->employer?->email }}</span>
                    </div>
                </div>

                @auth
                    @if(auth()->id() !== $jobPost->employer_id)
                        <a href="{{ route('messages.index', ['user_id' => $jobPost->employer_id, 'job_id' => $jobPost->id]) }}"
                            class="w-full py-2.5 px-4 rounded-xl border border-sky-300 bg-white hover:bg-sky-50 text-sky-800 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            Direct Message Employer
                        </a>
                    @else
                        <!-- Employer Owner Quick Actions -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <p class="text-[11px] font-extrabold text-sky-800 uppercase">You own this post</p>
                            <a href="{{ route('employer.posts.applicants', $jobPost) }}"
                                class="w-full py-2.5 px-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                                View & Hire Applicants ({{ $jobPost->applications->count() }})
                            </a>
                            <a href="{{ route('employer.posts.edit', $jobPost) }}"
                                class="w-full py-2 px-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs flex items-center justify-center">
                                Edit Post Details
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
            html: '<div style="background-color: #0284c7; width: 32px; height: 32px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.3); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:18px;height:18px;margin-top:4px;margin-left:4px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        L.marker([lat, lng], { icon: pinIcon })
            .addTo(map)
            .bindPopup("<b>{{ addslashes($jobPost->title) }}</b><br>{{ addslashes($jobPost->location_address) }}")
            .openPopup();
    });
</script>
@endpush
@endsection
