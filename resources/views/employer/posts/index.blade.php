@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Banner -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                My Job Postings
            </h1>
            <p class="text-sm font-semibold text-slate-700 mt-1">
                Manage all your published opportunities, monitor applicant counts, and lock or archive listings.
            </p>
        </div>

        <a href="{{ route('employer.posts.create') }}"
            class="px-5 py-3 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-lg shadow-sky-500/25 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Create New Job Post
        </a>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('employer.posts.index', ['status' => 'all']) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status', 'all') == 'all' ? 'bg-sky-600 text-white shadow-md' : 'glass-card text-slate-800 hover:bg-white' }}">
            All Posts
        </a>
        <a href="{{ route('employer.posts.index', ['status' => 'open']) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') == 'open' ? 'bg-sky-600 text-white shadow-md' : 'glass-card text-slate-800 hover:bg-white' }}">
            Open (Accepting Workers)
        </a>
        <a href="{{ route('employer.posts.index', ['status' => 'locked']) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') == 'locked' ? 'bg-sky-600 text-white shadow-md' : 'glass-card text-slate-800 hover:bg-white' }}">
            Locked (Capacity Reached)
        </a>
        <a href="{{ route('employer.posts.index', ['status' => 'archived']) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') == 'archived' ? 'bg-sky-600 text-white shadow-md' : 'glass-card text-slate-800 hover:bg-white' }}">
            Archived (Hidden from Feed)
        </a>
    </div>

    <!-- Job Posts Table / Cards -->
    <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl space-y-4">
        @if($posts->isEmpty())
            <div class="text-center py-12">
                <p class="text-sm font-bold text-slate-800">No job postings found under this filter.</p>
                <a href="{{ route('employer.posts.create') }}" class="mt-3 inline-block px-5 py-2.5 rounded-xl bg-sky-500 text-white font-bold text-xs">
                    Post an Opportunity
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($posts as $post)
                    <div class="p-5 rounded-2xl glass-card border border-white/90 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <!-- Left Details -->
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded bg-sky-100 text-sky-900 border border-sky-200">
                                    {{ $post->category }}
                                </span>
                                @if($post->isLocked())
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200">
                                        Locked (Capacity Filled)
                                    </span>
                                @elseif($post->isArchived())
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-slate-200 text-slate-800 border border-slate-300">
                                        Archived
                                    </span>
                                @else
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-200">
                                        Open
                                    </span>
                                @endif
                                <span class="text-[11px] text-slate-500 font-bold">• {{ $post->created_at->format('M d, Y') }}</span>
                            </div>

                            <h3 class="text-base sm:text-lg font-black text-slate-950">
                                <a href="{{ route('jobs.show', $post) }}" class="hover:text-sky-700 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h3>

                            <p class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $post->location_address }}
                            </p>

                            <!-- Worker Capacity Bar -->
                            <div class="pt-2 max-w-md">
                                <div class="flex items-center justify-between text-xs font-extrabold text-slate-900 mb-1">
                                    <span>Hired Capacity:</span>
                                    <span class="{{ $post->isLocked() ? 'text-amber-800' : 'text-sky-900' }}">
                                        {{ $post->workers_hired }} / {{ $post->workers_needed }} Workers
                                        @if($post->isLocked()) (Full) @else ({{ $post->remaining_slots }} remaining) @endif
                                    </span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full {{ $post->isLocked() ? 'bg-amber-500' : 'bg-sky-500' }}" style="width: {{ $post->capacity_percentage }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions with Clear Confirmation Modals (No Duplicates) -->
                        <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100">
                            
                            <!-- Applicants Button -->
                            <a href="{{ route('employer.posts.applicants', $post) }}"
                                class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-md shadow-sky-500/20 flex items-center gap-1.5 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                Applicants
                                <span class="bg-white/20 text-white px-1.5 py-0.2 rounded-full text-[10px]">
                                    {{ $post->applications_count }}
                                </span>
                            </a>

                            <!-- Edit Button -->
                            <a href="{{ route('employer.posts.edit', $post) }}"
                                class="px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                                Edit
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
                                    class="px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-900 font-bold text-xs hover:bg-emerald-100 transition-colors">
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
                                    class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-slate-800 font-bold text-xs hover:bg-slate-50 transition-colors">
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
                                class="px-3 py-2 rounded-xl border border-red-200 bg-red-50 text-red-800 font-bold text-xs hover:bg-red-100 transition-colors"
                                title="Delete Post">
                                Delete
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
