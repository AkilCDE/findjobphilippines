@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="glass-panel p-5 sm:p-6 rounded-2xl border border-white/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-950 tracking-tight">
                Philippine Job Map
            </h1>
            <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Workplace locations pinned by employers across the Philippines.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('jobs.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                View Job List
            </a>
            @auth
                @if(auth()->user()->isEmployer())
                    <a href="{{ route('employer.posts.create') }}" class="px-3.5 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-sm transition-colors">
                        + Post Job with Pin
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <!-- Map & Job Sidebar Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Interactive Map Container (8 cols) -->
        <div class="lg:col-span-8 glass-panel p-2 rounded-2xl border border-white/80 shadow-sm relative overflow-hidden flex flex-col">
            
            <!-- Quick Map Controls Filter Bar -->
            <div class="p-3 bg-white/80 border-b border-slate-200/80 rounded-t-xl flex flex-wrap items-center justify-between gap-3 text-xs z-10">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 font-bold text-sky-800">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span> Open Position
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-bold text-amber-800">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Locked (Full)
                    </span>
                </div>
                <button type="button" onclick="resetMapView()" class="text-sky-700 hover:text-sky-900 font-bold">
                    Reset Map Center
                </button>
            </div>

            <!-- Leaflet Map Element -->
            <div id="philippineJobsMap" class="w-full h-[540px] rounded-b-xl z-0"></div>

        </div>

        <!-- Pinned Jobs Directory Sidebar (4 cols) -->
        <div class="lg:col-span-4 glass-panel p-4 rounded-2xl border border-white/80 shadow-sm flex flex-col h-[600px]">
            <div class="mb-3 pb-2 border-b border-slate-200/80 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-950">Pinned Jobs</h3>
                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                    {{ count($jobs) }}
                </span>
            </div>

            <!-- Scrollable list of jobs -->
            <div class="flex-1 overflow-y-auto space-y-2.5 pr-1" id="jobSidebarList">
                @foreach($jobs as $job)
                    <div class="p-3 rounded-xl glass-card border border-white/80 cursor-pointer hover:border-sky-300 transition-all text-xs"
                        onclick="zoomToJob({{ $job['latitude'] }}, {{ $job['longitude'] }}, {{ $job['id'] }})">
                        
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-bold text-[10px] text-sky-800 bg-sky-50 px-1.5 py-0.5 rounded">
                                {{ $job['category'] }}
                            </span>
                            <span class="font-bold text-[10px] {{ $job['is_locked'] ? 'text-amber-800 bg-amber-50' : 'text-emerald-800 bg-emerald-50' }} px-1.5 py-0.5 rounded">
                                {{ $job['is_locked'] ? 'Locked' : 'Open' }}
                            </span>
                        </div>

                        <h4 class="font-black text-xs text-slate-950 hover:text-sky-700 line-clamp-1">
                            {{ $job['title'] }}
                        </h4>

                        <p class="text-[11px] font-semibold text-slate-600 mt-0.5">
                            {{ $job['employer_name'] }}
                        </p>

                        <p class="text-[11px] text-slate-700 truncate mt-1">
                            📍 {{ $job['location_address'] }}
                        </p>

                        <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between font-bold text-[11px]">
                            <span class="text-sky-800">
                                {{ $job['workers_hired'] }}/{{ $job['workers_needed'] }} Hired
                            </span>
                            <span class="text-emerald-700">
                                {{ $job['salary_range'] ?? 'Competitive' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    let map;
    const markers = {};
    const defaultCenter = [12.8797, 121.7740];
    const defaultZoom = 6;

    const jobsData = @json($jobs);

    document.addEventListener('DOMContentLoaded', function() {
        map = L.map('philippineJobsMap', {
            center: defaultCenter,
            zoom: defaultZoom,
            minZoom: 5,
            maxZoom: 18,
            scrollWheelZoom: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap | Findjobph',
            maxZoom: 19
        }).addTo(map);

        const openIcon = L.divIcon({
            className: 'custom-pin-open',
            html: '<div style="background-color: #0284c7; width: 26px; height: 26px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.25); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:13px;height:13px;margin-top:3.5px;margin-left:3.5px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [26, 26],
            iconAnchor: [13, 26],
            popupAnchor: [0, -26]
        });

        const lockedIcon = L.divIcon({
            className: 'custom-pin-locked',
            html: '<div style="background-color: #f59e0b; width: 26px; height: 26px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.25); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:13px;height:13px;margin-top:3.5px;margin-left:3.5px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [26, 26],
            iconAnchor: [13, 26],
            popupAnchor: [0, -26]
        });

        jobsData.forEach(job => {
            if (job.latitude && job.longitude) {
                const markerIcon = job.is_locked ? lockedIcon : openIcon;
                const marker = L.marker([job.latitude, job.longitude], { icon: markerIcon }).addTo(map);

                const popupContent = `
                    <div style="font-family: inherit; width: 220px; padding: 2px;">
                        <span style="font-size: 10px; font-weight: 700; color: #0369a1; background: #f0f9ff; padding: 2px 5px; border-radius: 4px;">
                            ${job.category}
                        </span>
                        <h4 style="font-size: 13px; font-weight: 800; color: #0f172a; margin: 4px 0 2px 0; line-height: 1.3;">
                            ${job.title}
                        </h4>
                        <p style="font-size: 11px; font-weight: 600; color: #475569; margin: 0 0 4px 0;">
                            ${job.employer_name}
                        </p>
                        <p style="font-size: 11px; color: #334155; margin: 0 0 6px 0;">
                            📍 ${job.location_address}
                        </p>
                        <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; margin-bottom: 8px;">
                            <span style="color: ${job.is_locked ? '#b45309' : '#0284c7'};">
                                ${job.workers_hired}/${job.workers_needed} Hired
                            </span>
                            <span style="color: #047857;">
                                ${job.salary_range || 'Competitive'}
                            </span>
                        </div>
                        <a href="${job.detail_url}" style="display: block; text-align: center; background: #0284c7; color: #ffffff; padding: 5px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">
                            View Details &rarr;
                        </a>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markers[job.id] = marker;
            }
        });
    });

    function zoomToJob(lat, lng, jobId) {
        if (map && lat && lng) {
            map.flyTo([lat, lng], 13, { duration: 1 });
            if (markers[jobId]) {
                setTimeout(() => markers[jobId].openPopup(), 1000);
            }
        }
    }

    function resetMapView() {
        if (map) {
            map.flyTo(defaultCenter, defaultZoom, { duration: 1 });
        }
    }
</script>
@endpush
@endsection
