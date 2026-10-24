@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Admin Header Banner -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="text-xs font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 border border-purple-200">
                    Administrator Center
                </span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight">
                Admin Command Dashboard
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-1">
                Full authority over users, employers, workers, and job postings sorted by users across the Philippines.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-md shadow-purple-500/20 transition-all hover:scale-[1.02]">
                Manage All Users
            </a>
            <a href="{{ route('admin.posts.index') }}"
                class="px-4 py-2.5 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs shadow-sm transition-colors">
                Manage Posts by User
            </a>
        </div>
    </div>

    <!-- System Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        
        <div class="glass-card p-5 rounded-3xl border border-purple-100">
            <p class="text-xs font-bold uppercase text-slate-600">Total Users</p>
            <p class="text-3xl font-black text-slate-950 mt-1">{{ $totalUsers }}</p>
            <p class="text-[11px] font-semibold text-purple-800 mt-0.5">
                {{ $workersCount }} Workers • {{ $employersCount }} Employers
            </p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-sky-100">
            <p class="text-xs font-bold uppercase text-slate-600">Total Job Postings</p>
            <p class="text-3xl font-black text-slate-950 mt-1">{{ $totalPosts }}</p>
            <p class="text-[11px] font-semibold text-sky-800 mt-0.5">
                {{ $openPosts }} Open • {{ $lockedPosts }} Locked
            </p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-emerald-100">
            <p class="text-xs font-bold uppercase text-slate-600">Total Applications</p>
            <p class="text-3xl font-black text-slate-950 mt-1">{{ $totalApplications }}</p>
            <p class="text-[11px] font-semibold text-emerald-800 mt-0.5">
                {{ $totalHired }} Workers Placed
            </p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-slate-100">
            <p class="text-xs font-bold uppercase text-slate-600">Direct Messages</p>
            <p class="text-3xl font-black text-slate-950 mt-1">{{ $totalMessages }}</p>
            <p class="text-[11px] font-semibold text-slate-700 mt-0.5">Worker-Employer Chats</p>
        </div>

    </div>

    <!-- Two-Column Recent Activity Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Users Table -->
        <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-sky-100 pb-3">
                <h3 class="text-base font-black text-slate-950">Recent Registered Users</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-sky-700 hover:underline">
                    View All ({{ $totalUsers }}) &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="text-[10px] text-slate-600 uppercase border-b border-slate-100">
                            <th class="py-2 px-2">User</th>
                            <th class="py-2 px-2">Role</th>
                            <th class="py-2 px-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentUsers as $u)
                            <tr class="hover:bg-white/60">
                                <td class="py-2.5 px-2">
                                    <span class="font-bold text-slate-950 block">{{ $u->display_name }}</span>
                                    <span class="text-[11px] text-slate-600">{{ $u->email }}</span>
                                </td>
                                <td class="py-2.5 px-2">
                                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $u->isAdmin() ? 'bg-purple-100 text-purple-900' : ($u->isEmployer() ? 'bg-sky-100 text-sky-900' : 'bg-emerald-100 text-emerald-900') }}">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-2 text-right">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="text-sky-700 hover:text-sky-900 font-bold">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Posts Table -->
        <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-sky-100 pb-3">
                <h3 class="text-base font-black text-slate-950">Recent Job Posts</h3>
                <a href="{{ route('admin.posts.index') }}" class="text-xs font-bold text-sky-700 hover:underline">
                    Manage Posts &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="text-[10px] text-slate-600 uppercase border-b border-slate-100">
                            <th class="py-2 px-2">Job Title</th>
                            <th class="py-2 px-2">Employer</th>
                            <th class="py-2 px-2">Capacity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentPosts as $p)
                            <tr class="hover:bg-white/60">
                                <td class="py-2.5 px-2">
                                    <a href="{{ route('jobs.show', $p) }}" class="font-bold text-slate-950 hover:text-sky-700 block line-clamp-1">
                                        {{ $p->title }}
                                    </a>
                                    <span class="text-[10px] text-slate-500 font-semibold">{{ $p->location_address }}</span>
                                </td>
                                <td class="py-2.5 px-2 font-bold text-slate-800">
                                    {{ $p->employer?->display_name ?? 'Employer' }}
                                </td>
                                <td class="py-2.5 px-2">
                                    <span class="text-xs font-extrabold {{ $p->isLocked() ? 'text-amber-800' : 'text-sky-800' }}">
                                        {{ $p->workers_hired }}/{{ $p->workers_needed }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
