@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header & Summary Stats Banner -->
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
                Employer and worker accounts categorized in separated containers for organized administrative oversight.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-black text-sky-900 bg-sky-100 px-3 py-1.5 rounded-full border border-sky-200">
                🏢 {{ $employersCount }} Employers
            </span>
            <span class="text-xs font-black text-emerald-900 bg-emerald-100 px-3 py-1.5 rounded-full border border-emerald-200">
                👷 {{ $workersCount }} Workers
            </span>
            <span class="text-xs font-black text-purple-900 bg-purple-100 px-3 py-1.5 rounded-full border border-purple-200">
                🛡️ {{ count($admins) }} Admins
            </span>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="glass-panel p-4 rounded-2xl border border-white/80 shadow-md">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, company, email, phone, or address..."
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-semibold">
            </div>

            <div class="w-36">
                <select name="sort" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-semibold">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition-all">
                Search Accounts
            </button>

            @if(request()->hasAny(['search', 'sort']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                    Reset
                </a>
            @endif
        </form>

        <!-- Quick Jump Buttons to Containers -->
        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-slate-700 flex-wrap">
            <span class="text-slate-500 uppercase text-[10px] tracking-wider">Quick Jump:</span>
            <a href="#employersContainer" class="px-3 py-1 rounded-lg bg-sky-50 text-sky-800 hover:bg-sky-100 border border-sky-200 transition-colors">
                🏢 Employers Container &darr;
            </a>
            <a href="#workersContainer" class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                👷 Workers Container &darr;
            </a>
            @if($admins->isNotEmpty())
                <a href="#adminsContainer" class="px-3 py-1 rounded-lg bg-purple-50 text-purple-800 hover:bg-purple-100 border border-purple-200 transition-colors">
                    🛡️ Admins &darr;
                </a>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CONTAINER 1: EMPLOYERS CONTAINER          -->
    <!-- ========================================== -->
    <div id="employersContainer" class="glass-panel p-6 sm:p-7 rounded-3xl border border-sky-200/80 bg-white/95 shadow-xl space-y-4">
        
        <!-- Employers Container Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-sky-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-sky-500 text-white font-black flex items-center justify-center text-lg shadow-md shadow-sky-500/20">
                    🏢
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-950 flex items-center gap-2">
                        <span>Registered Employers</span>
                        <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-900 border border-sky-200">
                            {{ $employers->total() }} Accounts
                        </span>
                    </h2>
                    <p class="text-xs font-semibold text-slate-600 mt-0.5">
                        Company owners, business managers, and recruiting organizations
                    </p>
                </div>
            </div>

            <span class="text-[11px] font-bold text-sky-800 bg-sky-50 px-3 py-1 rounded-xl border border-sky-200 self-start sm:self-auto">
                Container: Employers
            </span>
        </div>

        @if($employers->isEmpty())
            <div class="text-center py-10 text-xs font-semibold text-slate-600">
                <p class="text-sm font-bold text-slate-800">No employer accounts match your query.</p>
                <p class="text-slate-500 mt-1">Try clearing your search filter above.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Company / Employer</th>
                            <th class="py-3 px-3">Contact & Address</th>
                            <th class="py-3 px-3">Published Posts</th>
                            <th class="py-3 px-3">Registered Date</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($employers as $employer)
                            <tr class="hover:bg-sky-50/40 transition-colors">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-sky-500 text-white font-black flex items-center justify-center text-xs shadow-sm flex-shrink-0">
                                            {{ strtoupper(substr($employer->company_name ?? $employer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-sm text-slate-950 block">
                                                {{ $employer->company_name ?? $employer->name }}
                                            </span>
                                            <span class="text-[11px] text-slate-600 font-medium">
                                                Contact Person: {{ $employer->name }} • {{ $employer->email }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-slate-800 max-w-[220px]">
                                    <p class="font-bold text-slate-900">{{ $employer->contact_number ?? 'No contact number' }}</p>
                                    <p class="text-[11px] text-slate-600 truncate">📍 {{ $employer->address ?? 'Philippines' }}</p>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center gap-1 font-bold text-sky-800 bg-sky-100/80 px-2.5 py-1 rounded-lg border border-sky-200">
                                        <svg class="w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        {{ $employer->job_posts_count }} Post{{ $employer->job_posts_count == 1 ? '' : 's' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-slate-600 font-medium text-[11px]">
                                    {{ $employer->created_at->format('M d, Y') }}
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.users.edit', $employer) }}"
                                            class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                                            Edit
                                        </a>

                                        @if($employer->id !== auth()->id())
                                            <button type="button"
                                                onclick="confirmAction({
                                                    title: 'Delete Employer Account?',
                                                    message: 'Are you sure you want to permanently delete employer {{ addslashes($employer->display_name) }}? All their job posts and applicant records will be permanently removed.',
                                                    formAction: '{{ route('admin.users.destroy', $employer) }}',
                                                    method: 'DELETE',
                                                    confirmText: 'Yes, Delete Employer',
                                                    confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                                })"
                                                class="px-2.5 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 font-bold text-xs hover:bg-red-100 transition-colors">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-sky-100">
                {{ $employers->links() }}
            </div>
        @endif

    </div>

    <!-- ========================================== -->
    <!-- CONTAINER 2: WORKERS CONTAINER            -->
    <!-- ========================================== -->
    <div id="workersContainer" class="glass-panel p-6 sm:p-7 rounded-3xl border border-emerald-200/80 bg-white/95 shadow-xl space-y-4">
        
        <!-- Workers Container Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-emerald-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white font-black flex items-center justify-center text-lg shadow-md shadow-emerald-600/20">
                    👷
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-950 flex items-center gap-2">
                        <span>Registered Workers</span>
                        <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-200">
                            {{ $workers->total() }} Accounts
                        </span>
                    </h2>
                    <p class="text-xs font-semibold text-slate-600 mt-0.5">
                        Skilled job applicants, technicians, laborers, and trade professionals
                    </p>
                </div>
            </div>

            <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200 self-start sm:self-auto">
                Container: Workers
            </span>
        </div>

        @if($workers->isEmpty())
            <div class="text-center py-10 text-xs font-semibold text-slate-600">
                <p class="text-sm font-bold text-slate-800">No worker accounts match your query.</p>
                <p class="text-slate-500 mt-1">Try clearing your search filter above.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Worker Profile</th>
                            <th class="py-3 px-3">Sex & Address</th>
                            <th class="py-3 px-3">Contact Number</th>
                            <th class="py-3 px-3">Applications Submitted</th>
                            <th class="py-3 px-3">Registered Date</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($workers as $worker)
                            <tr class="hover:bg-emerald-50/40 transition-colors">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-xs shadow-sm flex-shrink-0">
                                            {{ strtoupper(substr($worker->display_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-sm text-slate-950 block">
                                                {{ $worker->display_name }}
                                            </span>
                                            <span class="text-[11px] text-slate-600 font-medium">
                                                {{ $worker->email }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-slate-800 max-w-[200px]">
                                    <p class="font-bold text-slate-900">{{ $worker->sex ?? 'Unspecified' }}</p>
                                    <p class="text-[11px] text-slate-600 truncate">📍 {{ $worker->address ?? 'Philippines' }}</p>
                                </td>

                                <td class="py-3.5 px-3 text-slate-800">
                                    <span class="font-bold text-slate-900">{{ $worker->contact_number ?? 'No phone set' }}</span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center gap-1 font-bold text-emerald-800 bg-emerald-100/80 px-2.5 py-1 rounded-lg border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        {{ $worker->applications_count }} Applied
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-slate-600 font-medium text-[11px]">
                                    {{ $worker->created_at->format('M d, Y') }}
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.users.edit', $worker) }}"
                                            class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                                            Edit
                                        </a>

                                        @if($worker->id !== auth()->id())
                                            <button type="button"
                                                onclick="confirmAction({
                                                    title: 'Delete Worker Account?',
                                                    message: 'Are you sure you want to permanently delete worker {{ addslashes($worker->display_name) }}? All their job applications and attachments will be deleted permanently.',
                                                    formAction: '{{ route('admin.users.destroy', $worker) }}',
                                                    method: 'DELETE',
                                                    confirmText: 'Yes, Delete Worker',
                                                    confirmClass: 'bg-red-600 hover:bg-red-700 text-white'
                                                })"
                                                class="px-2.5 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 font-bold text-xs hover:bg-red-100 transition-colors">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-emerald-100">
                {{ $workers->links() }}
            </div>
        @endif

    </div>

    <!-- ========================================== -->
    <!-- CONTAINER 3: ADMINISTRATORS SUMMARY        -->
    <!-- ========================================== -->
    @if($admins->isNotEmpty())
        <div id="adminsContainer" class="glass-panel p-6 rounded-3xl border border-purple-200/80 bg-white/95 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-purple-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-600 text-white font-black flex items-center justify-center text-sm shadow">
                        🛡️
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-950">System Administrators ({{ count($admins) }})</h2>
                        <p class="text-xs font-semibold text-slate-600">Full platform privileges and system configuration access</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($admins as $adminUser)
                    <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-200/60 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white font-black flex items-center justify-center text-sm shadow-sm flex-shrink-0">
                                {{ strtoupper(substr($adminUser->display_name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-black text-slate-950 truncate">{{ $adminUser->display_name }}</p>
                                <p class="text-[11px] text-purple-700 font-semibold truncate">{{ $adminUser->email }}</p>
                                <span class="text-[10px] text-slate-500 font-semibold">Joined {{ $adminUser->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <a href="{{ route('admin.users.edit', $adminUser) }}" class="px-2.5 py-1 rounded-lg bg-white border border-slate-300 text-slate-800 font-bold text-xs hover:bg-slate-50">
                                Edit
                            </a>
                            @if($adminUser->id === auth()->id())
                                <span class="text-[10px] font-bold text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">You</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
