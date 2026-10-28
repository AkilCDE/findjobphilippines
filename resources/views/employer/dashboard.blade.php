@extends('layouts.app')

@section('content')
<div class="space-y-8 text-zinc-100">

    <!-- Header Section -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Employer Dashboard
            </h1>
            <p class="text-xs sm:text-sm font-medium text-zinc-400 mt-1">
                Manage your job postings and worker applications
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('employer.scanner') }}"
                class="px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-2xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-850 text-zinc-100 font-extrabold text-xs sm:text-sm shadow-md flex items-center gap-2 transition-all hover:scale-[1.02]">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                Scan Worker QR
            </a>
            <a href="{{ route('employer.posts.create') }}"
                class="px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs sm:text-sm shadow-md flex items-center gap-2 transition-all hover:scale-[1.02]">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Post New Job
            </a>
            <a href="{{ route('employer.posts.index') }}"
                class="px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-2xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-850 text-zinc-200 font-bold text-xs sm:text-sm shadow-sm transition-colors">
                All My Posts
            </a>
        </div>
    </div>

    @if($pendingLeaveRequestsCount > 0)
        <div class="p-5 rounded-3xl border border-amber-800/80 bg-amber-950/50 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/20 border border-amber-600 text-amber-400 flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                    ⚠️
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">
                        {{ $pendingLeaveRequestsCount }} Worker Leave Request{{ $pendingLeaveRequestsCount > 1 ? 's' : '' }} Pending Confirmation
                    </h3>
                    <p class="text-xs font-medium text-amber-300 mt-0.5">
                        Worker(s) have requested to leave/quit their positions. Review and confirm to release them and reopen job slots.
                    </p>
                </div>
            </div>
            <a href="{{ route('employer.posts.index') }}"
                class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-sm transition-all whitespace-nowrap text-center">
                Review in Posts
            </a>
        </div>
    @endif

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        
        <div class="p-5 rounded-3xl bg-[#121215] border border-zinc-800">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase text-zinc-400">Total Hired</p>
                <div class="p-2 rounded-xl bg-zinc-800 text-zinc-200 border border-zinc-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-white mt-2">
                {{ $totalHired }} <span class="text-xs font-medium text-zinc-400">/ {{ $totalNeeded }} Needed</span>
            </p>
            <p class="text-[11px] font-medium text-emerald-400 mt-1">Filled across all posts</p>
        </div>

        <div class="p-5 rounded-3xl bg-[#121215] border border-zinc-800">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase text-zinc-400">Active Open Jobs</p>
                <div class="p-2 rounded-xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-white mt-2">{{ $activePosts }}</p>
            <p class="text-[11px] font-medium text-zinc-400 mt-1">Accepting applications</p>
        </div>

        <div class="p-5 rounded-3xl bg-[#121215] border border-zinc-800">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase text-zinc-400">Locked Posts</p>
                <div class="p-2 rounded-xl bg-amber-950/60 border border-amber-800/80 text-amber-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-white mt-2">{{ $lockedPosts }}</p>
            <p class="text-[11px] font-medium text-amber-400 mt-1">100% capacity reached</p>
        </div>

        <div class="p-5 rounded-3xl bg-[#121215] border border-zinc-800">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase text-zinc-400">Pending Applicants</p>
                <div class="p-2 rounded-xl bg-zinc-800 text-zinc-200 border border-zinc-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-white mt-2">{{ $pendingApplicationsCount }}</p>
            <p class="text-[11px] font-medium text-zinc-400 mt-1">Awaiting your review</p>
        </div>

    </div>

    <!-- Worker QR Scanner Quick Access Banner -->
    <div class="p-6 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-700 text-white flex items-center justify-center text-2xl shadow-md shrink-0">
                📷
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-white">Worker Digital ID & QR Scanner</h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-950/80 text-emerald-400 border border-emerald-800/80">Live Verification</span>
                </div>
                <p class="text-xs font-medium text-zinc-400 mt-0.5">
                    Scan any applicant's or worker's QR pass to instantly check their employment status, current or past employer, and work assignment.
                </p>
            </div>
        </div>
        <a href="{{ route('employer.scanner') }}"
            class="px-5 py-3 rounded-2xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all hover:scale-[1.02] flex items-center justify-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
            Open QR Scanner
        </a>
    </div>

    <!-- Posts Overview Table -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-800 pb-4">
            <div>
                <h3 class="text-lg font-bold text-white">Recent Job Opportunities</h3>
                <p class="text-xs font-medium text-zinc-400">Live monitoring of worker hire counts and lock status</p>
            </div>
            <a href="{{ route('employer.posts.create') }}" class="text-xs font-bold text-zinc-300 hover:text-white transition-colors">
                + Post Another Job
            </a>
        </div>

        @if($posts->isEmpty())
            <div class="text-center py-10">
                <p class="text-sm font-medium text-zinc-400">You haven't posted any jobs yet.</p>
                <a href="{{ route('employer.posts.create') }}" class="mt-3 inline-block px-5 py-2.5 rounded-xl bg-white text-black hover:bg-zinc-200 font-extrabold text-xs">
                    Post Your First Job
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-medium">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Job Opportunity</th>
                            <th class="py-3 px-3">Location Address</th>
                            <th class="py-3 px-3">Hired Status</th>
                            <th class="py-3 px-3">Capacity Progress</th>
                            <th class="py-3 px-3">Applicants</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-850">
                        @foreach($posts as $post)
                            <tr class="hover:bg-zinc-900/60 transition-colors">
                                <td class="py-3.5 px-3">
                                    <a href="{{ route('jobs.show', $post) }}" class="font-bold text-sm text-white hover:text-zinc-300 block">
                                        {{ $post->title }}
                                    </a>
                                    <span class="text-[10px] text-zinc-400 font-medium uppercase">{{ $post->category }} • {{ $post->job_type }}</span>
                                </td>
                                <td class="py-3.5 px-3 text-zinc-300 max-w-[200px] truncate">
                                    📍 {{ $post->location_address }}
                                </td>
                                <td class="py-3.5 px-3">
                                    @if($post->isLocked())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-950/80 text-amber-300 border border-amber-800/80">
                                            <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                            Locked
                                        </span>
                                    @elseif($post->isArchived())
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-400 border border-zinc-700">
                                            Archived
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-950/80 text-emerald-300 border border-emerald-800/80">
                                            Open
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 rounded-full bg-zinc-800 overflow-hidden">
                                            <div class="h-full {{ $post->isLocked() ? 'bg-amber-400' : 'bg-white' }}" style="width: {{ $post->capacity_percentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-white">
                                            {{ $post->workers_hired }}/{{ $post->workers_needed }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3">
                                    <a href="{{ route('employer.posts.applicants', $post) }}" class="font-bold text-zinc-200 hover:text-white underline">
                                        {{ $post->applications_count }} applicants &rarr;
                                    </a>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        <a href="{{ route('employer.posts.applicants', $post) }}"
                                            class="px-2.5 py-1.5 rounded-lg bg-zinc-800 text-zinc-200 hover:bg-zinc-750 border border-zinc-700 font-bold text-xs flex items-center gap-1 transition-colors"
                                            title="Review Applicants">
                                            <svg class="w-3.5 h-3.5 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            <span>Applicants ({{ $post->applications_count }})</span>
                                        </a>
                                        <a href="{{ route('employer.posts.edit', $post) }}"
                                            class="px-2.5 py-1.5 rounded-lg border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-1 transition-colors"
                                            title="Edit Post">
                                            <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            <span>Edit</span>
                                        </a>
                                        <button type="button"
                                            onclick="confirmAction({
                                                title: 'Delete Job Post Permanently?',
                                                message: 'Are you sure you want to permanently delete \'{{ addslashes($post->title) }}\'? All associated worker applications will be removed. This cannot be undone.',
                                                formAction: '{{ route('employer.posts.destroy', $post) }}',
                                                method: 'DELETE',
                                                confirmText: 'Yes, Delete Post',
                                                confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                            })"
                                            class="px-2.5 py-1.5 rounded-lg border border-red-800/60 bg-red-950/40 hover:bg-red-900/60 text-red-300 font-bold text-xs flex items-center gap-1 transition-colors"
                                            title="Delete Post">
                                            <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <span>Delete</span>
                                        </button>
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
