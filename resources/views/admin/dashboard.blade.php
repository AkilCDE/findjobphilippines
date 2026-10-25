@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Admin Header Banner -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-purple-100 text-purple-900 border border-purple-200">
                    Administrator Command Center
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                Admin Dashboard & Analytics
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1">
                Real-time metrics, platform growth graphs, and system administration
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-md shadow-purple-500/20 transition-all hover:scale-[1.02]">
                Manage Users
            </a>
            <a href="{{ route('admin.posts.index') }}"
                class="px-4 py-2.5 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs shadow-sm transition-colors">
                Manage Job Posts
            </a>
        </div>
    </div>

    <!-- System Metrics Grid (4 Key Counters) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="glass-card p-5 rounded-3xl border border-purple-100 bg-white/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] font-bold uppercase text-slate-500">Total Users</p>
                <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-black">👥</div>
            </div>
            <p class="text-3xl font-black text-slate-950">{{ $totalUsers }}</p>
            <p class="text-[11px] font-semibold text-purple-800 mt-1">
                {{ $workersCount }} Workers • {{ $employersCount }} Employers
            </p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-sky-100 bg-white/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] font-bold uppercase text-slate-500">Total Job Postings</p>
                <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-xs font-black">💼</div>
            </div>
            <p class="text-3xl font-black text-slate-950">{{ $totalPosts }}</p>
            <p class="text-[11px] font-semibold text-sky-800 mt-1">
                {{ $openPosts }} Open • {{ $lockedPosts }} Locked
            </p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-emerald-100 bg-white/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] font-bold uppercase text-slate-500">Applications</p>
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-black">📝</div>
            </div>
            <p class="text-3xl font-black text-slate-950">{{ $totalApplications }}</p>
            <p class="text-[11px] font-semibold text-emerald-800 mt-1">
                {{ $totalHired }} Workers Hired
            </p>
        </div>

        <div class="glass-card p-5 rounded-3xl border border-slate-100 bg-white/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] font-bold uppercase text-slate-500">Messages Sent</p>
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black">💬</div>
            </div>
            <p class="text-3xl font-black text-slate-950">{{ $totalMessages }}</p>
            <p class="text-[11px] font-semibold text-slate-600 mt-1">Direct Employer-Worker</p>
        </div>

    </div>

    <!-- ==================== INTERACTIVE ANALYTICS GRAPHS ==================== -->
    <div class="space-y-6">
        
        <!-- Header for Graphs -->
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-black text-slate-950 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Platform Analytics & Trends
            </h2>
            <span class="text-xs font-bold text-slate-500">Live Visual Reports</span>
        </div>

        <!-- Graphs Grid: Row 1 (Activity Trend & User Distribution) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- 1. Line Chart: 7-Day Application & Hiring Trend (2 cols) -->
            <div class="lg:col-span-2 glass-panel p-6 rounded-3xl border border-white/80 shadow-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-black text-slate-950">
                            Application & Hiring Trend
                        </h3>
                        <p class="text-xs text-slate-500 font-semibold">
                            Daily submitted applications vs. confirmed hires (Past 7 Days)
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <span class="inline-flex items-center gap-1.5 text-purple-700">
                            <span class="w-3 h-3 rounded-full bg-purple-600"></span> Applications
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-emerald-700">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Hired Workers
                        </span>
                    </div>
                </div>

                <div class="h-64 sm:h-72 w-full relative">
                    <canvas id="activityTrendChart"></canvas>
                </div>
            </div>

            <!-- 2. Doughnut Chart: User Breakdown (1 col) -->
            <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-950">
                        User Composition
                    </h3>
                    <p class="text-xs text-slate-500 font-semibold">
                        Registered platform accounts by role
                    </p>
                </div>

                <div class="h-64 sm:h-72 w-full relative flex items-center justify-center">
                    <canvas id="userCompositionChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Graphs Grid: Row 2 (Top Categories & Status Breakdown) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- 3. Bar Chart: Top Job Categories -->
            <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-950">
                        Top Job Posting Categories
                    </h3>
                    <p class="text-xs text-slate-500 font-semibold">
                        Number of listings per employment category
                    </p>
                </div>

                <div class="h-64 w-full relative">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>

            <!-- 4. Doughnut/Polar Chart: Application Status Outcomes -->
            <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-950">
                        Application Status Outcomes
                    </h3>
                    <p class="text-xs text-slate-500 font-semibold">
                        Hired, pending, leave requested, and completed placements
                    </p>
                </div>

                <div class="h-64 w-full relative flex items-center justify-center">
                    <canvas id="applicationStatusChart"></canvas>
                </div>
            </div>

        </div>

    </div>

    <!-- ==================== RECENT DATA TABLES ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
        
        <!-- Recent Users Table -->
        <div class="glass-panel p-6 rounded-3xl border border-white/80 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-black text-slate-950">Recent Registered Users</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-purple-700 hover:text-purple-900 flex items-center gap-1">
                    <span>View All ({{ $totalUsers }})</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="text-[10px] text-slate-500 uppercase border-b border-slate-100">
                            <th class="py-2.5 px-2">User</th>
                            <th class="py-2.5 px-2">Role</th>
                            <th class="py-2.5 px-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentUsers as $u)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-2">
                                    <span class="font-bold text-slate-950 block">{{ $u->display_name }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $u->email }}</span>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $u->isAdmin() ? 'bg-purple-100 text-purple-900 border border-purple-200' : ($u->isEmployer() ? 'bg-sky-100 text-sky-900 border border-sky-200' : 'bg-emerald-100 text-emerald-900 border border-emerald-200') }}">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-purple-50 text-purple-700 font-bold transition-colors">
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
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-black text-slate-950">Recent Job Posts</h3>
                <a href="{{ route('admin.posts.index') }}" class="text-xs font-bold text-purple-700 hover:text-purple-900 flex items-center gap-1">
                    <span>Manage Posts ({{ $totalPosts }})</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="text-[10px] text-slate-500 uppercase border-b border-slate-100">
                            <th class="py-2.5 px-2">Job Title</th>
                            <th class="py-2.5 px-2">Employer</th>
                            <th class="py-2.5 px-2">Capacity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentPosts as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-2">
                                    <a href="{{ route('jobs.show', $p) }}" target="_blank" class="font-bold text-slate-950 hover:text-purple-700 block line-clamp-1">
                                        {{ $p->title }}
                                    </a>
                                    <span class="text-[10px] text-slate-500 font-semibold">{{ $p->location_address }}</span>
                                </td>
                                <td class="py-3 px-2 font-bold text-slate-800">
                                    {{ $p->employer?->display_name ?? 'Employer' }}
                                </td>
                                <td class="py-3 px-2">
                                    <span class="text-xs font-extrabold {{ $p->isLocked() ? 'text-amber-700' : 'text-sky-700' }}">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart.js global defaults
        Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. Line Chart: Activity Trend (7 Days)
        const trendCtx = document.getElementById('activityTrendChart').getContext('2d');
        const gradientApps = trendCtx.createLinearGradient(0, 0, 0, 240);
        gradientApps.addColorStop(0, 'rgba(147, 51, 234, 0.3)');
        gradientApps.addColorStop(1, 'rgba(147, 51, 234, 0.0)');

        const gradientHires = trendCtx.createLinearGradient(0, 0, 0, 240);
        gradientHires.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradientHires.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($trendDates),
                datasets: [
                    {
                        label: 'Applications',
                        data: @json($trendApplications),
                        borderColor: '#9333ea',
                        backgroundColor: gradientApps,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#9333ea',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Hired Workers',
                        data: @json($trendHires),
                        borderColor: '#10b981',
                        backgroundColor: gradientHires,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: { weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: 'rgba(226, 232, 240, 0.8)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // 2. Doughnut Chart: User Composition
        const userCtx = document.getElementById('userCompositionChart').getContext('2d');
        new Chart(userCtx, {
            type: 'doughnut',
            data: {
                labels: ['Workers', 'Employers', 'Admins'],
                datasets: [{
                    data: [{{ $workersCount }}, {{ $employersCount }}, {{ $adminsCount }}],
                    backgroundColor: ['#10b981', '#0284c7', '#9333ea'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 14,
                            font: { weight: '600', size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 10
                    }
                }
            }
        });

        // 3. Bar Chart: Job Categories
        const catCtx = document.getElementById('categoriesChart').getContext('2d');
        const catLabels = @json(array_keys($categoryData));
        const catCounts = @json(array_values($categoryData));

        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: catLabels.length ? catLabels : ['General Labor'],
                datasets: [{
                    label: 'Job Postings',
                    data: catCounts.length ? catCounts : [{{ $totalPosts }}],
                    backgroundColor: '#8b5cf6',
                    borderRadius: 8,
                    maxBarThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 10
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: 'rgba(226, 232, 240, 0.8)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600', size: 10 } }
                    }
                }
            }
        });

        // 4. Doughnut Chart: Application Status Breakdown
        const appStatusCtx = document.getElementById('applicationStatusChart').getContext('2d');
        const appStatusLabels = @json(array_keys($applicationStatusData));
        const appStatusValues = @json(array_values($applicationStatusData));

        new Chart(appStatusCtx, {
            type: 'doughnut',
            data: {
                labels: appStatusLabels,
                datasets: [{
                    data: appStatusValues,
                    backgroundColor: [
                        '#10b981', // Hired
                        '#0284c7', // Pending
                        '#f59e0b', // Leave Requested
                        '#64748b', // Resigned
                        '#ef4444', // Fired
                        '#dc2626'  // Rejected
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            padding: 10,
                            font: { weight: '600', size: 10 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 10
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
