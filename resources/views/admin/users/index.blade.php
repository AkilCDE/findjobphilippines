@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header & Search Banner -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-purple-800 hover:text-purple-950 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to Admin Dashboard
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                User Management
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-1">
                Edit profiles, manage roles, and delete user accounts with full safety confirmations.
            </p>
        </div>

        <span class="text-xs font-black text-purple-900 bg-purple-100 px-3.5 py-1.5 rounded-full border border-purple-200">
            {{ $users->total() }} Total Registered Accounts
        </span>
    </div>

    <!-- Search & Role Filter Bar -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="glass-panel p-4 rounded-2xl border border-white/80 shadow-md flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[220px]">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by name, email, address, or phone..."
                class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-semibold">
        </div>

        <div class="w-44">
            <select name="role" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-semibold">
                <option value="all">All Roles</option>
                <option value="worker" {{ request('role') == 'worker' ? 'selected' : '' }}>Workers Only</option>
                <option value="employer" {{ request('role') == 'employer' ? 'selected' : '' }}>Employers Only</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins Only</option>
            </select>
        </div>

        <div class="w-36">
            <select name="sort" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-semibold">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                <option value="role" {{ request('sort') == 'role' ? 'selected' : '' }}>By Role</option>
            </select>
        </div>

        <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition-all">
            Filter Users
        </button>

        @if(request()->hasAny(['search', 'role', 'sort']))
            <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-800 font-bold text-xs">
                Reset
            </a>
        @endif
    </form>

    <!-- Users Table -->
    <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-semibold">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">User Profile</th>
                        <th class="py-3 px-3">Role</th>
                        <th class="py-3 px-3">Contact & Address</th>
                        <th class="py-3 px-3">Activity</th>
                        <th class="py-3 px-3">Joined Date</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-white/60 transition-colors">
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($user->display_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-sm text-slate-950 block">
                                            {{ $user->display_name }}
                                        </span>
                                        <span class="text-[11px] text-slate-600">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full {{ $user->isAdmin() ? 'bg-purple-100 text-purple-900 border border-purple-200' : ($user->isEmployer() ? 'bg-sky-100 text-sky-900 border border-sky-200' : 'bg-emerald-100 text-emerald-900 border border-emerald-200') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-slate-800 max-w-[220px]">
                                <p class="font-bold text-slate-900">{{ $user->contact_number ?? 'No contact' }}</p>
                                <p class="text-[11px] text-slate-600 truncate">{{ $user->address ?? 'No address set' }}</p>
                            </td>
                            <td class="py-3.5 px-3 text-slate-800">
                                @if($user->isEmployer())
                                    <span class="font-bold text-sky-800">{{ $user->job_posts_count }} Job Posts</span>
                                @elseif($user->isWorker())
                                    <span class="font-bold text-emerald-800">{{ $user->applications_count }} Applications</span>
                                @else
                                    <span class="text-purple-800 font-bold">Admin Privileges</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-slate-600 font-medium text-[11px]">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    <!-- Edit User Button -->
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                                        Edit
                                    </a>

                                    <!-- Delete User Button with Confirmation Modal -->
                                    @if($user->id !== auth()->id())
                                        <button type="button"
                                            onclick="confirmAction({
                                                title: 'Delete User Account Permanently?',
                                                message: 'Are you sure you want to delete {{ addslashes($user->display_name) }} ({{ $user->email }})? All their job posts, applications, and messages will be removed permanently.',
                                                formAction: '{{ route('admin.users.destroy', $user) }}',
                                                method: 'DELETE',
                                                confirmText: 'Yes, Delete User',
                                                confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                            })"
                                            class="px-2.5 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 font-bold text-xs hover:bg-red-100 transition-colors">
                                            Delete
                                        </button>
                                    @else
                                        <span class="text-[10px] text-slate-400 font-semibold px-2 py-1">You</span>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
