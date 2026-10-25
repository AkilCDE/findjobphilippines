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

            <!-- Mandatory Requirements Checkbox Dropdown -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-900">
                        Requirements <span class="text-red-500">* (Mandatory)</span>
                    </label>
                    <span class="text-[11px] font-semibold text-slate-500">
                        Select all required documents & qualifications
                    </span>
                </div>

                <!-- Custom Dropdown Button -->
                <div class="relative">
                    <button type="button" id="requirementsDropdownBtn" onclick="toggleRequirementsDropdown()"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl glass-input text-sm font-semibold text-slate-800 hover:border-sky-400 focus:outline-none transition-colors">
                        <span id="requirementsBtnText" class="flex items-center gap-2 text-slate-700">
                            <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Choose requirements from dropdown...</span>
                        </span>
                        <svg id="requirementsChevron" class="w-4 h-4 text-slate-500 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Panel with Checkboxes -->
                    <div id="requirementsDropdownMenu"
                        class="hidden absolute left-0 right-0 mt-2 p-3 rounded-2xl bg-white border border-slate-200 shadow-2xl space-y-2.5 z-30 max-h-72 overflow-y-auto">
                        
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs font-bold">
                            <span class="text-slate-600 uppercase text-[10px]">Philippine Standard Requirements</span>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="selectAllRequirements()" class="text-sky-700 hover:text-sky-900">Select All</button>
                                <span class="text-slate-300">•</span>
                                <button type="button" onclick="clearAllRequirements()" class="text-red-600 hover:text-red-800">Clear</button>
                            </div>
                        </div>

                        <!-- Checkbox Options -->
                        <div class="space-y-1.5 text-xs font-semibold text-slate-800" id="requirementsCheckboxList">
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
                                <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-sky-50/70 cursor-pointer select-none transition-colors">
                                    <input type="checkbox" value="{{ $req }}" onchange="syncRequirements()"
                                        class="req-checkbox w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    <span>{{ $req }}</span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Add Custom Requirement -->
                        <div class="pt-2 border-t border-slate-100">
                            <p class="text-[10px] font-bold uppercase text-slate-500 mb-1.5">Add Custom Requirement</p>
                            <div class="flex gap-2">
                                <input type="text" id="customReqInput" placeholder="e.g. 2 years experience with backhoe excavator"
                                    class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-sky-500"
                                    onkeydown="if(event.key === 'Enter'){ event.preventDefault(); addCustomRequirement(); }">
                                <button type="button" onclick="addCustomRequirement()"
                                    class="px-3 py-1.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs shadow-sm">
                                    + Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Badges Display -->
                <div id="selectedRequirementsBadges" class="flex flex-wrap gap-1.5 min-h-[32px] p-2 rounded-2xl bg-slate-50/80 border border-slate-200/70">
                    <span id="noRequirementsSelectedText" class="text-xs text-slate-500 font-medium italic p-1">
                        No requirements selected yet. Please select at least one requirement from the dropdown above.
                    </span>
                </div>

                <!-- Hidden Input Bound to Form (Mandatory) -->
                <textarea name="requirements" id="requirements" class="hidden" required>{{ old('requirements', $jobPost->requirements) }}</textarea>
                <p id="requirementsError" class="text-xs font-bold text-red-600 hidden">
                    * Requirements are mandatory. Please choose at least one item from the dropdown.
                </p>
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

    // Requirements Dropdown Logic
    let selectedRequirements = [];

    function toggleRequirementsDropdown() {
        const menu = document.getElementById('requirementsDropdownMenu');
        const chevron = document.getElementById('requirementsChevron');
        menu.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
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
        const checkboxes = document.querySelectorAll('.req-checkbox:checked');
        selectedRequirements = Array.from(checkboxes).map(cb => cb.value.trim());
        renderRequirementsUI();
    }

    function selectAllRequirements() {
        document.querySelectorAll('.req-checkbox').forEach(cb => cb.checked = true);
        syncRequirements();
    }

    function clearAllRequirements() {
        document.querySelectorAll('.req-checkbox').forEach(cb => cb.checked = false);
        syncRequirements();
    }

    function addCustomRequirement() {
        const input = document.getElementById('customReqInput');
        const val = input.value.trim();
        if (!val) return;

        const list = document.getElementById('requirementsCheckboxList');
        const label = document.createElement('label');
        label.className = 'flex items-center gap-2.5 p-2 rounded-xl hover:bg-sky-50/70 cursor-pointer select-none transition-colors';
        label.innerHTML = `
            <input type="checkbox" value="${val}" checked onchange="syncRequirements()"
                class="req-checkbox w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
            <span>${val} <em class="text-sky-700">(Custom)</em></span>
        `;
        list.appendChild(label);
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
            container.innerHTML = `<span id="noRequirementsSelectedText" class="text-xs text-slate-500 font-medium italic p-1">
                No requirements selected yet. Please select at least one requirement from the dropdown above.
            </span>`;
            btnText.innerHTML = `
                <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Choose requirements from dropdown...</span>
            `;
            hiddenTextarea.value = '';
        } else {
            container.innerHTML = '';
            selectedRequirements.forEach((req, idx) => {
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-100 text-sky-900 border border-sky-200 text-xs font-bold shadow-xs';
                badge.innerHTML = `
                    <span>${idx + 1}. ${req}</span>
                    <button type="button" onclick="removeRequirement('${req.replace(/'/g, "\\'")}')" class="text-sky-700 hover:text-red-600 font-extrabold ml-1">×</button>
                `;
                container.appendChild(badge);
            });

            btnText.innerHTML = `
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-bold text-slate-900">${selectedRequirements.length} requirement(s) selected</span>
            `;

            hiddenTextarea.value = selectedRequirements.map((r, i) => `${i + 1}. ${r}`).join("\n");
            errorMsg.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const existingVal = document.getElementById('requirements')?.value.trim();
        if (existingVal) {
            document.querySelectorAll('.req-checkbox').forEach(cb => {
                if (existingVal.toLowerCase().includes(cb.value.toLowerCase().substring(0, 15))) {
                    cb.checked = true;
                }
            });
            // If existingVal has custom lines not matched, add them as custom items
            const lines = existingVal.split('\n').map(l => l.replace(/^\d+\.\s*/, '').trim()).filter(Boolean);
            lines.forEach(line => {
                let matched = false;
                document.querySelectorAll('.req-checkbox').forEach(cb => {
                    if (cb.value.toLowerCase().includes(line.toLowerCase().substring(0, 15))) {
                        cb.checked = true;
                        matched = true;
                    }
                });
                if (!matched && line.length > 3) {
                    const list = document.getElementById('requirementsCheckboxList');
                    const label = document.createElement('label');
                    label.className = 'flex items-center gap-2.5 p-2 rounded-xl hover:bg-sky-50/70 cursor-pointer select-none transition-colors';
                    label.innerHTML = `
                        <input type="checkbox" value="${line}" checked onchange="syncRequirements()"
                            class="req-checkbox w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span>${line} <em class="text-sky-700">(Custom)</em></span>
                    `;
                    list.appendChild(label);
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
