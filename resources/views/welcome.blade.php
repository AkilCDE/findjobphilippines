@extends('layouts.app')

@section('content')

<!-- Fullscreen Fixed Background Picture (Covers Entire Screen / Viewport) -->
<div class="fixed inset-0 z-0 pointer-events-none select-none overflow-hidden" aria-hidden="true">
    <img src="{{ asset('images/landing-bg.jpg') }}" alt="FindJobPH Fullscreen Background"
        class="w-full h-full object-cover object-center filter blur-[2px] brightness-[0.35] contrast-[1.15] scale-105">
    <!-- Ambient dark gradient overlay to ensure 100% text legibility on all devices -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-[#09090b]/50 to-[#09090b]/90"></div>
</div>

<!-- Main Landing Page Content (Always in Front of Fullscreen Background) -->
<div class="relative z-10 space-y-16 sm:space-y-24 py-4 sm:py-8 text-zinc-100">

    <!-- ========================================== -->
    <!-- HERO SECTION: CENTERED BROWSE BUTTON -->
    <!-- ========================================== -->
    <section class="relative text-center max-w-4xl mx-auto px-5 sm:px-10 py-10 sm:py-14 space-y-6 z-10 bg-[#0c0d12]/85 backdrop-blur-md rounded-3xl border border-zinc-800 shadow-2xl">
        
        <!-- Trust Eyebrow Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-zinc-900/95 text-zinc-200 border border-zinc-700 shadow-sm animate-pulse">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            <span>🇵🇭 100% Direct Philippine Hiring • Map-Verified Workplaces • Zero Placement Fees</span>
        </div>

        <!-- Hero Headline -->
        <h1 class="text-3xl sm:text-5xl md:text-6xl font-black text-white tracking-tight leading-[1.15] drop-shadow-md">
            Connecting Filipino Workers <br class="hidden sm:inline" />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-zinc-200 to-zinc-400">
                Directly to Real Employers
            </span>
        </h1>

        <!-- Engaging Subtitle -->
        <p class="text-sm sm:text-base md:text-lg font-medium text-zinc-200 max-w-2xl mx-auto leading-relaxed drop-shadow-sm">
            Discover verified jobs across the Philippines with exact pinned workplace locations, transparent salaries, and honest hiring quotas. No agency fees, no middleman cuts.
        </p>

        <!-- ============================================================== -->
        <!-- CENTERED BROWSE JOBS BUTTON (STANDARD BUTTON SIZING) -->
        <!-- ============================================================== -->
        <div class="pt-2 pb-1 space-y-3">
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('jobs.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-sm shadow-md transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Browse All Jobs</span>
                    <span class="text-xs bg-black/10 text-black px-2 py-0.5 rounded-md font-bold">
                        {{ $openJobsCount }} Open
                    </span>
                    <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>

                <a href="{{ route('jobs.map') }}" 
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900/90 hover:bg-zinc-800 text-zinc-100 font-bold text-sm shadow-sm transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>View Interactive Philippine Map</span>
                </a>
            </div>

            <p class="text-xs font-semibold text-zinc-400">
                🔒 Open to browse for everyone • Sign in required to apply
            </p>
        </div>

    </section>

    <!-- ========================================== -->
    <!-- ATTRACTING FEATURES: WHY FINDJOBPH -->
    <!-- ========================================== -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-300 bg-zinc-900/90 px-3 py-1 rounded-full border border-zinc-700">
                Authentic & Transparent
            </span>
            <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight drop-shadow-md">
                Why Filipino Workers & Employers Choose Findjobph
            </h2>
            <p class="text-xs sm:text-sm font-medium text-zinc-300">
                Built specifically for direct local employment without agency exploitation.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            
            <!-- Feature 1: Exact Workplace Map Pins -->
            <div class="bg-[#121215]/95 backdrop-blur-md p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 space-y-3 transition-colors shadow-xl relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-white">Pinned Workplace GPS</h3>
                <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                    Never worry about misleading job locations. Every job has an exact pinned coordinate on our interactive Philippine map, allowing you to compute your commute before applying.
                </p>
            </div>

            <!-- Feature 2: Anti-Fraud Live Camera Selfie -->
            <div class="bg-[#121215]/95 backdrop-blur-md p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 space-y-3 transition-colors shadow-xl relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-white">Verified Identity & Selfies</h3>
                <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                    Zero fake applicants, zero scam listings. Our mandatory live camera snapshot and document verification protect both workers and authentic direct employers.
                </p>
            </div>

            <!-- Feature 3: Honest Capacity Auto-Lock -->
            <div class="bg-[#121215]/95 backdrop-blur-md p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 space-y-3 transition-colors shadow-xl relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-white">Real-Time Capacity Lock</h3>
                <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                    Transparent hiring quotas! You can see exactly how many slots remain (e.g. 2 of 5 hired). Once the employer fulfills their quota, the job post automatically locks.
                </p>
            </div>

            <!-- Feature 4: Direct Employer Chat -->
            <div class="bg-[#121215]/95 backdrop-blur-md p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 space-y-3 transition-colors shadow-xl relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-white">Direct Messaging</h3>
                <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                    Communicate directly with the hiring business or employer. Inquire about schedules, job requirements, and updates through our fast, integrated chat.
                </p>
            </div>

            <!-- Feature 5: Zero Salary Deductions -->
            <div class="bg-[#121215]/95 backdrop-blur-md p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 space-y-3 transition-colors shadow-xl relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-white">Keep 100% of Your Wage</h3>
                <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                    Findjobph takes zero commission or salary deductions. Every peso stated in the job post salary range goes straight from the employer to the worker.
                </p>
            </div>

            <!-- Feature 6: Island-Wide Opportunities -->
            <div class="bg-[#121215]/95 backdrop-blur-md p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 space-y-3 transition-colors shadow-xl relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-white">Luzon, Visayas & Mindanao</h3>
                <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                    Whether you are in Bohol, Cebu, Davao, or Metro Manila, search local jobs or explore regional opportunities across all Philippine island provinces.
                </p>
            </div>

        </div>

    </section>

    <!-- ========================================== -->
    <!-- POPULAR CATEGORIES SHORTCUTS -->
    <!-- ========================================== -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="bg-[#121215]/95 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-zinc-800 shadow-xl space-y-6 relative z-10">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-800 pb-4">
                <div>
                    <h2 class="text-lg sm:text-2xl font-black text-white">
                        Explore Popular Job Categories
                    </h2>
                    <p class="text-xs font-medium text-zinc-300 mt-0.5">
                        Click any category to jump directly to open listings in our Jobs feed
                    </p>
                </div>
                
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-200 hover:text-white transition-colors">
                    <span>View All Categories</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($categories as $catName => $meta)
                    <a href="{{ route('jobs.index', ['category' => $catName]) }}"
                        class="p-4 rounded-2xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-750 flex items-center justify-between group transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-300 group-hover:bg-white group-hover:text-black flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white group-hover:text-zinc-100 transition-colors">
                                    {{ $catName }}
                                </h4>
                                <p class="text-[11px] font-medium text-zinc-300 line-clamp-1">
                                    {{ $meta['desc'] }}
                                </p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-zinc-400 group-hover:text-white transform group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ========================================== -->
    <!-- BOTTOM TUTORIAL: HOW TO USE FINDJOBPH -->
    <!-- ========================================== -->
    <section id="tutorial" class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-400 bg-zinc-900 px-3 py-1 rounded-full border border-zinc-800">
                Step-by-Step Guide
            </span>
            <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                How to Use Findjobph
            </h2>
            <p class="text-xs sm:text-sm font-medium text-zinc-400">
                A simple walkthrough for Job Seekers (Workers) and Direct Employers.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Path A: For Job Seekers / Workers -->
            <div class="bg-[#121215]/95 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-zinc-800 space-y-6 shadow-xl relative z-10">
                
                <div class="flex items-center gap-3 border-b border-zinc-800 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-zinc-800 text-white flex items-center justify-center font-black text-lg shadow-sm">
                        👷
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-white">For Job Seekers (Workers)</h3>
                        <p class="text-xs font-medium text-zinc-300">How to find work, verify your identity, and get hired</p>
                    </div>
                </div>

                <ol class="space-y-5">
                    
                    <!-- Step 1 -->
                    <li class="flex items-start gap-4">
                        <span class="w-8 h-8 rounded-xl bg-zinc-800 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5 border border-zinc-700">
                            1
                        </span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">Browse Open Jobs & Location Map</h4>
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                Click the prominent <strong>Browse Jobs</strong> button to see all active job listings, or switch to the <strong>Philippine Map</strong> to view pinned workplaces near your home. You do not need an account to browse!
                            </p>
                        </div>
                    </li>

                    <!-- Step 2 -->
                    <li class="flex items-start gap-4">
                        <span class="w-8 h-8 rounded-xl bg-zinc-800 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5 border border-zinc-700">
                            2
                        </span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">Sign In or Create a Worker Account</h4>
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                When you find a position you like, you must sign in to apply. If you don't have an account yet, tap <strong>Sign Up</strong> and register as a Worker with your name and contact details.
                            </p>
                        </div>
                    </li>

                    <!-- Step 3 -->
                    <li class="flex items-start gap-4">
                        <span class="w-8 h-8 rounded-xl bg-zinc-800 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5 border border-zinc-700">
                            3
                        </span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">Submit Requirements & Live Camera Selfie</h4>
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                In the application section, upload photos of your valid ID or certifications. Then, take a quick <strong>live selfie snapshot</strong> with your phone or webcam to verify your authentic identity.
                            </p>
                        </div>
                    </li>

                    <!-- Step 4 -->
                    <li class="flex items-start gap-4">
                        <span class="w-8 h-8 rounded-xl bg-zinc-800 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5 border border-zinc-700">
                            4
                        </span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">Direct Messaging & Start Working</h4>
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                Track your application status live under <strong>Applications</strong>. Chat directly with your prospective employer via our integrated messaging system and begin working!
                            </p>
                        </div>
                    </li>

                </ol>

                <div class="pt-2 border-t border-zinc-800">
                    <a href="{{ route('jobs.index') }}"
                        class="w-full py-3 px-4 rounded-2xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs text-center flex items-center justify-center gap-2 shadow-sm transition-colors">
                        <span>Browse Jobs as Worker</span>
                        <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

            </div>

            <!-- Path B: For Direct Employers -->
            <div class="bg-[#121215]/95 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-zinc-800 space-y-6 shadow-xl relative z-10">
                
                <div class="flex items-center gap-3 border-b border-zinc-800 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-zinc-800 text-white flex items-center justify-center font-black text-lg shadow-sm">
                        🏢
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-white">For Direct Employers</h3>
                        <p class="text-xs font-medium text-zinc-300">How to post jobs, verify applicants, and fill quotas</p>
                    </div>
                </div>

                <ol class="space-y-5">
                    
                    <!-- Step 1 -->
                    <li class="flex items-start gap-4">
                        <span class="w-8 h-8 rounded-xl bg-zinc-800 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5 border border-zinc-700">
                            1
                        </span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">Post Your Opening & Pin Workplace Location</h4>
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                Sign up as an Employer and click <strong>Post a Job</strong>. Specify the salary, required worker count, and click on the interactive map to pinpoint your exact job site.
                            </p>
                        </div>
                    </li>

                    <!-- Step 2 -->
                    <li class="flex items-start gap-4">
                        <span class="w-8 h-8 rounded-xl bg-zinc-800 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5 border border-zinc-700">
                            2
                        </span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">Review Verified Applicants with Photos</h4>
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                Access your <strong>Applicants</strong> page to review submitted documents and live selfies side-by-side. No bots, fake accounts, or spam applications.
                            </p>
                        </div>
                    </li>

                    <!-- Step 3 -->
                    <li class="flex items-start gap-4">
                        <span class="w-8 h-8 rounded-xl bg-zinc-800 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5 border border-zinc-700">
                            3
                        </span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">One-Click Hire & Automatic Quota Lock</h4>
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                Click <strong>Hire</strong> to onboard your selected workers. Once all needed slots are filled, the job post automatically locks to prevent unnecessary applications.
                            </p>
                        </div>
                    </li>

                    <!-- Step 4 -->
                    <li class="flex items-start gap-4">
                        <span class="w-8 h-8 rounded-xl bg-zinc-800 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5 border border-zinc-700">
                            4
                        </span>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white">Manage Hires & Mutual Leave Confirmations</h4>
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed">
                                Chat with hired workers, release workers when contracts finish, or confirm mutual resignation requests in full transparency.
                            </p>
                        </div>
                    </li>

                </ol>

                <div class="pt-2 border-t border-zinc-800">
                    <a href="{{ route('register', ['role' => 'employer']) }}"
                        class="w-full py-3 px-4 rounded-2xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-white font-extrabold text-xs text-center flex items-center justify-center gap-2 shadow-sm transition-colors">
                        <span>Post Jobs as Employer</span>
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

            </div>

        </div>

    </section>

    <!-- ========================================== -->
    <!-- FAQ SECTION (RESOLVING COMMON QUESTIONS) -->
    <!-- ========================================== -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 relative z-10">
        
        <div class="text-center mb-8 space-y-2">
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Frequently Asked Questions
            </h2>
            <p class="text-xs sm:text-sm font-medium text-zinc-300">
                Everything you need to know about browsing, applying, and hiring on Findjobph.
            </p>
        </div>

        <div class="space-y-4">
            
            <div class="bg-[#121215]/95 backdrop-blur-md p-5 rounded-2xl border border-zinc-800 shadow-md">
                <h4 class="text-sm font-bold text-white">
                    Can I browse jobs without signing in?
                </h4>
                <p class="text-xs font-medium text-zinc-300 mt-1.5 leading-relaxed">
                    Yes! Browsing the job catalog, searching categories, checking salary ranges, and viewing pinned workplace locations on the Philippine Map are completely free and open to everyone. You only need to sign in when you want to submit an application.
                </p>
            </div>

            <div class="bg-[#121215]/95 backdrop-blur-md p-5 rounded-2xl border border-zinc-800 shadow-md">
                <h4 class="text-sm font-bold text-white">
                    Why can't I apply if I am not signed in?
                </h4>
                <p class="text-xs font-medium text-zinc-300 mt-1.5 leading-relaxed">
                    To maintain high trust and safety, applications require a verified Worker account. This prevents spam, fake applications, and ensures employers receive legitimate submissions with verified contact info and anti-fraud selfies.
                </p>
            </div>

            <div class="bg-[#121215]/95 backdrop-blur-md p-5 rounded-2xl border border-zinc-800 shadow-md">
                <h4 class="text-sm font-bold text-white">
                    Why do you require a live camera selfie when applying?
                </h4>
                <p class="text-xs font-medium text-zinc-300 mt-1.5 leading-relaxed">
                    The live camera selfie snapshot ensures that the applicant is an authentic person matching their identity documents. It protects Filipino employers from bot spam and fraudulent credentials.
                </p>
            </div>

            <div class="bg-[#121215]/95 backdrop-blur-md p-5 rounded-2xl border border-zinc-800 shadow-md">
                <h4 class="text-sm font-bold text-white">
                    What does "Locked" status mean on a job post?
                </h4>
                <p class="text-xs font-medium text-zinc-300 mt-1.5 leading-relaxed">
                    Every employer specifies their required headcount (e.g. 5 workers needed). Once all positions have been filled, the post automatically locks. You will never waste time applying for a job that has already filled its quota!
                </p>
            </div>

        </div>

    </section>

    <!-- ========================================== -->
    <!-- FINAL CALL TO ACTION (BOTTOM CTA) -->
    <!-- ========================================== -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="relative overflow-hidden rounded-3xl bg-[#121215] border border-zinc-800 p-8 sm:p-12 text-white shadow-2xl text-center space-y-6">
            
            <div class="relative z-10 space-y-3 max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight text-white">
                    Ready to Land Your Next Job in the Philippines?
                </h2>
                <p class="text-xs sm:text-base font-medium text-zinc-400">
                    Explore real job opportunities near you with transparent pay and exact pinned locations. Free for all Filipino job seekers.
                </p>
            </div>

            <div class="relative z-10 flex flex-wrap items-center justify-center gap-3 pt-2">
                
                <a href="{{ route('jobs.index') }}"
                    class="px-5 py-2.5 rounded-xl bg-white text-black hover:bg-zinc-200 font-extrabold text-sm shadow-md transition-all">
                    Browse All Jobs Now →
                </a>

                @guest
                    <a href="{{ route('register', ['role' => 'worker']) }}"
                        class="px-5 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-sm transition-all">
                        Create Free Worker Account
                    </a>
                @endguest

            </div>

        </div>
    </section>

</div>
@endsection
