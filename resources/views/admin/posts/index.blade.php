@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-purple-800 hover:text-purple-950 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to Admin Dashboard
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                Manage Posts <span class="text-purple-700">Sorted by Users</span>
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-1">
                Supervise all job postings organized by employer account, monitor lock statuses, or remove non-compliant listings.
            </p>
        </div>

        <span class="text-xs font-black text-purple-900 bg-purple-100 px-3.5 py-1.5 rounded-full border border-purple-200">
            {{ $posts->total() }} Total Job Postings
        </span>
    </div>

    <!-- Filter Bar: Sorted by Users / Employers -->
    <form method="GET" action="{{ route('admin.posts.index') }}" class="glass-panel p-4 rounded-2xl border border-white/80 shadow-md flex flex-wrap items-center gap-3">
        
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search job title, location, or employer name..."
                class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-semibold">
        </div>

        <!-- Filter by Employer / User -->
        <div class="w-56">
            <select name="employer_id" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-semibold">
                <option value="">All Employers (Sorted)</option>
                @foreach($employers as $emp)
                    <option value="{{ $emp->id }}" {{ request('employer_id') == $emp->id ? 'selected' : '' }}>
                        👤 {{ $emp->display_name }} ({{ $emp->job_posts_count }} posts)
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="w-36">
            <select name="status" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-semibold">
                <option value="all">All Statuses</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open Only</option>
                <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Locked Only</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived Only</option>
            </select>
        </div>

        <!-- Sorting -->
        <div class="w-40">
            <select name="sort" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-semibold">
                <option value="employer" {{ request('sort', 'employer') == 'employer' ? 'selected' : '' }}>Sort by Employer Name</option>
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Sort by Newest</option>
                <option value="capacity" {{ request('sort') == 'capacity' ? 'selected' : '' }}>Sort by Capacity</option>
                <option value="workers_needed" {{ request('sort') == 'workers_needed' ? 'selected' : '' }}>Sort by Target Quota</option>
            </select>
        </div>

        <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition-all">
            Apply Filters
        </button>

        @if(request()->hasAny(['search', 'employer_id', 'status', 'sort']))
            <a href="{{ route('admin.posts.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-800 font-bold text-xs">
                Reset
            </a>
        @endif
    </form>

    <!-- Posts Table -->
    <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-semibold">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">Employer / User</th>
                        <th class="py-3 px-3">Job Opportunity</th>
                        <th class="py-3 px-3">Workplace Location</th>
                        <th class="py-3 px-3">Hired Quota</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Admin Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($posts as $post)
                        <tr class="hover:bg-white/60 transition-colors">
                            <td class="py-3.5 px-3">
                                <span class="font-extrabold text-sm text-purple-950 block">
                                    {{ $post->employer?->display_name ?? 'User #' . $post->employer_id }}
                                </span>
                                <span class="text-[11px] text-slate-600 font-medium">
                                    {{ $post->employer?->email }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 max-w-[240px]">
                                <a href="{{ route('jobs.show', $post) }}" class="font-bold text-slate-950 hover:text-sky-700 block line-clamp-1">
                                    {{ $post->title }}
                                </a>
                                <span class="text-[10px] text-slate-500 font-bold uppercase">{{ $post->category }} • {{ $post->job_type }}</span>
                            </td>
                            <td class="py-3.5 px-3 text-slate-800 max-w-[200px] truncate">
                                📍 {{ $post->location_address }}
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-xs text-slate-900">
                                        {{ $post->workers_hired }}/{{ $post->workers_needed }}
                                    </span>
                                    <div class="w-16 h-2 rounded-full bg-slate-200 overflow-hidden">
                                        <div class="h-full {{ $post->isLocked() ? 'bg-amber-500' : 'bg-sky-500' }}" style="width: {{ $post->capacity_percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3">
                                @if($post->isLocked())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">
                                        Locked
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
                            <td class="py-3.5 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    <!-- Toggle Lock/Open Status Form with Modal Confirmation -->
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Toggle Post Status?',
                                            message: 'Are you sure you want to change status of \'{{ addslashes($post->title) }}\' to {{ $post->status === 'open' ? 'LOCKED' : 'OPEN' }}?',
                                            formAction: '{{ route('admin.posts.toggle-status', $post) }}',
                                            confirmText: 'Yes, Change Status',
                                            confirmClass: 'bg-purple-600 hover:bg-purple-700 text-white'
                                        })"
                                        class="px-2.5 py-1.5 rounded-lg border border-purple-200 bg-purple-50 text-purple-900 font-bold text-xs hover:bg-purple-100 transition-colors"
                                        title="Toggle Lock/Open">
                                        {{ $post->status === 'open' ? 'Lock' : 'Open' }}
                                    </button>

                                    <!-- Delete Post by Admin with Strict Confirmation -->
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Admin: Delete Job Post?',
                                            message: 'Are you sure you want to permanently delete \'{{ addslashes($post->title) }}\' posted by {{ addslashes($post->employer?->display_name) }}? All applications will be erased.',
                                            formAction: '{{ route('admin.posts.destroy', $post) }}',
                                            method: 'DELETE',
                                            confirmText: 'Yes, Delete Post',
                                            confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                        })"
                                        class="px-2.5 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 font-bold text-xs hover:bg-red-100 transition-colors"
                                        title="Delete Post">
                                        Delete
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100">
            {{ $posts->links() }}
        </div>
    </div>

</div>
@endsection
