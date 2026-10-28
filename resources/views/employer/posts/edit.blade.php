@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 text-zinc-100">

    <!-- Header -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Edit Job Post
            </h1>
            <p class="text-xs font-medium text-zinc-400 mt-1">
                {{ $jobPost->title }}
            </p>
        </div>
        <a href="{{ route('employer.posts.index') }}" class="text-xs font-bold text-zinc-300 hover:text-white flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Posts
        </a>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('employer.posts.update', $jobPost) }}" class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-6">
        @csrf
        @method('PUT')

        <!-- Job Basic Details -->
        <div class="space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider border-b border-zinc-800 pb-2">
                Job Details
            </h2>

            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                    Job Title <span class="text-red-400">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $jobPost->title) }}" required
                    class="w-full px-4 py-3 rounded-2xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-500 text-sm font-semibold focus:outline-none focus:border-zinc-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                        Category <span class="text-red-400">*</span>
                    </label>
                    <select name="category" id="category" class="w-full px-4 py-3 rounded-2xl bg-[#18181b] border border-zinc-700 text-white text-sm font-semibold focus:outline-none focus:border-zinc-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $jobPost->category) == $cat ? 'selected' : '' }} class="bg-zinc-900 text-white">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="job_type" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                        Employment Type <span class="text-red-400">*</span>
                    </label>
                    <select name="job_type" id="job_type" class="w-full px-4 py-3 rounded-2xl bg-[#18181b] border border-zinc-700 text-white text-sm font-semibold focus:outline-none focus:border-zinc-500">
                        @foreach(['Full-time', 'Daily Rate', 'Contract', 'Part-time', 'Project-based'] as $type)
                            <option value="{{ $type }}" {{ old('job_type', $jobPost->job_type) == $type ? 'selected' : '' }} class="bg-zinc-900 text-white">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="salary_range" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                        Salary / Rate
                    </label>
                    <input type="text" name="salary_range" id="salary_range" value="{{ old('salary_range', $jobPost->salary_range) }}"
                        placeholder="e.g. ₱600 - ₱800 / day"
                        class="w-full px-4 py-3 rounded-2xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-500 text-sm font-semibold focus:outline-none focus:border-zinc-500">
                </div>
            </div>

            <!-- Worker Capacity Limit -->
            <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-800">
                <label for="workers_needed" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                    Workers Needed <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" name="workers_needed" id="workers_needed" min="1" max="1000" value="{{ old('workers_needed', $jobPost->workers_needed) }}" required
                        class="w-32 px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-sm font-black focus:outline-none focus:border-zinc-500">
                    <p class="text-xs text-zinc-400 font-medium">
                        Current: <strong class="text-white">{{ $jobPost->workers_hired }}</strong> hired. Increasing quota unlocks the post if previously full.
                    </p>
                </div>
            </div>
        </div>

        <!-- Workplace Location -->
        <div class="space-y-4 pt-4 border-t border-zinc-800">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">
                Workplace Location
            </h2>

            <!-- Quick Pin Locations -->
            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <span class="font-bold text-zinc-400">Quick Pin:</span>
                <button type="button" onclick="setPresetLocation(9.8433, 124.0287, 'Ambuan, Catigbian, Bohol, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-zinc-900 border border-zinc-750 hover:bg-zinc-850 font-semibold text-zinc-200 transition-colors">
                    Catigbian, Bohol
                </button>
                <button type="button" onclick="setPresetLocation(10.3157, 123.8854, 'Cebu City, Cebu, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-zinc-900 border border-zinc-750 hover:bg-zinc-850 font-semibold text-zinc-200 transition-colors">
                    Cebu City
                </button>
                <button type="button" onclick="setPresetLocation(14.5547, 121.0493, 'Bonifacio Global City, Taguig, Metro Manila, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-zinc-900 border border-zinc-750 hover:bg-zinc-850 font-semibold text-zinc-200 transition-colors">
                    Metro Manila
                </button>
                <button type="button" onclick="setPresetLocation(7.1907, 125.4553, 'Davao City, Davao del Sur, Philippines')"
                    class="px-2.5 py-1 rounded-xl bg-zinc-900 border border-zinc-750 hover:bg-zinc-850 font-semibold text-zinc-200 transition-colors">
                    Davao City
                </button>
            </div>

            <!-- Leaflet Interactive Pin Picker Map -->
            <div id="locationPickerMap" class="w-full h-72 rounded-2xl border border-zinc-800 shadow-inner z-0 bg-[#09090b]"></div>

            <!-- Address and Coordinates Row -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-8">
                    <label for="location_address" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                        Address <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="location_address" id="location_address" value="{{ old('location_address', $jobPost->location_address) }}" required
                        placeholder="Barangay, Municipality/City, Province"
                        class="w-full px-4 py-3 rounded-2xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-500 text-sm font-semibold focus:outline-none focus:border-zinc-500">
                </div>
                <div class="md:col-span-2">
                    <label for="latitude" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                        Latitude
                    </label>
                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $jobPost->latitude) }}" required readonly
                        class="w-full px-3 py-3 rounded-2xl bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs font-mono font-bold">
                </div>
                <div class="md:col-span-2">
                    <label for="longitude" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                        Longitude
                    </label>
                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $jobPost->longitude) }}" required readonly
                        class="w-full px-3 py-3 rounded-2xl bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs font-mono font-bold">
                </div>
            </div>
        </div>

        <!-- Description & Requirements -->
        <div class="space-y-4 pt-4 border-t border-zinc-800">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">
                Requirements & Description
            </h2>

            <!-- Mandatory Requirements Checkbox Dropdown -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase tracking-wider text-zinc-300">
                        Requirements <span class="text-red-400">* (Mandatory)</span>
                    </label>
                    <span class="text-[11px] font-medium text-zinc-400">
                        Select all required documents & qualifications
                    </span>
                </div>

                <!-- Custom Dropdown Button -->
                <div class="relative">
                    <button type="button" id="requirementsDropdownBtn" onclick="toggleRequirementsDropdown()"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl bg-[#18181b] border border-zinc-700 text-sm font-semibold text-zinc-200 hover:border-zinc-500 focus:outline-none transition-colors">
                        <span id="requirementsBtnText" class="flex items-center gap-2 text-zinc-300">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Choose requirements from dropdown...</span>
                        </span>
                        <svg id="requirementsChevron" class="w-4 h-4 text-zinc-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Panel with Checkboxes -->
                    <div id="requirementsDropdownMenu"
                        class="hidden absolute left-0 right-0 mt-2 p-3 rounded-2xl bg-[#141418] border border-zinc-700 shadow-2xl space-y-2.5 z-30 max-h-72 overflow-y-auto">
                        
                        <div class="flex items-center justify-between pb-2 border-b border-zinc-800 text-xs font-bold">
                            <span class="text-zinc-400 uppercase text-[10px]">Philippine Standard Requirements</span>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="selectAllRequirements()" class="text-zinc-300 hover:text-white">Select All</button>
                                <span class="text-zinc-600">•</span>
                                <button type="button" onclick="clearAllRequirements()" class="text-red-400 hover:text-red-300">Clear</button>
                            </div>
                        </div>

                        <!-- Checkbox Options -->
                        <div class="space-y-1.5 text-xs font-medium text-zinc-200" id="requirementsCheckboxList">
                            @php
                                $standardReqs = [
                                    "Valid Government Issued ID (PhilSys, Driver's License, UMID, Passport)",
                                    "Barangay Clearance / Certificate of Residency",
                                    "Police Clearance or NBI Clearance",
                                    "Medical Certificate / Fit to Work Clearance",
                                    "Updated Resume / Bio-Data",
                                    "SSS, PhilHealth, and Pag-IBIG Membership Numbers",
                                    "TESDA NC II Certificate or PRC License",
                                    "High School Diploma or Transcript of Records (TOR)",
                                    "Drug Test Result Clearance",
                                    "1x1 or 2x2 ID Photo",
                                    "BIR Form 2316 or Certificate of Employment (COE)",
                                    "Willingness to work on-site / outdoor shifts",
                                ];
                            @endphp

                            @foreach($standardReqs as $idx => $req)
                                <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-zinc-800/80 cursor-pointer select-none transition-colors">
                                    <input type="checkbox" value="{{ $req }}" onchange="syncRequirements()"
                                        class="req-checkbox w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-white focus:ring-0">
                                    <span>{{ $req }}</span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Add Custom Requirement -->
                        <div class="pt-2 border-t border-zinc-800">
                            <p class="text-[10px] font-bold uppercase text-zinc-400 mb-1.5">Add Custom Requirement</p>
                            <div class="flex gap-2">
                                <input type="text" id="customReqInput" placeholder="e.g. 2 years experience with backhoe excavator"
                                    class="flex-1 px-3 py-1.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-xs font-semibold focus:outline-none focus:border-zinc-500"
                                    onkeydown="if(event.key === 'Enter'){ event.preventDefault(); addCustomRequirement(); }">
                                <button type="button" onclick="addCustomRequirement()"
                                    class="px-3 py-1.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-sm">
                                    + Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Badges Display -->
                <div id="selectedRequirementsBadges" class="flex flex-wrap gap-1.5 min-h-[32px] p-2 rounded-2xl bg-zinc-900 border border-zinc-800">
                    <span id="noRequirementsSelectedText" class="text-xs text-zinc-500 font-medium italic p-1">
                        No requirements selected yet. Please select at least one requirement from the dropdown above.
                    </span>
                </div>

                <!-- Hidden Input Bound to Form (Mandatory) -->
                <textarea name="requirements" id="requirements" class="hidden" required>{{ old('requirements', $jobPost->requirements) }}</textarea>
                <p id="requirementsError" class="text-xs font-bold text-red-400 hidden">
                    * Requirements are mandatory. Please choose at least one item from the dropdown.
                </p>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-1">
                    Description <span class="text-red-400">*</span>
                </label>
                <textarea name="description" id="description" rows="4" required
                    class="w-full px-4 py-3 rounded-2xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-500 text-sm font-semibold focus:outline-none focus:border-zinc-500">{{ old('description', $jobPost->description) }}</textarea>
            </div>
        </div>

        <!-- Action Button -->
        <div class="pt-4 border-t border-zinc-800 flex items-center justify-end gap-3">
            <a href="{{ route('employer.posts.index') }}" class="px-5 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold text-xs transition-colors">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all">
                Update Post
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
    let pickerMap;
    let marker;

    document.addEventListener('DOMContentLoaded', function() {
        const initialLat = parseFloat(document.getElementById('latitude').value) || {{ $jobPost->latitude ?? 9.8433 }};
        const initialLng = parseFloat(document.getElementById('longitude').value) || {{ $jobPost->longitude ?? 124.0287 }};

        pickerMap = L.map('locationPickerMap', {
            center: [initialLat, initialLng],
            zoom: 13,
            scrollWheelZoom: false
        });

        // CartoDB Dark Matter tiles
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO | Findjobph',
            maxZoom: 19
        }).addTo(pickerMap);

        const customPin = L.divIcon({
            className: 'custom-pin-draggable',
            html: '<div style="background-color: #10b981; width: 32px; height: 32px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.5); display: flex; items-center; justify-content: center; color: white;"><svg style=\"width:18px;height:18px;margin-top:4px;margin-left:4px;\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\" clip-rule=\"evenodd\"></path></svg></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
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

    // Requirements Dropdown Logic
    let selectedRequirements = [];

    function toggleRequirementsDropdown() {
        const menu = document.getElementById('requirementsDropdownMenu');
        const chevron = document.getElementById('requirementsChevron');
        menu.classList.toggle('hidden');
        if (menu.classList.contains('hidden')) {
            chevron.classList.remove('rotate-180');
        } else {
            chevron.classList.add('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const btn = document.getElementById('requirementsDropdownBtn');
        const menu = document.getElementById('requirementsDropdownMenu');
        if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
            document.getElementById('requirementsChevron')?.classList.remove('rotate-180');
        }
    });

    function syncRequirements() {
        selectedRequirements = [];
        document.querySelectorAll('.req-checkbox:checked').forEach(cb => {
            selectedRequirements.push(cb.value);
        });
        renderRequirementsUI();
    }

    function selectAllRequirements() {
        document.querySelectorAll('.req-checkbox').forEach(cb => {
            cb.checked = true;
        });
        syncRequirements();
    }

    function clearAllRequirements() {
        document.querySelectorAll('.req-checkbox').forEach(cb => {
            cb.checked = false;
        });
        syncRequirements();
    }

    function addCustomRequirement() {
        const input = document.getElementById('customReqInput');
        const val = input.value.trim();
        if (!val) return;

        const container = document.getElementById('requirementsCheckboxList');
        const label = document.createElement('label');
        label.className = 'flex items-center gap-2.5 p-2 rounded-xl hover:bg-zinc-800/80 cursor-pointer select-none transition-colors';
        label.innerHTML = `
            <input type="checkbox" value="${val.replace(/"/g, '&quot;')}" checked onchange="syncRequirements()"
                class="req-checkbox w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-white focus:ring-0">
            <span>${val} (Custom)</span>
        `;
        container.appendChild(label);
        input.value = '';
        syncRequirements();
    }

    function removeRequirement(val) {
        document.querySelectorAll('.req-checkbox').forEach(cb => {
            if (cb.value === val) {
                cb.checked = false;
            }
        });
        syncRequirements();
    }

    function renderRequirementsUI() {
        const container = document.getElementById('selectedRequirementsBadges');
        const btnText = document.getElementById('requirementsBtnText');
        const hiddenTextarea = document.getElementById('requirements');
        const errorMsg = document.getElementById('requirementsError');

        if (selectedRequirements.length === 0) {
            container.innerHTML = `<span id="noRequirementsSelectedText" class="text-xs text-zinc-500 font-medium italic p-1">
                No requirements selected yet. Please select at least one requirement from the dropdown above.
            </span>`;
            btnText.innerHTML = `
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Choose requirements from dropdown...</span>
            `;
            hiddenTextarea.value = '';
        } else {
            container.innerHTML = '';
            selectedRequirements.forEach((req, idx) => {
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-800 text-zinc-200 border border-zinc-700 text-xs font-bold shadow-xs';
                badge.innerHTML = `
                    <span>${idx + 1}. ${req}</span>
                    <button type="button" onclick="removeRequirement('${req.replace(/'/g, "\\'")}')" class="text-zinc-400 hover:text-red-400 font-extrabold ml-1">×</button>
                `;
                container.appendChild(badge);
            });

            btnText.innerHTML = `
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-bold text-white">${selectedRequirements.length} requirement(s) selected</span>
            `;

            hiddenTextarea.value = selectedRequirements.map((r, i) => `${i + 1}. ${r}`).join("\n");
            errorMsg.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const existingVal = document.getElementById('requirements')?.value.trim();
        if (existingVal) {
            document.querySelectorAll('.req-checkbox').forEach(cb => {
                if (existingVal.includes(cb.value)) {
                    cb.checked = true;
                }
            });
            syncRequirements();
        }

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (selectedRequirements.length === 0) {
                    e.preventDefault();
                    document.getElementById('requirementsError').classList.remove('hidden');
                    document.getElementById('requirementsDropdownBtn').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    });
</script>
@endpush
@endsection
