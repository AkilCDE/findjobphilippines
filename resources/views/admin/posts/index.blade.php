@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header Banner (Monochrome Dark) -->
    <div class="bg-[#121215] p-6 sm:p-8 rounded-3xl border border-zinc-800 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-zinc-400 hover:text-white flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to Dashboard
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Manage Job Postings
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-zinc-400 mt-1">
                Supervise all job postings organized by employer account, monitor lock statuses, or remove non-compliant listings.
            </p>
        </div>

        <span class="text-xs font-black text-white bg-zinc-900 px-3.5 py-1.5 rounded-full border border-zinc-700">
            {{ $posts->total() }} Total Job Postings
        </span>
    </div>

    <!-- Filter Bar: Sorted by Users / Employers -->
    <form method="GET" action="{{ route('admin.posts.index') }}" class="bg-[#121215] p-4 rounded-2xl border border-zinc-800 shadow-md flex flex-wrap items-center gap-3">
        
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search job title, location, or employer name..."
                class="w-full px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-800 text-white placeholder-zinc-500 text-xs font-semibold focus:border-white focus:outline-none">
        </div>

        <!-- Filter by Employer / User -->
        <div class="w-56">
            <select name="employer_id" class="w-full px-3 py-2.5 rounded-xl bg-[#18181b] border border-zinc-800 text-white text-xs font-semibold focus:border-white focus:outline-none">
                <option value="">All Employers (Sorted)</option>
                @foreach($employers as $emp)
                    <option value="{{ $emp->id }}" {{ request('employer_id') == $emp->id ? 'selected' : '' }}>
                        👤 {{ $emp->name }} @if(!empty($emp->company_name))(🏢 {{ $emp->company_name }})@endif ({{ $emp->job_posts_count }} posts)
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="w-36">
            <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-[#18181b] border border-zinc-800 text-white text-xs font-semibold focus:border-white focus:outline-none">
                <option value="all">All Statuses</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open Only</option>
                <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Locked Only</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived Only</option>
            </select>
        </div>

        <!-- Sorting -->
        <div class="w-40">
            <select name="sort" class="w-full px-3 py-2.5 rounded-xl bg-[#18181b] border border-zinc-800 text-white text-xs font-semibold focus:border-white focus:outline-none">
                <option value="employer" {{ request('sort', 'employer') == 'employer' ? 'selected' : '' }}>Sort by Employer Name</option>
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Sort by Newest</option>
                <option value="capacity" {{ request('sort') == 'capacity' ? 'selected' : '' }}>Sort by Capacity</option>
                <option value="workers_needed" {{ request('sort') == 'workers_needed' ? 'selected' : '' }}>Sort by Target Quota</option>
            </select>
        </div>

        <button type="submit" class="px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-black text-xs shadow-md transition-all">
            Apply Filters
        </button>

        @if(request()->hasAny(['search', 'employer_id', 'status', 'sort']))
            <a href="{{ route('admin.posts.index') }}" class="px-3.5 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 text-zinc-300 hover:text-white font-bold text-xs transition-colors">
                Reset
            </a>
        @endif
    </form>

    <!-- Posts Table -->
    <div class="bg-[#121215] p-6 rounded-3xl border border-zinc-800 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-semibold">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">Employer / User</th>
                        <th class="py-3 px-3">Job Opportunity</th>
                        <th class="py-3 px-3">Workplace Location</th>
                        <th class="py-3 px-3">Hired Quota</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Admin Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/70">
                    @forelse($posts as $post)
                        <tr class="hover:bg-zinc-900/50 transition-colors">
                            <td class="py-3.5 px-3">
                                <span class="font-extrabold text-sm text-white block">
                                    {{ $post->employer?->name ?? 'User #' . $post->employer_id }}
                                </span>
                                <span class="text-[11px] text-zinc-400 font-medium block">
                                    @if(!empty($post->employer?->company_name))
                                        <span class="text-zinc-300 font-semibold">🏢 {{ $post->employer->company_name }}</span> • 
                                    @endif
                                    {{ $post->employer?->email }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 max-w-[240px]">
                                <a href="{{ route('jobs.show', $post) }}" class="font-bold text-white hover:text-zinc-300 block line-clamp-1 transition-colors">
                                    {{ $post->title }}
                                </a>
                                <span class="text-[10px] text-zinc-400 font-bold uppercase">{{ $post->category }} • {{ $post->job_type }}</span>
                            </td>
                            <td class="py-3.5 px-3 text-zinc-300 max-w-[200px] truncate">
                                📍 {{ $post->location_address }}
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-xs text-white">
                                        {{ $post->workers_hired }}/{{ $post->workers_needed }}
                                    </span>
                                    <div class="w-16 h-2 rounded-full bg-zinc-800 overflow-hidden">
                                        <div class="h-full {{ $post->isLocked() ? 'bg-zinc-500' : 'bg-white' }}" style="width: {{ $post->capacity_percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3">
                                @if($post->isLocked())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300 border border-zinc-700">
                                        Locked
                                    </span>
                                @elseif($post->isArchived())
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-zinc-900 text-zinc-500 border border-zinc-800">
                                        Archived
                                    </span>
                                @else
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-white text-black">
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
                                            confirmClass: 'bg-white hover:bg-zinc-200 text-black'
                                        })"
                                        class="px-2.5 py-1.5 rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-200 font-bold text-xs hover:bg-zinc-800 transition-colors"
                                        title="Toggle Lock/Open">
                                        {{ $post->status === 'open' ? 'Lock' : 'Open' }}
                                    </button>

                                    <!-- Delete Post by Admin with Strict Confirmation -->
                                    <button type="button"
                                        onclick="confirmAction({
                                            title: 'Admin: Delete Job Post?',
                                            message: 'Are you sure you want to permanently delete \'{{ addslashes($post->title) }}\' posted by {{ addslashes($post->employer?->name ?? 'Employer') }}@if(!empty($post->employer?->company_name)) ({{ addslashes($post->employer->company_name) }})@endif? All applications will be erased.',
                                            formAction: '{{ route('admin.posts.destroy', $post) }}',
                                            method: 'DELETE',
                                            confirmText: 'Yes, Delete Post',
                                            confirmClass: 'bg-white hover:bg-zinc-200 text-black'
                                        })"
                                        class="px-2.5 py-1.5 rounded-lg border border-zinc-800 bg-zinc-900 text-zinc-400 font-bold text-xs hover:text-white hover:bg-zinc-800 transition-colors"
                                        title="Delete Post">
                                        Delete
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-500">No job postings found matching your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-3 border-t border-zinc-800">
            {{ $posts->links() }}
        </div>
    </div>

</div>
@endsection
