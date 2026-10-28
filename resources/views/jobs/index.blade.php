@extends('layouts.app')

@section('content')
<div class="space-y-6 text-zinc-100">

    <!-- Navigation Breadcrumb -->
    <div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Home
        </a>
    </div>

    <!-- Attracting Header Section (Dark Mode) -->
    <div class="text-center pt-1 pb-1 space-y-2.5">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-zinc-900 text-zinc-300 border border-zinc-800 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Verified Work Across the Philippines</span>
        </div>
        
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-tight">
            Find Your Next Job. <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-zinc-200 to-zinc-400">Start Today.</span>
        </h1>
        
        <p class="text-xs sm:text-sm md:text-base font-medium text-zinc-400 max-w-xl mx-auto leading-relaxed">
            Discover verified jobs with workplace map pins, transparent salaries, and direct employer hiring.
        </p>
    </div>

    <!-- Guest Alert Notice -->
    @guest
        <div class="p-4 rounded-2xl bg-[#121215] border border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-zinc-800 text-zinc-200 flex items-center justify-center shrink-0 border border-zinc-700 shadow-sm">
                    <svg class="w-5 h-5 text-zinc-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-white">Browsing Jobs in Guest Mode</p>
                    <p class="text-[11px] font-medium text-zinc-400">You can view all job posts, salaries, and map coordinates freely. Sign in with a Worker account when ready to apply.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-300 hover:text-white hover:bg-zinc-800 text-xs font-bold transition-colors">
                    Sign In
                </a>
                <a href="{{ route('register', ['role' => 'worker']) }}" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-zinc-200 text-black text-xs font-extrabold shadow-sm transition-all">
                    Sign Up as Worker
                </a>
            </div>
        </div>
    @endguest

    <!-- Clean, Compact Search & Filter Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-[#121215] border border-zinc-800 shadow-sm">
        <form method="GET" action="{{ route('jobs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            
            <!-- Search Keyword -->
            <div class="md:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search jobs by title or location..."
                    class="w-full px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-500 text-xs font-semibold focus:outline-none focus:border-zinc-500">
            </div>

            <!-- Category -->
            <div class="md:col-span-3">
                <select name="category" class="w-full px-3 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-xs font-semibold focus:outline-none focus:border-zinc-500">
                    <option value="" class="bg-zinc-900 text-white">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }} class="bg-zinc-900 text-white">
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Availability -->
            <div class="md:col-span-2">
                <select name="availability" class="w-full px-3 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-xs font-semibold focus:outline-none focus:border-zinc-500">
                    <option value="open" {{ request('availability', 'open') == 'open' ? 'selected' : '' }} class="bg-zinc-900 text-white">Open Only</option>
                    <option value="all" {{ request('availability') == 'all' ? 'selected' : '' }} class="bg-zinc-900 text-white">All Jobs</option>
                    <option value="locked" {{ request('availability') == 'locked' ? 'selected' : '' }} class="bg-zinc-900 text-white">Locked Only</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-sm transition-all">
                    Search
                </button>
                @if(request()->hasAny(['search', 'category', 'location', 'job_type', 'availability', 'sort']))
                    <a href="{{ route('jobs.index') }}" title="Reset"
                        class="p-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 text-xs font-bold transition-colors">
                        Clear
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Job Cards Grid -->
    @if($jobs->isEmpty())
        <div class="p-12 text-center rounded-2xl bg-[#121215] border border-zinc-800">
            <h3 class="text-base font-bold text-white">No jobs found</h3>
            <p class="text-xs text-zinc-400 mt-1">Try adjusting your search filters.</p>
            <a href="{{ route('jobs.index') }}" class="mt-4 inline-block px-4 py-2 rounded-xl bg-white text-black hover:bg-zinc-200 text-xs font-extrabold">
                Show All Jobs
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($jobs as $job)
                <div class="bg-[#121215] p-5 rounded-2xl flex flex-col justify-between border border-zinc-800 hover:border-zinc-700 transition-colors">
                    
                    <div class="space-y-2.5">
                        <!-- Category & Status -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[11px] font-bold text-zinc-300 bg-zinc-800 border border-zinc-700 px-2 py-0.5 rounded">
                                    {{ $job->category }}
                                </span>
                                @auth
                                    @if(auth()->user()->isEmployer() && $job->employer_id === auth()->id())
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-zinc-800 text-white border border-zinc-600 shadow-xs">
                                            Your Post
                                        </span>
                                    @endif
                                @endauth
                            </div>

                            @if($job->isLocked())
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-amber-950/80 text-amber-300 border border-amber-800/80">
                                    Locked
                                </span>
                            @else
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-950/80 text-emerald-300 border border-emerald-800/80">
                                    Open
                                </span>
                            @endif
                        </div>

                        <!-- Job Title -->
                        <h3 class="text-base font-bold text-white hover:text-zinc-300 transition-colors line-clamp-2 leading-snug">
                            <a href="{{ route('jobs.show', $job) }}">
                                {{ $job->title }}
                            </a>
                        </h3>

                        <!-- Employer Name -->
                        <p class="text-xs font-medium text-zinc-400">
                            {{ $job->employer?->display_name ?? 'Employer' }}
                        </p>

                        <!-- Location -->
                        <p class="text-xs text-zinc-300 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ $job->location_address }}</span>
                        </p>

                        <!-- Salary & Job Type -->
                        <div class="flex items-center justify-between text-xs font-bold pt-1">
                            <span class="text-emerald-400">
                                {{ $job->salary_range ?? 'Competitive Pay' }}
                            </span>
                            <span class="text-zinc-400 font-medium">
                                {{ $job->job_type }}
                            </span>
                        </div>

                        <!-- Capacity Bar -->
                        <div class="pt-2 border-t border-zinc-800">
                            <div class="flex items-center justify-between text-xs font-bold text-zinc-300 mb-1">
                                <span>Workers:</span>
                                <span class="{{ $job->isLocked() ? 'text-amber-400' : 'text-zinc-200' }}">
                                    {{ $job->workers_hired }} / {{ $job->workers_needed }} Hired
                                </span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-zinc-800 overflow-hidden">
                                <div class="h-full {{ $job->isLocked() ? 'bg-amber-400' : 'bg-white' }}"
                                    style="width: {{ $job->capacity_percentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons: Manage/Edit/Delete for Owner Employer, or Details/Apply for Others -->
                    <div class="mt-4 pt-3 border-t border-zinc-800">
                        @if(auth()->check() && auth()->user()->isEmployer() && $job->employer_id === auth()->id())
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('jobs.show', $job) }}"
                                    class="flex-1 min-w-[70px] py-2 px-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs text-center shadow-sm transition-all hover:scale-[1.01]">
                                    Manage
                                </a>
                                <a href="{{ route('employer.posts.edit', $job) }}"
                                    class="py-2 px-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-1 transition-colors"
                                    title="Edit Post">
                                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span>Edit</span>
                                </a>
                                <button type="button"
                                    onclick="confirmAction({
                                        title: 'Delete Job Post Permanently?',
                                        message: 'Are you sure you want to permanently delete \'{{ addslashes($job->title) }}\'? All associated worker applications will be removed. This cannot be undone.',
                                        formAction: '{{ route('employer.posts.destroy', $job) }}',
                                        method: 'DELETE',
                                        confirmText: 'Yes, Delete Post',
                                        confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                    })"
                                    class="py-2 px-2.5 rounded-xl border border-red-800/60 bg-red-950/40 hover:bg-red-900/60 text-red-300 font-bold text-xs flex items-center gap-1 transition-colors"
                                    title="Delete Post">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    <span>Delete</span>
                                </button>
                            </div>
                        @elseif(auth()->check() && auth()->user()->isEmployer())
                            <a href="{{ route('jobs.show', $job) }}"
                                class="w-full py-2.5 px-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold text-xs flex items-center justify-center transition-colors">
                                View Details
                            </a>
                        @elseif(auth()->check() && auth()->user()->isWorker())
                            <a href="{{ route('jobs.show', $job) }}"
                                class="w-full py-2.5 px-3 rounded-xl {{ $job->isLocked() ? 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700' : 'bg-white hover:bg-zinc-200 text-black' }} font-extrabold text-xs flex items-center justify-center gap-1.5 transition-all hover:scale-[1.01]">
                                <span>{{ $job->isLocked() ? 'View Details (Locked)' : 'View Details & Apply' }}</span>
                                @if(!$job->isLocked())
                                    <svg class="w-3.5 h-3.5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                @endif
                            </a>
                        @else
                            {{-- Guest state: cannot apply without signing in --}}
                            <a href="{{ route('jobs.show', $job) }}"
                                class="w-full py-2.5 px-3 rounded-xl {{ $job->isLocked() ? 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700' : 'bg-white hover:bg-zinc-200 text-black' }} font-extrabold text-xs flex items-center justify-center gap-1.5 transition-all hover:scale-[1.01]">
                                <span>{{ $job->isLocked() ? 'View Details (Locked)' : 'View Details (Sign in to Apply)' }}</span>
                                @if(!$job->isLocked())
                                    <svg class="w-3.5 h-3.5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                @endif
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    @endif

</div>
@endsection
