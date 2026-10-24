@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl">
        <div class="flex items-center gap-2 mb-1.5">
            <a href="{{ route('employer.posts.index') }}" class="text-xs font-bold text-sky-800 hover:text-sky-950 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to My Posts
            </a>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
            Post a Job Opportunity
        </h1>
        <p class="text-sm font-semibold text-slate-700 mt-1">
            Specify the title, required qualifications, worker capacity, and drop an interactive location pin on the Philippine map.
        </p>
    </div>

    <!-- Create Form -->
    <form method="POST" action="{{ route('employer.posts.store') }}" class="glass-panel p-6 sm:p-10 rounded-3xl border border-white/80 shadow-2xl space-y-6">
        @csrf

        <!-- Job Basic Details -->
        <div class="space-y-4">
            <h2 class="text-base font-black text-slate-950 border-b border-sky-100 pb-2">
                1. Job Information
            </h2>

            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Job Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    placeholder="e.g. Heavy Equipment Operator & Skilled Mason"
                    class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="job_type" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Employment Type <span class="text-red-500">*</span>
                    </label>
                    <select name="job_type" id="job_type" class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
                        <option value="Full-time" {{ old('job_type') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Daily Rate" {{ old('job_type') == 'Daily Rate' ? 'selected' : '' }}>Daily Rate</option>
                        <option value="Contract" {{ old('job_type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Part-time" {{ old('job_type') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="Project-based" {{ old('job_type') == 'Project-based' ? 'selected' : '' }}>Project-based</option>
                    </select>
                </div>

                <div>
                    <label for="salary_range" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Salary or Daily Rate
                    </label>
                    <input type="text" name="salary_range" id="salary_range" value="{{ old('salary_range') }}"
                        placeholder="e.g. ₱650 - ₱900 / day"
                        class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
                </div>
            </div>

            <!-- Worker Capacity Limit -->
            <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200">
                <label for="workers_needed" class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1">
                    How Many Workers Needed? (Capacity Target) <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" name="workers_needed" id="workers_needed" min="1" max="1000" value="{{ old('workers_needed', 10) }}" required
                        class="w-36 px-4 py-2.5 rounded-xl glass-input text-sm font-black text-sky-950">
                    <p class="text-xs font-semibold text-slate-700">
                        ⚡ <strong>Automatic Lock System:</strong> As you hire workers (e.g., 2/10 hired), when 10/10 workers are hired, this job post will <strong>automatically lock</strong> and stop accepting new applicants.
                    </p>
                </div>
            </div>
        </div>

        <!-- Philippine Map Pin Selection (Required) -->
        <div class="space-y-4 pt-4 border-t border-sky-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-950">
                        2. Workplace Location on Philippine Map
                    </h2>
                    <p class="text-xs font-semibold text-slate-700">
                        Click anywhere on the map or drag the pin to set the exact site where workers will report.
                    </p>
                </div>
                <span class="text-[11px] font-black text-sky-800 bg-sky-100 px-2.5 py-1 rounded-full">
                    GPS Coordinates API
                </span>
            </div>

            <!-- Quick Province Jump Presets -->
            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <span class="font-bold text-slate-800">Quick Pin Locations:</span>
                <button type="button" onclick="setPresetLocation(9.8433, 124.0287, 'Ambuan, Catigbian, Bohol, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-white border border-sky-200 hover:bg-sky-50 font-bold text-sky-800 transition-colors">
                    📍 Catigbian, Bohol
                </button>
                <button type="button" onclick="setPresetLocation(10.3157, 123.8854, 'Cebu City, Cebu, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-white border border-sky-200 hover:bg-sky-50 font-bold text-sky-800 transition-colors">
                    📍 Cebu City
                </button>
                <button type="button" onclick="setPresetLocation(14.5547, 121.0493, 'Bonifacio Global City, Taguig, Metro Manila, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-white border border-sky-200 hover:bg-sky-50 font-bold text-sky-800 transition-colors">
                    📍 BGC Taguig, Manila
                </button>
                <button type="button" onclick="setPresetLocation(7.1907, 125.4553, 'Davao City, Davao del Sur, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-white border border-sky-200 hover:bg-sky-50 font-bold text-sky-800 transition-colors">
                    📍 Davao City
                </button>
                <button type="button" onclick="setPresetLocation(16.4023, 120.5960, 'Baguio City, Benguet, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-white border border-sky-200 hover:bg-sky-50 font-bold text-sky-800 transition-colors">
                    📍 Baguio City
                </button>
            </div>

            <!-- Leaflet Interactive Pin Picker Map -->
            <div id="locationPickerMap" class="w-full h-80 rounded-2xl border border-sky-200 shadow-inner z-0"></div>

            <!-- Address and Coordinates Row -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-8">
                    <label for="location_address" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Full Location Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="location_address" id="location_address" value="{{ old('location_address', 'Ambuan, Catigbian, Bohol, Philippines') }}" required
                        placeholder="e.g. Ambuan, Catigbian, Bohol, Philippines"
                        class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
                </div>
                <div class="md:col-span-2">
                    <label for="latitude" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Latitude
                    </label>
                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', '9.8433') }}" required readonly
                        class="w-full px-3 py-3 rounded-2xl bg-slate-100 text-slate-800 text-xs font-mono font-bold">
                </div>
                <div class="md:col-span-2">
                    <label for="longitude" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Longitude
                    </label>
                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', '124.0287') }}" required readonly
                        class="w-full px-3 py-3 rounded-2xl bg-slate-100 text-slate-800 text-xs font-mono font-bold">
                </div>
            </div>
        </div>

        <!-- Description & Requirements -->
        <div class="space-y-4 pt-4 border-t border-sky-100">
            <h2 class="text-base font-black text-slate-950">
                3. Qualifications & Job Details
            </h2>

            <div>
                <label for="requirements" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Requirements to be Submitted by Worker <span class="text-red-500">*</span>
                </label>
                <textarea name="requirements" id="requirements" rows="4" required
                    placeholder="List the required documents (e.g. 1. Valid ID or Barangay Clearance&#10;2. TESDA NC II Heavy Equipment Certificate&#10;3. Fit to work medical certificate)..."
                    class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">{{ old('requirements') }}</textarea>
                <p class="text-[11px] font-semibold text-slate-600 mt-1">
                    Workers will be required to submit or check off these items when applying.
                </p>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Job Description & Daily Duties <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="4" required
                    placeholder="Explain the work environment, schedule, allowances, and duties..."
                    class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Action Button -->
        <div class="pt-4 border-t border-sky-100 flex items-center justify-end gap-3">
            <a href="{{ route('employer.posts.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm">
                Cancel
            </a>
            <button type="submit"
                class="px-8 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-lg shadow-sky-500/25 transition-all hover:scale-[1.02]">
                Publish Job Post & Pinned Location
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
    let pickerMap;
    let marker;

    document.addEventListener('DOMContentLoaded', function() {
        const initialLat = parseFloat(document.getElementById('latitude').value) || 9.8433;
        const initialLng = parseFloat(document.getElementById('longitude').value) || 124.0287;

        pickerMap = L.map('locationPickerMap', {
            center: [initialLat, initialLng],
            zoom: 12,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap | Findjobph',
            maxZoom: 19
        }).addTo(pickerMap);

        const customPin = L.divIcon({
            className: 'custom-pin-draggable',
            html: '<div style="background-color: #0284c7; width: 34px; height: 34px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.35); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:20px;height:20px;margin-top:4px;margin-left:4px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [34, 34],
            iconAnchor: [17, 34]
        });

        marker = L.marker([initialLat, initialLng], {
            draggable: true,
            icon: customPin
        }).addTo(pickerMap);

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
            reverseGeocode(position.lat, position.lng);
        });

        pickerMap.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });
    });

    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = parseFloat(lat).toFixed(6);
        document.getElementById('longitude').value = parseFloat(lng).toFixed(6);
    }

    function setPresetLocation(lat, lng, address) {
        if (pickerMap && marker) {
            pickerMap.flyTo([lat, lng], 13);
            marker.setLatLng([lat, lng]);
            updateCoordinates(lat, lng);
            document.getElementById('location_address').value = address;
        }
    }

    // Reverse geocode via Nominatim API to suggest clean Philippine address
    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=14&addressdetails=1`, {
            headers: { 'Accept-Language': 'en' }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById('location_address').value = data.display_name;
            }
        })
        .catch(err => {
            console.log('Reverse geocoding unavailable, manual address maintained.', err);
        });
    }
</script>
@endpush
@endsection
