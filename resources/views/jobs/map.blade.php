@extends('layouts.app')

@section('content')
<div class="space-y-6 text-zinc-100">

    <!-- Header Banner -->
    <div class="p-5 sm:p-6 rounded-2xl bg-[#121215] border border-zinc-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                Philippine Job Map
            </h1>
            <p class="text-xs font-medium text-zinc-400 mt-0.5">
                Workplace locations pinned by employers across the Philippines.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('jobs.index') }}" class="px-3.5 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs transition-colors">
                View Job List
            </a>
            @auth
                @if(auth()->user()->isEmployer())
                    <a href="{{ route('employer.posts.create') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-sm transition-colors">
                        + Post Job with Pin
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <!-- Map & Job Sidebar Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Interactive Map Container (8 cols) -->
        <div class="lg:col-span-8 bg-[#121215] p-2 rounded-2xl border border-zinc-800 shadow-sm relative overflow-hidden flex flex-col">
            
            <!-- Quick Map Controls Filter Bar -->
            <div class="p-3 bg-zinc-900 border-b border-zinc-800 rounded-t-xl flex flex-wrap items-center justify-between gap-3 text-xs z-10">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 font-bold text-emerald-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Open Position
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-bold text-amber-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Locked (Full)
                    </span>
                </div>
                <button type="button" onclick="resetMapView()" class="text-zinc-300 hover:text-white font-bold transition-colors">
                    Reset Map Center
                </button>
            </div>

            <!-- Leaflet Map Element -->
            <div id="philippineJobsMap" class="w-full h-[540px] rounded-b-xl z-0 bg-[#09090b]"></div>

        </div>

        <!-- Pinned Jobs Directory Sidebar (4 cols) -->
        <div class="lg:col-span-4 bg-[#121215] p-4 rounded-2xl border border-zinc-800 shadow-sm flex flex-col h-[600px]">
            <div class="mb-3 pb-2 border-b border-zinc-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white">Pinned Jobs</h3>
                <span class="text-xs font-bold text-zinc-300 bg-zinc-800 px-2 py-0.5 rounded-full border border-zinc-700">
                    {{ count($jobs) }}
                </span>
            </div>

            <!-- Scrollable list of jobs -->
            <div class="flex-1 overflow-y-auto space-y-2.5 pr-1" id="jobSidebarList">
                @foreach($jobs as $job)
                    <div class="p-3 rounded-xl bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all text-xs"
                        onclick="zoomToJob({{ $job['latitude'] }}, {{ $job['longitude'] }}, {{ $job['id'] }})">
                        
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-bold text-[10px] text-zinc-300 bg-zinc-800 border border-zinc-700 px-1.5 py-0.5 rounded">
                                {{ $job['category'] }}
                            </span>
                            <span class="font-bold text-[10px] {{ $job['is_locked'] ? 'text-amber-300 bg-amber-950/80 border border-amber-800/80' : 'text-emerald-300 bg-emerald-950/80 border border-emerald-800/80' }} px-1.5 py-0.5 rounded">
                                {{ $job['is_locked'] ? 'Locked' : 'Open' }}
                            </span>
                        </div>

                        <h4 class="font-bold text-xs text-white hover:text-zinc-300 line-clamp-1">
                            {{ $job['title'] }}
                        </h4>

                        <p class="text-[11px] font-medium text-zinc-400 mt-0.5">
                            {{ $job['employer_name'] }}
                        </p>

                        <p class="text-[11px] text-zinc-300 truncate mt-1">
                            📍 {{ $job['location_address'] }}
                        </p>

                        <div class="mt-2 pt-1.5 border-t border-zinc-800 flex items-center justify-between font-bold text-[11px]">
                            <span class="text-zinc-300">
                                {{ $job['workers_hired'] }}/{{ $job['workers_needed'] }} Hired
                            </span>
                            <span class="text-emerald-400">
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

        // CartoDB Dark Matter tiles for pure dark mode
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO | Findjobph',
            maxZoom: 19
        }).addTo(map);

        const openIcon = L.divIcon({
            className: 'custom-pin-open',
            html: '<div style="background-color: #10b981; width: 26px; height: 26px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.5); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:13px;height:13px;margin-top:3.5px;margin-left:3.5px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [26, 26],
            iconAnchor: [13, 26],
            popupAnchor: [0, -26]
        });

        const lockedIcon = L.divIcon({
            className: 'custom-pin-locked',
            html: '<div style="background-color: #f59e0b; width: 26px; height: 26px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.5); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:13px;height:13px;margin-top:3.5px;margin-left:3.5px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [26, 26],
            iconAnchor: [13, 26],
            popupAnchor: [0, -26]
        });

        jobsData.forEach(job => {
            if (job.latitude && job.longitude) {
                const markerIcon = job.is_locked ? lockedIcon : openIcon;
                const marker = L.marker([job.latitude, job.longitude], { icon: markerIcon }).addTo(map);

                const popupContent = `
                    <div style="font-family: inherit; width: 220px; padding: 2px; color: #ffffff;">
                        <span style="font-size: 10px; font-weight: 700; color: #e4e4e7; background: #27272a; padding: 2px 6px; border-radius: 4px; border: 1px solid #3f3f46;">
                            ${job.category}
                        </span>
                        <h4 style="font-size: 13px; font-weight: 800; color: #ffffff; margin: 6px 0 2px 0; line-height: 1.3;">
                            ${job.title}
                        </h4>
                        <p style="font-size: 11px; font-weight: 600; color: #a1a1aa; margin: 0 0 4px 0;">
                            ${job.employer_name}
                        </p>
                        <p style="font-size: 11px; color: #d4d4d8; margin: 0 0 6px 0;">
                            📍 ${job.location_address}
                        </p>
                        <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; margin-bottom: 8px;">
                            <span style="color: ${job.is_locked ? '#fbbf24' : '#ffffff'};">
                                ${job.workers_hired}/${job.workers_needed} Hired
                            </span>
                            <span style="color: #34d399;">
                                ${job.salary_range || 'Competitive'}
                            </span>
                        </div>
                        <a href="${job.detail_url}" style="display: block; text-align: center; background: #ffffff; color: #000000; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; text-decoration: none;">
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
