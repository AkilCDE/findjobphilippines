@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header Section -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                Employer Dashboard
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1">
                Manage your job postings and worker applications
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('employer.posts.create') }}"
                class="px-5 py-3 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-lg shadow-sky-500/25 flex items-center gap-2 transition-all hover:scale-[1.02]">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Post New Job
            </a>
            <a href="{{ route('employer.posts.index') }}"
                class="px-4 py-3 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm shadow-sm transition-colors">
                All My Posts
            </a>
        </div>
    </div>

    @if($pendingLeaveRequestsCount > 0)
        <div class="glass-panel p-5 rounded-3xl border border-amber-300 bg-amber-50/80 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg shadow-sm flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h3 class="text-sm font-black text-amber-950">
                        {{ $pendingLeaveRequestsCount }} Worker Leave Request{{ $pendingLeaveRequestsCount > 1 ? 's' : '' }} Pending Confirmation
                    </h3>
                    <p class="text-xs font-semibold text-amber-900 mt-0.5">
                        Worker(s) have requested to leave/quit their positions. Review and confirm to release them and reopen job slots.
                    </p>
                </div>
            </div>
            <a href="{{ route('employer.posts.index') }}"
                class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-sm transition-all whitespace-nowrap text-center">
                Review in Posts
            </a>
        </div>
    @endif

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        
        <div class="glass-card p-5 rounded-3xl border border-white/80">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase text-slate-600">Total Hired Workers</p>
                <div class="p-2 rounded-xl bg-sky-100 text-sky-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-950 mt-2">
                {{ $totalHired }} <span class="text-xs font-bold text-slate-600">/ {{ $totalNeeded }} Needed</span>
            </p>
            <p class="text-[11px] font-semibold text-emerald-700 mt-1">Filled across all posts</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-white/80">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase text-slate-600">Active Open Jobs</p>
                <div class="p-2 rounded-xl bg-emerald-100 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-950 mt-2">{{ $activePosts }}</p>
            <p class="text-[11px] font-semibold text-sky-800 mt-1">Accepting applications</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-white/80">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase text-slate-600">Locked Posts</p>
                <div class="p-2 rounded-xl bg-amber-100 text-amber-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-950 mt-2">{{ $lockedPosts }}</p>
            <p class="text-[11px] font-semibold text-amber-800 mt-1">100% capacity reached</p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-white/80">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase text-slate-600">Pending Applicants</p>
                <div class="p-2 rounded-xl bg-purple-100 text-purple-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-950 mt-2">{{ $pendingApplicationsCount }}</p>
            <p class="text-[11px] font-semibold text-purple-800 mt-1">Awaiting your review</p>
        </div>

    </div>

    <!-- Posts Overview Table -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-sky-100 pb-4">
            <div>
                <h3 class="text-lg font-black text-slate-950">Recent Job Opportunities</h3>
                <p class="text-xs font-semibold text-slate-700">Live monitoring of worker hire counts and lock status</p>
            </div>
            <a href="{{ route('employer.posts.create') }}" class="text-xs font-bold text-sky-700 hover:text-sky-900">
                + Post Another Job
            </a>
        </div>

        @if($posts->isEmpty())
            <div class="text-center py-10">
                <p class="text-sm font-bold text-slate-700">You haven't posted any jobs yet.</p>
                <a href="{{ route('employer.posts.create') }}" class="mt-3 inline-block px-5 py-2.5 rounded-xl bg-sky-500 text-white font-bold text-xs">
                    Post Your First Job
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Job Opportunity</th>
                            <th class="py-3 px-3">Location Address</th>
                            <th class="py-3 px-3">Hired Status</th>
                            <th class="py-3 px-3">Capacity Progress</th>
                            <th class="py-3 px-3">Applicants</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($posts as $post)
                            <tr class="hover:bg-white/60 transition-colors">
                                <td class="py-3.5 px-3">
                                    <a href="{{ route('jobs.show', $post) }}" class="font-extrabold text-sm text-slate-950 hover:text-sky-700 block">
                                        {{ $post->title }}
                                    </a>
                                    <span class="text-[10px] text-slate-600 font-bold uppercase">{{ $post->category }} • {{ $post->job_type }}</span>
                                </td>
                                <td class="py-3.5 px-3 text-slate-800 max-w-[200px] truncate">
                                    📍 {{ $post->location_address }}
                                </td>
                                <td class="py-3.5 px-3">
                                    @if($post->isLocked())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                            Locked (10/10)
                                        </span>
                                    @elseif($post->isArchived())
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-slate-200 text-slate-800">
                                            Archived
                                        </span>
                                    @else
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900">
                                            Open
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 rounded-full bg-slate-200 overflow-hidden">
                                            <div class="h-full {{ $post->isLocked() ? 'bg-amber-500' : 'bg-sky-500' }}" style="width: {{ $post->capacity_percentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-extrabold text-slate-900">
                                            {{ $post->workers_hired }}/{{ $post->workers_needed }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3">
                                    <a href="{{ route('employer.posts.applicants', $post) }}" class="font-extrabold text-sky-700 hover:underline">
                                        {{ $post->applications_count }} applicants &rarr;
                                    </a>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('employer.posts.applicants', $post) }}" class="p-1.5 rounded-lg bg-sky-50 text-sky-800 hover:bg-sky-100" title="Review Applicants">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        </a>
                                        <a href="{{ route('employer.posts.edit', $post) }}" class="p-1.5 rounded-lg bg-slate-100 text-slate-800 hover:bg-slate-200" title="Edit Post">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
