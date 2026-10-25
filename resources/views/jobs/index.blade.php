@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Attracting Header Section (Big & Small Text) -->
    <div class="text-center pt-2 pb-1 space-y-2.5">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-100 text-sky-900 border border-sky-200 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
            <span>Verified Work Across the Philippines</span>
        </div>
        
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-950 tracking-tight leading-tight">
            Find Your Next Job. <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-blue-600">Start Today.</span>
        </h1>
        
        <p class="text-xs sm:text-sm md:text-base font-semibold text-slate-600 max-w-xl mx-auto leading-relaxed">
            Discover verified jobs with workplace map pins, transparent salaries, and direct employer hiring.
        </p>
    </div>

    <!-- Clean, Compact Search & Filter Bar -->
    <div class="glass-panel p-4 sm:p-5 rounded-2xl border border-white/80 shadow-sm">
        <form method="GET" action="{{ route('jobs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            
            <!-- Search Keyword -->
            <div class="md:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search jobs by title or location..."
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-semibold">
            </div>

            <!-- Category -->
            <div class="md:col-span-3">
                <select name="category" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-semibold">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Availability -->
            <div class="md:col-span-2">
                <select name="availability" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-semibold">
                    <option value="open" {{ request('availability', 'open') == 'open' ? 'selected' : '' }}>Open Only</option>
                    <option value="all" {{ request('availability') == 'all' ? 'selected' : '' }}>All Jobs</option>
                    <option value="locked" {{ request('availability') == 'locked' ? 'selected' : '' }}>Locked Only</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-sm transition-all">
                    Search
                </button>
                @if(request()->hasAny(['search', 'category', 'location', 'job_type', 'availability', 'sort']))
                    <a href="{{ route('jobs.index') }}" title="Reset"
                        class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors">
                        Clear
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Job Cards Grid -->
    @if($jobs->isEmpty())
        <div class="glass-panel p-12 text-center rounded-2xl border border-white/80">
            <h3 class="text-base font-bold text-slate-900">No jobs found</h3>
            <p class="text-xs text-slate-600 mt-1">Try adjusting your search filters.</p>
            <a href="{{ route('jobs.index') }}" class="mt-4 inline-block px-4 py-2 rounded-xl bg-sky-500 text-white text-xs font-bold">
                Show All Jobs
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($jobs as $job)
                <div class="glass-card p-5 rounded-2xl flex flex-col justify-between border border-white/80">
                    
                    <div class="space-y-2.5">
                        <!-- Category & Status -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[11px] font-bold text-sky-800 bg-sky-50 px-2 py-0.5 rounded">
                                    {{ $job->category }}
                                </span>
                                @auth
                                    @if(auth()->user()->isEmployer() && $job->employer_id === auth()->id())
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded bg-sky-500 text-white shadow-xs">
                                            Your Post
                                        </span>
                                    @endif
                                @endauth
                            </div>

                            @if($job->isLocked())
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-900">
                                    Locked
                                </span>
                            @else
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-900">
                                    Open
                                </span>
                            @endif
                        </div>

                        <!-- Job Title -->
                        <h3 class="text-base font-black text-slate-950 hover:text-sky-700 transition-colors line-clamp-2 leading-snug">
                            <a href="{{ route('jobs.show', $job) }}">
                                {{ $job->title }}
                            </a>
                        </h3>

                        <!-- Employer Name -->
                        <p class="text-xs font-bold text-slate-700">
                            {{ $job->employer?->display_name ?? 'Employer' }}
                        </p>

                        <!-- Location -->
                        <p class="text-xs text-slate-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ $job->location_address }}</span>
                        </p>

                        <!-- Salary & Job Type -->
                        <div class="flex items-center justify-between text-xs font-bold pt-1">
                            <span class="text-emerald-700">
                                {{ $job->salary_range ?? 'Competitive Pay' }}
                            </span>
                            <span class="text-slate-500 font-semibold">
                                {{ $job->job_type }}
                            </span>
                        </div>

                        <!-- Capacity Bar -->
                        <div class="pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-800 mb-1">
                                <span>Workers:</span>
                                <span class="{{ $job->isLocked() ? 'text-amber-800' : 'text-sky-800' }}">
                                    {{ $job->workers_hired }} / {{ $job->workers_needed }} Hired
                                </span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full {{ $job->isLocked() ? 'bg-amber-500' : 'bg-sky-500' }}"
                                    style="width: {{ $job->capacity_percentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons: Manage/Edit/Delete for Owner Employer, or Details/Apply for Others -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        @if(auth()->check() && auth()->user()->isEmployer() && $job->employer_id === auth()->id())
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('jobs.show', $job) }}"
                                    class="flex-1 min-w-[70px] py-2 px-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-xs text-center shadow-sm transition-all hover:scale-[1.01]">
                                    Manage
                                </a>
                                <a href="{{ route('employer.posts.edit', $job) }}"
                                    class="py-2 px-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs flex items-center gap-1 transition-colors"
                                    title="Edit Post">
                                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
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
                                    class="py-2 px-2.5 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs flex items-center gap-1 transition-colors"
                                    title="Delete Post">
                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    <span>Delete</span>
                                </button>
                            </div>
                        @else
                            <a href="{{ route('jobs.show', $job) }}"
                                class="w-full py-2 px-3 rounded-xl {{ $job->isLocked() ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-sky-500 hover:bg-sky-600 text-white shadow-sm' }} font-bold text-xs flex items-center justify-center transition-colors">
                                {{ auth()->check() && auth()->user()->isEmployer() ? 'View Details' : 'View Details & Apply' }}
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
