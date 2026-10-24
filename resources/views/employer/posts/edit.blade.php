@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                Edit Job Post
            </h1>
            <p class="text-xs font-semibold text-slate-600 mt-1">
                {{ $jobPost->title }}
            </p>
        </div>
        <a href="{{ route('employer.posts.index') }}" class="text-xs font-bold text-sky-800 hover:text-sky-950 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Posts
        </a>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('employer.posts.update', $jobPost) }}" class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl space-y-6">
        @csrf
        @method('PUT')

        <!-- Job Basic Details -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider border-b border-slate-100 pb-2">
                Job Details
            </h2>

            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Job Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $jobPost->title) }}" required
                    class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $jobPost->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="job_type" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Employment Type <span class="text-red-500">*</span>
                    </label>
                    <select name="job_type" id="job_type" class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
                        <option value="Full-time" {{ old('job_type', $jobPost->job_type) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Daily Rate" {{ old('job_type', $jobPost->job_type) == 'Daily Rate' ? 'selected' : '' }}>Daily Rate</option>
                        <option value="Contract" {{ old('job_type', $jobPost->job_type) == 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Part-time" {{ old('job_type', $jobPost->job_type) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="Project-based" {{ old('job_type', $jobPost->job_type) == 'Project-based' ? 'selected' : '' }}>Project-based</option>
                    </select>
                </div>

                <div>
                    <label for="salary_range" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Salary / Rate
                    </label>
                    <input type="text" name="salary_range" id="salary_range" value="{{ old('salary_range', $jobPost->salary_range) }}"
                        class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
                </div>
            </div>

            <!-- Worker Capacity Limit -->
            <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200">
                <label for="workers_needed" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Workers Needed Target <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" name="workers_needed" id="workers_needed" min="{{ $jobPost->workers_hired }}" max="1000" value="{{ old('workers_needed', $jobPost->workers_needed) }}" required
                        class="w-32 px-4 py-2.5 rounded-xl glass-input text-sm font-black text-sky-950">
                    <p class="text-xs text-slate-600">
                        Currently <strong>{{ $jobPost->workers_hired }}</strong> hired. Cannot set lower than current hires.
                    </p>
                </div>
            </div>
        </div>

        <!-- Workplace Location -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h2 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider">
                Workplace Location
            </h2>

            <div id="editLocationMap" class="w-full h-72 rounded-2xl border border-sky-200 shadow-inner z-0"></div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-8">
                    <label for="location_address" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="location_address" id="location_address" value="{{ old('location_address', $jobPost->location_address) }}" required
                        class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">
                </div>
                <div class="md:col-span-2">
                    <label for="latitude" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Latitude
                    </label>
                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $jobPost->latitude) }}" required readonly
                        class="w-full px-3 py-3 rounded-2xl bg-slate-100 text-slate-800 text-xs font-mono font-bold">
                </div>
                <div class="md:col-span-2">
                    <label for="longitude" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Longitude
                    </label>
                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $jobPost->longitude) }}" required readonly
                        class="w-full px-3 py-3 rounded-2xl bg-slate-100 text-slate-800 text-xs font-mono font-bold">
                </div>
            </div>
        </div>

        <!-- Description & Requirements -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h2 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider">
                Requirements & Description
            </h2>

            <div>
                <label for="requirements" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Requirements <span class="text-red-500">*</span>
                </label>
                <textarea name="requirements" id="requirements" rows="4" required
                    class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">{{ old('requirements', $jobPost->requirements) }}</textarea>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="4" required
                    class="w-full px-4 py-3 rounded-2xl glass-input text-sm font-semibold">{{ old('description', $jobPost->description) }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('employer.posts.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-xs shadow-md shadow-sky-500/20 transition-all">
                Save Changes
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
    let editMap;
    let editMarker;

    document.addEventListener('DOMContentLoaded', function() {
        const initialLat = parseFloat(document.getElementById('latitude').value) || 9.8433;
        const initialLng = parseFloat(document.getElementById('longitude').value) || 124.0287;

        editMap = L.map('editLocationMap', {
            center: [initialLat, initialLng],
            zoom: 13,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap | Findjobph',
            maxZoom: 19
        }).addTo(editMap);

        const pinIcon = L.divIcon({
            className: 'custom-pin-draggable',
            html: '<div style="background-color: #0284c7; width: 32px; height: 32px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:18px;height:18px;margin-top:4px;margin-left:4px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        editMarker = L.marker([initialLat, initialLng], {
            draggable: true,
            icon: pinIcon
        }).addTo(editMap);

        editMarker.on('dragend', function() {
            const pos = editMarker.getLatLng();
            document.getElementById('latitude').value = pos.lat.toFixed(6);
            document.getElementById('longitude').value = pos.lng.toFixed(6);
        });

        editMap.on('click', function(e) {
            editMarker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
        });
    });
</script>
@endpush
@endsection
