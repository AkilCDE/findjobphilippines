@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Admin Header Banner (Monochrome Dark) -->
    <div class="bg-[#121215] p-6 sm:p-8 rounded-3xl border border-zinc-800/90 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-zinc-800 text-zinc-200 border border-zinc-700">
                    Administrator Command Center
                </span>
                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-white text-black">
                    Live System
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Admin Dashboard & Analytics
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-zinc-400 mt-1">
                Real-time platform activity metrics, user growth analytics, and verified employment data.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="px-5 py-2.5 rounded-2xl bg-white hover:bg-zinc-200 text-black font-black text-xs shadow-md transition-all hover:scale-[1.02]">
                Manage Users
            </a>
            <a href="{{ route('admin.posts.index') }}"
                class="px-5 py-2.5 rounded-2xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 hover:text-white font-bold text-xs shadow-sm transition-colors">
                Manage Job Posts
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SYSTEM METRICS GRID (4 KEY COUNTER CARDS) -->
    <!-- ========================================== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Users -->
        <div class="bg-[#121215] p-5 sm:p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 transition-all shadow-md group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Users</p>
                <div class="w-8 h-8 rounded-xl bg-zinc-900 border border-zinc-700 text-white flex items-center justify-center text-xs font-black">
                    👥
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-black text-white">{{ number_format($totalUsers) }}</p>
            <div class="flex items-center gap-2 mt-2 text-[11px] font-semibold text-zinc-400">
                <span>{{ $workersCount }} Workers</span>
                <span class="text-zinc-600">•</span>
                <span>{{ $employersCount }} Employers</span>
            </div>
        </div>

        <!-- Total Job Postings -->
        <div class="bg-[#121215] p-5 sm:p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 transition-all shadow-md group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Job Listings</p>
                <div class="w-8 h-8 rounded-xl bg-zinc-900 border border-zinc-700 text-white flex items-center justify-center text-xs font-black">
                    💼
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-black text-white">{{ number_format($totalPosts) }}</p>
            <div class="flex items-center gap-2 mt-2 text-[11px] font-semibold text-zinc-400">
                <span class="text-white font-bold">{{ $openPosts }} Open</span>
                <span class="text-zinc-600">•</span>
                <span>{{ $lockedPosts }} Locked</span>
                <span class="text-zinc-600">•</span>
                <span>{{ $archivedPosts }} Archived</span>
            </div>
        </div>

        <!-- Applications & Hires -->
        <div class="bg-[#121215] p-5 sm:p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 transition-all shadow-md group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Applications</p>
                <div class="w-8 h-8 rounded-xl bg-zinc-900 border border-zinc-700 text-white flex items-center justify-center text-xs font-black">
                    📝
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-black text-white">{{ number_format($totalApplications) }}</p>
            <div class="flex items-center gap-2 mt-2 text-[11px] font-semibold text-zinc-400">
                <span class="text-white font-bold">{{ $totalHired }} Hired</span>
                <span class="text-zinc-600">•</span>
                <span>{{ $totalApplications > 0 ? round(($totalHired / $totalApplications) * 100, 1) : 0 }}% Conversion</span>
            </div>
        </div>

        <!-- Total Messages -->
        <div class="bg-[#121215] p-5 sm:p-6 rounded-3xl border border-zinc-800 hover:border-zinc-700 transition-all shadow-md group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Direct Messages</p>
                <div class="w-8 h-8 rounded-xl bg-zinc-900 border border-zinc-700 text-white flex items-center justify-center text-xs font-black">
                    💬
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-black text-white">{{ number_format($totalMessages) }}</p>
            <p class="text-[11px] font-semibold text-zinc-400 mt-2">
                Employer & Worker Exchanges
            </p>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- MODERN ANALYTICS GRAPHS (NO MOUNTAINS!)   -->
    <!-- ========================================== -->
    <div class="space-y-6">
        
        <!-- Row 1: Main Activity Graph (Sleek Columns with Toggle) & User Ring -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Graph 1: Platform Activity Trend (Grouped Columns / Modern Bars) -->
            <div class="lg:col-span-2 bg-[#121215] p-6 rounded-3xl border border-zinc-800/90 shadow-2xl flex flex-col justify-between space-y-4">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-800/80 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-black text-white" id="mainChartTitle">
                                7-Day Application & Hiring Volume
                            </h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300 border border-zinc-700">
                                Live
                            </span>
                        </div>
                        <p class="text-xs text-zinc-400 font-semibold mt-0.5" id="mainChartSubtitle">
                            Daily verified worker applications submitted vs. confirmed hires
                        </p>
                    </div>

                    <!-- View Mode Toggle Buttons -->
                    <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-black border border-zinc-800">
                        <button type="button" id="btnModeDaily" onclick="switchActivityMode('daily')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-black shadow-sm">
                            7-Day Daily
                        </button>
                        <button type="button" id="btnModeMonthly" onclick="switchActivityMode('monthly')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-zinc-400 hover:text-white">
                            6-Month Posts
                        </button>
                    </div>
                </div>

                <!-- Chart Canvas Container -->
                <div class="h-64 sm:h-72 w-full relative">
                    <canvas id="mainActivityChart"></canvas>
                </div>

                <!-- Legend & Quick Metrics Strip -->
                <div class="pt-3 border-t border-zinc-850 flex flex-wrap items-center justify-between gap-3 text-xs font-semibold">
                    <div class="flex items-center gap-4" id="dailyLegend">
                        <span class="inline-flex items-center gap-2 text-white font-bold">
                            <span class="w-3 h-3 rounded-md bg-white"></span>
                            <span>Applications ({{ array_sum($trendApplications) }})</span>
                        </span>
                        <span class="inline-flex items-center gap-2 text-zinc-400 font-bold">
                            <span class="w-3 h-3 rounded-md bg-zinc-500"></span>
                            <span>Hires ({{ array_sum($trendHires) }})</span>
                        </span>
                    </div>

                    <div class="text-zinc-500 text-[11px]">
                        Last updated {{ now()->format('h:i A') }} • Anti-aliased vector rendering
                    </div>
                </div>

            </div>

            <!-- Graph 2: User Composition (Executive Modern Donut Ring with Center Stat) -->
            <div class="bg-[#121215] p-6 rounded-3xl border border-zinc-800/90 shadow-2xl flex flex-col justify-between space-y-4">
                
                <div class="border-b border-zinc-800/80 pb-3">
                    <h3 class="text-base font-black text-white">
                        User Composition
                    </h3>
                    <p class="text-xs text-zinc-400 font-semibold mt-0.5">
                        Account distribution by system role
                    </p>
                </div>

                <!-- Donut Chart with Center Label -->
                <div class="relative h-56 w-full flex items-center justify-center">
                    <canvas id="userDonutChart"></canvas>
                    
                    <!-- Center Metric -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl sm:text-3xl font-black text-white">{{ $totalUsers }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Total Users</span>
                    </div>
                </div>

                <!-- Breakdown Metrics List -->
                <div class="space-y-2 pt-2 border-t border-zinc-850 text-xs">
                    <div class="flex items-center justify-between font-bold">
                        <span class="flex items-center gap-2 text-white">
                            <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
                            <span>Workers</span>
                        </span>
                        <span class="text-zinc-300">
                            {{ $workersCount }} <span class="text-zinc-500">({{ $totalUsers > 0 ? round(($workersCount / $totalUsers) * 100, 1) : 0 }}%)</span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between font-bold">
                        <span class="flex items-center gap-2 text-zinc-300">
                            <span class="w-2.5 h-2.5 rounded-full bg-zinc-400"></span>
                            <span>Employers</span>
                        </span>
                        <span class="text-zinc-300">
                            {{ $employersCount }} <span class="text-zinc-500">({{ $totalUsers > 0 ? round(($employersCount / $totalUsers) * 100, 1) : 0 }}%)</span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between font-bold">
                        <span class="flex items-center gap-2 text-zinc-400">
                            <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                            <span>Admins</span>
                        </span>
                        <span class="text-zinc-400">
                            {{ $adminsCount }} <span class="text-zinc-600">({{ $totalUsers > 0 ? round(($adminsCount / $totalUsers) * 100, 1) : 0 }}%)</span>
                        </span>
                    </div>
                </div>

            </div>

        </div>

        <!-- Row 2: Top Categories (Horizontal Ranking Bars) & Application Funnel -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Graph 3: Horizontal Ranking Bars for Top Job Categories -->
            <div class="bg-[#121215] p-6 rounded-3xl border border-zinc-800/90 shadow-2xl space-y-4">
                
                <div class="flex items-center justify-between border-b border-zinc-800/80 pb-3">
                    <div>
                        <h3 class="text-base font-black text-white">
                            Top Job Categories
                        </h3>
                        <p class="text-xs text-zinc-400 font-semibold mt-0.5">
                            Active postings volume ranked by industry
                        </p>
                    </div>
                    <span class="text-xs font-bold text-zinc-400">
                        {{ count($categoryData) }} Categories
                    </span>
                </div>

                <div class="h-64 w-full relative">
                    <canvas id="categoriesChart"></canvas>
                </div>

            </div>

            <!-- Graph 4: Application Pipeline & Lifecycle Status -->
            <div class="bg-[#121215] p-6 rounded-3xl border border-zinc-800/90 shadow-2xl space-y-4">
                
                <div class="flex items-center justify-between border-b border-zinc-800/80 pb-3">
                    <div>
                        <h3 class="text-base font-black text-white">
                            Application Status Pipeline
                        </h3>
                        <p class="text-xs text-zinc-400 font-semibold mt-0.5">
                            Status distribution of all candidate submissions
                        </p>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-zinc-900 border border-zinc-700 text-white">
                        {{ $totalApplications }} Total
                    </span>
                </div>

                <div class="h-64 w-full relative flex items-center justify-center">
                    <canvas id="applicationStatusChart"></canvas>
                </div>

            </div>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- RECENT DATA TABLES (MONOCHROME DARK)      -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
        
        <!-- Recent Users Table -->
        <div class="bg-[#121215] p-6 rounded-3xl border border-zinc-800/90 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-800/80 pb-3">
                <div>
                    <h3 class="text-base font-black text-white">Recent Registered Users</h3>
                    <p class="text-xs text-zinc-400 font-semibold">Latest accounts registered on Findjobph</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-zinc-300 hover:text-white flex items-center gap-1 transition-colors">
                    <span>View All ({{ $totalUsers }})</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="text-[10px] text-zinc-400 uppercase tracking-wider border-b border-zinc-800">
                            <th class="py-2.5 px-2">User</th>
                            <th class="py-2.5 px-2">Role</th>
                            <th class="py-2.5 px-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/70">
                        @forelse($recentUsers as $u)
                            <tr class="hover:bg-zinc-900/50 transition-colors">
                                <td class="py-3 px-2">
                                    <span class="font-bold text-white block">{{ $u->name }}</span>
                                    <div class="text-[11px] text-zinc-400">
                                        @if($u->isEmployer() && !empty($u->company_name))
                                            <span class="text-zinc-300 font-semibold block truncate max-w-[180px]">🏢 {{ $u->company_name }}</span>
                                        @endif
                                        <span>{{ $u->email }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $u->isAdmin() ? 'bg-white text-black' : 'bg-zinc-800 text-zinc-200 border border-zinc-700' }}">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <a href="{{ route('admin.users.edit', $u) }}"
                                        class="px-3 py-1 rounded-lg bg-zinc-800 hover:bg-white hover:text-black text-zinc-200 font-bold transition-all">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-zinc-500">No registered users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Posts Table -->
        <div class="bg-[#121215] p-6 rounded-3xl border border-zinc-800/90 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-800/80 pb-3">
                <div>
                    <h3 class="text-base font-black text-white">Recent Job Postings</h3>
                    <p class="text-xs text-zinc-400 font-semibold">Latest employer job opportunities</p>
                </div>
                <a href="{{ route('admin.posts.index') }}" class="text-xs font-bold text-zinc-300 hover:text-white flex items-center gap-1 transition-colors">
                    <span>Manage Posts ({{ $totalPosts }})</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-semibold">
                    <thead>
                        <tr class="text-[10px] text-zinc-400 uppercase tracking-wider border-b border-zinc-800">
                            <th class="py-2.5 px-2">Job Title</th>
                            <th class="py-2.5 px-2">Employer</th>
                            <th class="py-2.5 px-2 text-right">Quota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/70">
                        @forelse($recentPosts as $p)
                            <tr class="hover:bg-zinc-900/50 transition-colors">
                                <td class="py-3 px-2">
                                    <a href="{{ route('jobs.show', $p) }}" target="_blank" class="font-bold text-white hover:text-zinc-300 block line-clamp-1 transition-colors">
                                        {{ $p->title }}
                                    </a>
                                    <span class="text-[11px] text-zinc-400">{{ $p->category }}</span>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="text-zinc-200 font-bold block truncate max-w-[140px]">{{ $p->employer?->name ?? 'Employer' }}</span>
                                    @if(!empty($p->employer?->company_name))
                                        <span class="text-[10px] text-zinc-400 block truncate max-w-[140px]">🏢 {{ $p->employer->company_name }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <span class="text-[11px] font-black {{ $p->isLocked() ? 'text-zinc-400' : 'text-white' }}">
                                        {{ $p->workers_hired }}/{{ $p->workers_needed }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-zinc-500">No job postings created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Configure Global Chart.js Dark Mode Defaults
        Chart.defaults.color = '#a1a1aa';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.06)';
        Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, -apple-system, sans-serif";
        Chart.defaults.font.weight = '600';

        // Dark Tooltip Preset
        const darkTooltipOptions = {
            backgroundColor: '#18181b',
            titleColor: '#ffffff',
            bodyColor: '#e4e4e7',
            borderColor: '#3f3f46',
            borderWidth: 1,
            padding: 12,
            cornerRadius: 12,
            titleFont: { weight: 'bold', size: 12 },
            bodyFont: { weight: '600', size: 11 },
            boxPadding: 4
        };

        // ==========================================
        // 1. MAIN ACTIVITY GRAPH (CLEAN ARCHITECTURAL BARS - NO MOUNTAINS)
        // ==========================================
        const dailyDates = @json($trendDates);
        const dailyApps = @json($trendApplications);
        const dailyHires = @json($trendHires);

        const monthlyDates = @json($monthlyDates ?? []);
        const monthlyPosts = @json($monthlyPostsData ?? []);

        const actCtx = document.getElementById('mainActivityChart').getContext('2d');
        let currentMode = 'daily';

        window.mainChart = new Chart(actCtx, {
            type: 'bar',
            data: {
                labels: dailyDates,
                datasets: [
                    {
                        label: 'Applications',
                        data: dailyApps,
                        backgroundColor: '#ffffff',
                        borderRadius: 6,
                        maxBarThickness: 28,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Hired Workers',
                        data: dailyHires,
                        backgroundColor: '#71717a',
                        borderRadius: 6,
                        maxBarThickness: 28,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 600, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: darkTooltipOptions
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, color: '#71717a' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#a1a1aa' }
                    }
                }
            }
        });

        // Function to toggle between 7-Day Activity & 6-Month Postings
        window.switchActivityMode = function(mode) {
            currentMode = mode;
            const btnDaily = document.getElementById('btnModeDaily');
            const btnMonthly = document.getElementById('btnModeMonthly');
            const title = document.getElementById('mainChartTitle');
            const subtitle = document.getElementById('mainChartSubtitle');
            const legend = document.getElementById('dailyLegend');

            if (mode === 'daily') {
                btnDaily.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-black shadow-sm';
                btnMonthly.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-zinc-400 hover:text-white';
                title.textContent = '7-Day Application & Hiring Volume';
                subtitle.textContent = 'Daily verified worker applications submitted vs. confirmed hires';
                legend.style.display = 'flex';

                window.mainChart.data.labels = dailyDates;
                window.mainChart.data.datasets = [
                    {
                        label: 'Applications',
                        data: dailyApps,
                        backgroundColor: '#ffffff',
                        borderRadius: 6,
                        maxBarThickness: 28,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Hired Workers',
                        data: dailyHires,
                        backgroundColor: '#71717a',
                        borderRadius: 6,
                        maxBarThickness: 28,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    }
                ];
            } else {
                btnMonthly.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-black shadow-sm';
                btnDaily.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-zinc-400 hover:text-white';
                title.textContent = '6-Month Job Posting Growth';
                subtitle.textContent = 'Total job postings created by employers per month';
                legend.style.display = 'none';

                window.mainChart.data.labels = monthlyDates.length ? monthlyDates : ['Month'];
                window.mainChart.data.datasets = [
                    {
                        label: 'Job Postings Created',
                        data: monthlyPosts.length ? monthlyPosts : [{{ $totalPosts }}],
                        backgroundColor: '#ffffff',
                        borderRadius: 6,
                        maxBarThickness: 36
                    }
                ];
            }
            window.mainChart.update();
        };

        // ==========================================
        // 2. USER COMPOSITION DONUT RING (EXECUTIVE MONOCHROME)
        // ==========================================
        const userCtx = document.getElementById('userDonutChart').getContext('2d');
        new Chart(userCtx, {
            type: 'doughnut',
            data: {
                labels: ['Workers', 'Employers', 'Admins'],
                datasets: [{
                    data: [{{ $workersCount }}, {{ $employersCount }}, {{ $adminsCount }}],
                    backgroundColor: ['#ffffff', '#a1a1aa', '#3f3f46'],
                    borderWidth: 3,
                    borderColor: '#121215',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: { display: false },
                    tooltip: darkTooltipOptions
                }
            }
        });

        // ==========================================
        // 3. TOP JOB CATEGORIES (HORIZONTAL RANKING BARS)
        // ==========================================
        const catCtx = document.getElementById('categoriesChart').getContext('2d');
        const catLabels = @json(array_keys($categoryData));
        const catCounts = @json(array_values($categoryData));

        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: catLabels.length ? catLabels : ['General Labor'],
                datasets: [{
                    label: 'Job Listings',
                    data: catCounts.length ? catCounts : [{{ $totalPosts }}],
                    backgroundColor: [
                        '#ffffff',
                        '#e4e4e7',
                        '#d4d4d8',
                        '#a1a1aa',
                        '#71717a',
                        '#52525b'
                    ],
                    borderRadius: 5,
                    maxBarThickness: 22
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: darkTooltipOptions
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, color: '#71717a' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            color: '#e4e4e7',
                            font: { size: 11, weight: '700' }
                        }
                    }
                }
            }
        });

        // ==========================================
        // 4. APPLICATION STATUS PIPELINE (MONOCHROME DONUT)
        // ==========================================
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
                        '#ffffff', // Hired (Brightest)
                        '#e4e4e7', // Pending
                        '#a1a1aa', // Leave Requested
                        '#71717a', // Resigned
                        '#52525b', // Fired
                        '#3f3f46'  // Rejected
                    ],
                    borderWidth: 3,
                    borderColor: '#121215',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#a1a1aa',
                            boxWidth: 10,
                            padding: 10,
                            font: { weight: '600', size: 10 }
                        }
                    },
                    tooltip: darkTooltipOptions
                }
            }
        });
    });
</script>
@endpush
@endsection
