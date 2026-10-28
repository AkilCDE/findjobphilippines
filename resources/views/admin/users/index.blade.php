@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header & Summary Stats Banner (Monochrome Dark) -->
    <div class="bg-[#121215] p-6 sm:p-8 rounded-3xl border border-zinc-800 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-zinc-400 hover:text-white flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to Dashboard
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                User Management
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-zinc-400 mt-1">
                Employer and worker accounts organized in separated containers for administrative supervision.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-black text-zinc-200 bg-zinc-900 px-3 py-1.5 rounded-full border border-zinc-700">
                🏢 {{ $employersCount }} Employers
            </span>
            <span class="text-xs font-black text-zinc-200 bg-zinc-900 px-3 py-1.5 rounded-full border border-zinc-700">
                👷 {{ $workersCount }} Workers
            </span>
            <span class="text-xs font-black text-white bg-zinc-800 px-3 py-1.5 rounded-full border border-zinc-600">
                🛡️ {{ count($admins) }} Admins
            </span>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-[#121215] p-4 rounded-2xl border border-zinc-800 shadow-md">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, company, email, phone, or address..."
                    class="w-full px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-800 text-white placeholder-zinc-500 text-xs font-semibold focus:border-white focus:outline-none">
            </div>

            <div class="w-36">
                <select name="sort" class="w-full px-3 py-2.5 rounded-xl bg-[#18181b] border border-zinc-800 text-white text-xs font-semibold focus:border-white focus:outline-none">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-black text-xs shadow-md transition-all">
                Search Accounts
            </button>

            @if(request()->hasAny(['search', 'sort']))
                <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold text-xs transition-colors">
                    Reset
                </a>
            @endif
        </form>

        <!-- Quick Jump Buttons to Containers -->
        <div class="mt-3 pt-3 border-t border-zinc-800/80 flex items-center gap-2 text-xs font-bold text-zinc-300 flex-wrap">
            <span class="text-zinc-500 uppercase text-[10px] tracking-wider">Quick Jump:</span>
            <a href="#employersContainer" class="px-3 py-1 rounded-lg bg-zinc-900 text-zinc-300 hover:bg-zinc-800 hover:text-white border border-zinc-800 transition-colors">
                🏢 Employers Container &darr;
            </a>
            <a href="#workersContainer" class="px-3 py-1 rounded-lg bg-zinc-900 text-zinc-300 hover:bg-zinc-800 hover:text-white border border-zinc-800 transition-colors">
                👷 Workers Container &darr;
            </a>
            @if($admins->isNotEmpty())
                <a href="#adminsContainer" class="px-3 py-1 rounded-lg bg-zinc-900 text-zinc-300 hover:bg-zinc-800 hover:text-white border border-zinc-800 transition-colors">
                    🛡️ Admins &darr;
                </a>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CONTAINER 1: EMPLOYERS CONTAINER          -->
    <!-- ========================================== -->
    <div id="employersContainer" class="bg-[#121215] p-6 sm:p-7 rounded-3xl border border-zinc-800 shadow-2xl space-y-4">
        
        <!-- Employers Container Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-800/80 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-zinc-900 border border-zinc-700 text-white font-black flex items-center justify-center text-lg shadow-sm">
                    🏢
                </div>
                <div>
                    <h2 class="text-lg font-black text-white flex items-center gap-2">
                        <span>Registered Employers</span>
                        <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-zinc-800 text-zinc-200 border border-zinc-700">
                            {{ $employers->total() }} Accounts
                        </span>
                    </h2>
                    <p class="text-xs font-semibold text-zinc-400 mt-0.5">
                        Company owners, business managers, and recruiting organizations
                    </p>
                </div>
            </div>

            <span class="text-[11px] font-bold text-zinc-400 bg-zinc-900 px-3 py-1 rounded-xl border border-zinc-800 self-start sm:self-auto">
                Container: Employers
            </span>
        </div>

        @if($employers->isEmpty())
            <div class="text-center py-10 text-xs font-semibold text-zinc-400">
                <p class="text-sm font-bold text-white">No employer accounts match your query.</p>
                <p class="text-zinc-500 mt-1">Try clearing your search filter above.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Company / Employer</th>
                            <th class="py-3 px-3">Contact & Address</th>
                            <th class="py-3 px-3">Published Posts</th>
                            <th class="py-3 px-3">Registered Date</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/70">
                        @foreach($employers as $employer)
                            <tr class="hover:bg-zinc-900/50 transition-colors">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-zinc-900 text-white border border-zinc-700 font-black flex items-center justify-center text-xs shadow-sm flex-shrink-0">
                                            {{ strtoupper(substr($employer->company_name ?? $employer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-sm text-white block">
                                                {{ $employer->company_name ?? $employer->name }}
                                            </span>
                                            <span class="text-[11px] text-zinc-400 font-medium">
                                                Contact Person: {{ $employer->name }} • {{ $employer->email }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-zinc-300 max-w-[220px]">
                                    <p class="font-bold text-white">{{ $employer->contact_number ?? 'No contact number' }}</p>
                                    <p class="text-[11px] text-zinc-400 truncate">📍 {{ $employer->address ?? 'Philippines' }}</p>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center gap-1 font-bold text-zinc-200 bg-zinc-900 px-2.5 py-1 rounded-lg border border-zinc-700">
                                        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        {{ $employer->job_posts_count }} Post{{ $employer->job_posts_count == 1 ? '' : 's' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-zinc-400 font-medium text-[11px]">
                                    {{ $employer->created_at->format('M d, Y') }}
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.users.edit', $employer) }}"
                                            class="px-3 py-1.5 rounded-lg border border-zinc-700 bg-zinc-900 hover:bg-white hover:text-black text-zinc-200 font-bold text-xs transition-colors">
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
                                                    confirmClass: 'bg-white hover:bg-zinc-200 text-black'
                                                })"
                                                class="px-2.5 py-1.5 rounded-lg border border-zinc-800 bg-zinc-900 text-zinc-400 hover:text-white font-bold text-xs hover:bg-zinc-800 transition-colors">
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

            <div class="mt-4 pt-3 border-t border-zinc-800">
                {{ $employers->links() }}
            </div>
        @endif

    </div>

    <!-- ========================================== -->
    <!-- CONTAINER 2: WORKERS CONTAINER            -->
    <!-- ========================================== -->
    <div id="workersContainer" class="bg-[#121215] p-6 sm:p-7 rounded-3xl border border-zinc-800 shadow-2xl space-y-4">
        
        <!-- Workers Container Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-800/80 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-zinc-900 border border-zinc-700 text-white font-black flex items-center justify-center text-lg shadow-sm">
                    👷
                </div>
                <div>
                    <h2 class="text-lg font-black text-white flex items-center gap-2">
                        <span>Registered Workers</span>
                        <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-zinc-800 text-zinc-200 border border-zinc-700">
                            {{ $workers->total() }} Accounts
                        </span>
                    </h2>
                    <p class="text-xs font-semibold text-zinc-400 mt-0.5">
                        Skilled job applicants, technicians, laborers, and trade professionals
                    </p>
                </div>
            </div>

            <span class="text-[11px] font-bold text-zinc-400 bg-zinc-900 px-3 py-1 rounded-xl border border-zinc-800 self-start sm:self-auto">
                Container: Workers
            </span>
        </div>

        @if($workers->isEmpty())
            <div class="text-center py-10 text-xs font-semibold text-zinc-400">
                <p class="text-sm font-bold text-white">No worker accounts match your query.</p>
                <p class="text-zinc-500 mt-1">Try clearing your search filter above.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Worker Profile</th>
                            <th class="py-3 px-3">Sex & Address</th>
                            <th class="py-3 px-3">Contact Number</th>
                            <th class="py-3 px-3">Applications Submitted</th>
                            <th class="py-3 px-3">Registered Date</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/70">
                        @foreach($workers as $worker)
                            <tr class="hover:bg-zinc-900/50 transition-colors">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-zinc-900 text-white border border-zinc-700 font-black flex items-center justify-center text-xs shadow-sm flex-shrink-0">
                                            {{ strtoupper(substr($worker->display_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-sm text-white block">
                                                {{ $worker->display_name }}
                                            </span>
                                            <span class="text-[11px] text-zinc-400 font-medium">
                                                {{ $worker->email }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-zinc-300 max-w-[200px]">
                                    <p class="font-bold text-white">{{ $worker->sex ?? 'Unspecified' }}</p>
                                    <p class="text-[11px] text-zinc-400 truncate">📍 {{ $worker->address ?? 'Philippines' }}</p>
                                </td>

                                <td class="py-3.5 px-3 text-zinc-300">
                                    <span class="font-bold text-white">{{ $worker->contact_number ?? 'No phone set' }}</span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center gap-1 font-bold text-zinc-200 bg-zinc-900 px-2.5 py-1 rounded-lg border border-zinc-700">
                                        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        {{ $worker->applications_count }} Applied
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-zinc-400 font-medium text-[11px]">
                                    {{ $worker->created_at->format('M d, Y') }}
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.users.edit', $worker) }}"
                                            class="px-3 py-1.5 rounded-lg border border-zinc-700 bg-zinc-900 hover:bg-white hover:text-black text-zinc-200 font-bold text-xs transition-colors">
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
                                                    confirmClass: 'bg-white hover:bg-zinc-200 text-black'
                                                })"
                                                class="px-2.5 py-1.5 rounded-lg border border-zinc-800 bg-zinc-900 text-zinc-400 hover:text-white font-bold text-xs hover:bg-zinc-800 transition-colors">
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

            <div class="mt-4 pt-3 border-t border-zinc-800">
                {{ $workers->links() }}
            </div>
        @endif

    </div>

    <!-- ========================================== -->
    <!-- CONTAINER 3: ADMINISTRATORS SUMMARY        -->
    <!-- ========================================== -->
    @if($admins->isNotEmpty())
        <div id="adminsContainer" class="bg-[#121215] p-6 rounded-3xl border border-zinc-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-800/80 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-black flex items-center justify-center text-sm shadow">
                        🛡️
                    </div>
                    <div>
                        <h2 class="text-base font-black text-white">System Administrators ({{ count($admins) }})</h2>
                        <p class="text-xs font-semibold text-zinc-400">Full platform privileges and system configuration access</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($admins as $adminUser)
                    <div class="p-4 rounded-2xl bg-zinc-900/60 border border-zinc-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-white text-black font-black flex items-center justify-center text-sm shadow-sm flex-shrink-0">
                                {{ strtoupper(substr($adminUser->display_name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-black text-white truncate">{{ $adminUser->display_name }}</p>
                                <p class="text-[11px] text-zinc-400 font-semibold truncate">{{ $adminUser->email }}</p>
                                <span class="text-[10px] text-zinc-500 font-semibold">Joined {{ $adminUser->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <a href="{{ route('admin.users.edit', $adminUser) }}" class="px-2.5 py-1 rounded-lg bg-zinc-800 border border-zinc-700 text-zinc-200 font-bold text-xs hover:bg-white hover:text-black transition-colors">
                                Edit
                            </a>
                            @if($adminUser->id === auth()->id())
                                <span class="text-[10px] font-bold text-white bg-zinc-800 border border-zinc-700 px-2 py-0.5 rounded-full">You</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
