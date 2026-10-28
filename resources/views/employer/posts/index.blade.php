@extends('layouts.app')

@section('content')
<div class="space-y-6 text-zinc-100">

    <!-- Top Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                My Job Postings
            </h1>
            <p class="text-sm font-medium text-zinc-400 mt-1">
                Manage all your published opportunities, monitor applicant counts, and lock or archive listings.
            </p>
        </div>

        <a href="{{ route('employer.posts.create') }}"
            class="px-5 py-3 rounded-2xl bg-white hover:bg-zinc-200 text-black font-extrabold text-sm shadow-md flex items-center gap-2 transition-all hover:scale-[1.02]">
            <svg class="w-5 h-5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Create New Job Post
        </a>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('employer.posts.index', ['status' => 'all']) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status', 'all') == 'all' ? 'bg-white text-black' : 'bg-zinc-900 border border-zinc-800 text-zinc-300 hover:text-white' }}">
            All Posts
        </a>
        <a href="{{ route('employer.posts.index', ['status' => 'open']) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') == 'open' ? 'bg-white text-black' : 'bg-zinc-900 border border-zinc-800 text-zinc-300 hover:text-white' }}">
            Open (Accepting Workers)
        </a>
        <a href="{{ route('employer.posts.index', ['status' => 'locked']) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') == 'locked' ? 'bg-white text-black' : 'bg-zinc-900 border border-zinc-800 text-zinc-300 hover:text-white' }}">
            Locked (Capacity Reached)
        </a>
        <a href="{{ route('employer.posts.index', ['status' => 'archived']) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') == 'archived' ? 'bg-white text-black' : 'bg-zinc-900 border border-zinc-800 text-zinc-300 hover:text-white' }}">
            Archived (Hidden from Feed)
        </a>
    </div>

    <!-- Job Posts Table / Cards -->
    <div class="p-6 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-4">
        @if($posts->isEmpty())
            <div class="text-center py-12">
                <p class="text-sm font-medium text-zinc-400">No job postings found under this filter.</p>
                <a href="{{ route('employer.posts.create') }}" class="mt-3 inline-block px-5 py-2.5 rounded-xl bg-white text-black hover:bg-zinc-200 font-extrabold text-xs">
                    Post an Opportunity
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($posts as $post)
                    <div class="p-5 rounded-2xl bg-zinc-900 border border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <!-- Left Details -->
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase px-2.5 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700">
                                    {{ $post->category }}
                                </span>
                                @if($post->isLocked())
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-950/80 text-amber-300 border border-amber-800/80">
                                        Locked (Capacity Filled)
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
                                <span class="text-[11px] text-zinc-500 font-medium">• {{ $post->created_at->format('M d, Y') }}</span>
                            </div>

                            <h3 class="text-base sm:text-lg font-bold text-white">
                                <a href="{{ route('jobs.show', $post) }}" class="hover:text-zinc-300 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h3>

                            <p class="text-xs font-medium text-zinc-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $post->location_address }}
                            </p>

                            <!-- Worker Capacity Bar -->
                            <div class="pt-2 max-w-md">
                                <div class="flex items-center justify-between text-xs font-bold text-zinc-300 mb-1">
                                    <span>Hired Capacity:</span>
                                    <span class="{{ $post->isLocked() ? 'text-amber-400' : 'text-zinc-200' }}">
                                        {{ $post->workers_hired }} / {{ $post->workers_needed }} Workers
                                        @if($post->isLocked()) (Full) @else ({{ $post->remaining_slots }} remaining) @endif
                                    </span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-zinc-800 overflow-hidden">
                                    <div class="h-full {{ $post->isLocked() ? 'bg-amber-400' : 'bg-white' }}" style="width: {{ $post->capacity_percentage }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-zinc-800">
                            
                            <!-- Applicants Button -->
                            <a href="{{ route('employer.posts.applicants', $post) }}"
                                class="px-4 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md flex items-center gap-1.5 transition-all">
                                <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                Applicants
                                <span class="bg-black/15 text-black px-1.5 py-0.2 rounded-full text-[10px] font-black">
                                    {{ $post->applications_count }}
                                </span>
                            </a>

                            <!-- Edit Button -->
                            <a href="{{ route('employer.posts.edit', $post) }}"
                                class="px-3 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-1 transition-colors">
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                <span>Edit</span>
                            </a>

                            <!-- Archive / Unarchive Button with Modal Confirmation -->
                            @if($post->isArchived())
                                <button type="button"
                                    onclick="confirmAction({
                                        title: 'Restore Post to Feed?',
                                        message: 'Are you sure you want to unarchive \'{{ addslashes($post->title) }}\'? It will become visible again to workers.',
                                        formAction: '{{ route('employer.posts.unarchive', $post) }}',
                                        confirmText: 'Yes, Restore Post',
                                        confirmClass: 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                    })"
                                    class="px-3 py-2 rounded-xl border border-emerald-800/80 bg-emerald-950/40 text-emerald-300 font-bold text-xs hover:bg-emerald-900/60 transition-colors">
                                    Restore
                                </button>
                            @else
                                <button type="button"
                                    onclick="confirmAction({
                                        title: 'Archive Job Post?',
                                        message: 'Are you sure you want to archive \'{{ addslashes($post->title) }}\'? It will no longer appear on worker feeds or maps.',
                                        formAction: '{{ route('employer.posts.archive', $post) }}',
                                        confirmText: 'Yes, Archive Post',
                                        confirmClass: 'bg-amber-600 hover:bg-amber-700 text-white'
                                    })"
                                    class="px-3 py-2 rounded-xl border border-zinc-700 bg-zinc-800 text-zinc-300 font-bold text-xs hover:bg-zinc-700 transition-colors">
                                    Archive
                                </button>
                            @endif

                            <!-- Delete Button with Strict Modal Confirmation -->
                            <button type="button"
                                onclick="confirmAction({
                                    title: 'Delete Job Post Permanently?',
                                    message: 'Are you sure you want to delete \'{{ addslashes($post->title) }}\'? All associated worker applications will be permanently removed. This cannot be undone.',
                                    formAction: '{{ route('employer.posts.destroy', $post) }}',
                                    method: 'DELETE',
                                    confirmText: 'Yes, Delete Post',
                                    confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                })"
                                class="px-3 py-2 rounded-xl border border-red-800/60 bg-red-950/40 text-red-300 font-bold text-xs hover:bg-red-900/60 flex items-center gap-1 transition-colors"
                                title="Delete Post">
                                <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                <span>Delete</span>
                            </button>

                        </div>

                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
