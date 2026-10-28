@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 text-zinc-100 space-y-6">

    <!-- Header Section -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-white text-black font-black text-sm">
                    📷
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Worker QR Scanner
                </h1>
            </div>
            <p class="text-xs sm:text-sm font-medium text-zinc-400 mt-1">
                Scan worker digital QR pass to view real-time identity, status, and employment history.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('employer.dashboard') }}"
                class="px-4 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Dashboard
            </a>
        </div>
    </div>

    <!-- Scanner Interface Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Live Camera Viewfinder (2 cols on desktop) -->
        <div class="lg:col-span-2 p-6 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <h2 class="text-base font-bold text-white">Live Camera Scanner</h2>
                </div>
                <div id="cameraStatusText" class="text-xs font-semibold text-zinc-400">
                    Ready
                </div>
            </div>

            <!-- Scanner Viewport Container -->
            <div class="relative rounded-2xl overflow-hidden bg-black border-2 border-zinc-800 min-h-[300px] flex items-center justify-center">
                <div id="employerQrReader" class="w-full"></div>
                
                <!-- Placeholder when camera not active -->
                <div id="cameraPlaceholder" class="text-center p-8 space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-zinc-900 border border-zinc-800 text-zinc-400 flex items-center justify-center text-3xl">
                        📹
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">Camera is currently idle</p>
                        <p class="text-xs text-zinc-400 mt-1 max-w-xs mx-auto">
                            Click Start Camera below to scan a worker's digital pass using your device webcam or phone camera.
                        </p>
                    </div>
                    <button type="button" onclick="startScannerCamera()"
                        class="px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all inline-flex items-center gap-2">
                        <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                        Start Camera
                    </button>
                </div>
            </div>

            <!-- Camera Controls -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <select id="cameraSelect" class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-200 text-xs font-semibold focus:outline-none focus:border-white">
                        <option value="">Detecting cameras...</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" id="startCamBtn" onclick="startScannerCamera()"
                        class="px-4 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all">
                        Start
                    </button>
                    <button type="button" id="stopCamBtn" onclick="stopScannerCamera()" disabled
                        class="px-4 py-2 rounded-xl border border-zinc-700 bg-zinc-900 text-zinc-500 font-bold text-xs transition-colors cursor-not-allowed">
                        Stop
                    </button>
                </div>
            </div>
        </div>

        <!-- Secondary Scan Methods: File Upload & Manual Input -->
        <div class="space-y-6">
            
            <!-- File Upload Scan Card -->
            <div class="p-6 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📁</span>
                    <h3 class="text-sm font-bold text-white">Scan Image / Screenshot</h3>
                </div>
                <p class="text-xs font-medium text-zinc-400">
                    No camera? Upload a saved photo or screenshot of the worker's QR pass.
                </p>

                <label class="block p-5 rounded-2xl border-2 border-dashed border-zinc-700 hover:border-zinc-500 bg-zinc-900/50 cursor-pointer text-center transition-colors">
                    <svg class="w-8 h-8 mx-auto text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="block text-xs font-bold text-zinc-200 mt-2">Choose QR Image File</span>
                    <span class="block text-[10px] text-zinc-500 mt-0.5">PNG, JPG, WEBP</span>
                    <input type="file" id="qrFileInput" accept="image/*" class="hidden" onchange="handleQrFileUpload(event)">
                </label>

                <div id="fileScanFeedback" class="text-xs font-semibold text-zinc-400 text-center hidden"></div>
            </div>

            <!-- Manual Token / Code Card -->
            <div class="p-6 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⌨️</span>
                    <h3 class="text-sm font-bold text-white">Manual Token / Code</h3>
                </div>
                <p class="text-xs font-medium text-zinc-400">
                    Paste raw QR code text or verification token directly.
                </p>

                <div class="space-y-2">
                    <input type="text" id="manualQrInput" placeholder="Paste QR data or token..."
                        class="w-full px-3 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-200 text-xs font-mono focus:outline-none focus:border-white">
                    <button type="button" onclick="handleManualQrSubmit()"
                        class="w-full px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white font-extrabold text-xs transition-colors">
                        Verify Worker Code
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- ==================== NICE POP-UP UI FOR SCANNED WORKER ==================== -->
<div id="workerScanPopUpModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md hidden transition-all">
    <div class="w-full max-w-xl rounded-3xl bg-[#121215] border-2 border-zinc-800 p-6 sm:p-8 shadow-2xl relative overflow-hidden animate-scale-up space-y-6">
        
        <!-- Ambient decorative glow -->
        <div id="modalGlowBackdrop" class="absolute -top-16 -right-16 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Top Header with Dismiss Button -->
        <div class="flex items-center justify-between border-b border-zinc-800 pb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white text-black font-black flex items-center justify-center text-sm shadow-md">
                    ✓
                </div>
                <div>
                    <h3 class="text-base font-black text-white tracking-wide">
                        Worker Pass Verified
                    </h3>
                    <p class="text-[11px] text-zinc-400 font-medium">Real-time status synced with database</p>
                </div>
            </div>

            <button type="button" onclick="closeWorkerPopUpModal()"
                class="p-2 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-900 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Worker Identity Details -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <div id="modalWorkerAvatar" class="w-20 h-20 rounded-2xl bg-zinc-800 border-2 border-zinc-700 text-white font-black text-2xl flex items-center justify-center shadow-lg shrink-0">
                W
            </div>

            <div class="flex-1 text-center sm:text-left space-y-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                    <h4 id="modalWorkerName" class="text-2xl font-black text-white tracking-tight">
                        Worker Name
                    </h4>
                    <span id="modalWorkerId" class="text-xs px-2.5 py-0.5 rounded-lg bg-zinc-850 text-zinc-300 border border-zinc-750 font-mono font-bold">
                        #00000
                    </span>
                </div>

                <p class="text-xs font-semibold text-zinc-400 flex flex-wrap items-center justify-center sm:justify-start gap-x-3 gap-y-1">
                    <span id="modalWorkerSex">👤 Sex</span>
                    <span>•</span>
                    <span id="modalWorkerAddress">📍 Address</span>
                </p>

                <p class="text-xs font-semibold text-zinc-400 flex flex-wrap items-center justify-center sm:justify-start gap-x-3 gap-y-1 pt-1">
                    <span id="modalWorkerContact">📞 Contact</span>
                    <span>•</span>
                    <span id="modalWorkerEmail">✉️ Email</span>
                </p>
            </div>
        </div>

        <!-- Live Status Card Container -->
        <div id="modalStatusContainer" class="p-5 rounded-2xl border transition-all">
            <!-- Injected dynamically via JS based on Hired, Fired, or Pending rules -->
        </div>

        <!-- Pop-up Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-zinc-800">
            <div class="flex items-center gap-1.5 text-xs text-zinc-400">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Live Authenticated</span>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a id="modalMessageWorkerBtn" href="#"
                    class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    Message Worker
                </a>
                <button type="button" onclick="closeWorkerPopUpModal(true)"
                    class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs transition-colors text-center">
                    Scan Another
                </button>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
    let html5QrScanner = null;
    let isCameraRunning = false;
    const scanEndpoint = "{{ route('employer.scan-worker-qr') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    document.addEventListener('DOMContentLoaded', function () {
        initCameraDevices();
    });

    async function initCameraDevices() {
        if (typeof Html5Qrcode === 'undefined') return;
        try {
            const devices = await Html5Qrcode.getCameras();
            const selectEl = document.getElementById('cameraSelect');
            selectEl.innerHTML = '';
            if (devices && devices.length > 0) {
                devices.forEach((cam, i) => {
                    const opt = document.createElement('option');
                    opt.value = cam.id;
                    opt.textContent = cam.label || `Camera ${i + 1}`;
                    selectEl.appendChild(opt);
                });
                document.getElementById('cameraStatusText').textContent = `${devices.length} camera(s) detected`;
            } else {
                selectEl.innerHTML = '<option value="">No cameras detected</option>';
                document.getElementById('cameraStatusText').textContent = 'No cameras found';
            }
        } catch (err) {
            console.error('Error fetching cameras:', err);
            document.getElementById('cameraStatusText').textContent = 'Camera permission required';
        }
    }

    async function startScannerCamera() {
        if (isCameraRunning) return;
        if (typeof Html5Qrcode === 'undefined') {
            alert('QR scanner library is not loaded.');
            return;
        }

        const selectEl = document.getElementById('cameraSelect');
        const cameraId = selectEl.value;

        try {
            document.getElementById('cameraPlaceholder').classList.add('hidden');
            document.getElementById('cameraStatusText').textContent = 'Camera starting...';

            if (!html5QrScanner) {
                html5QrScanner = new Html5Qrcode("employerQrReader");
            }

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            const cameraConfig = cameraId ? { deviceId: { exact: cameraId } } : { facingMode: "environment" };

            await html5QrScanner.start(
                cameraConfig,
                config,
                onQrCodeScanned,
                (errorMessage) => {
                    // ignore frame read errors
                }
            );

            isCameraRunning = true;
            document.getElementById('startCamBtn').disabled = true;
            document.getElementById('startCamBtn').classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('stopCamBtn').disabled = false;
            document.getElementById('stopCamBtn').classList.remove('text-zinc-500', 'cursor-not-allowed');
            document.getElementById('stopCamBtn').classList.add('text-white', 'hover:bg-zinc-800');
            document.getElementById('cameraStatusText').textContent = 'Scanning live...';
        } catch (err) {
            console.error('Camera start failed:', err);
            alert('Failed to start camera. Please ensure camera permissions are granted.');
            document.getElementById('cameraPlaceholder').classList.remove('hidden');
            document.getElementById('cameraStatusText').textContent = 'Camera failed';
        }
    }

    async function stopScannerCamera() {
        if (!isCameraRunning || !html5QrScanner) return;
        try {
            await html5QrScanner.stop();
            isCameraRunning = false;
            document.getElementById('startCamBtn').disabled = false;
            document.getElementById('startCamBtn').classList.remove('opacity-50', 'cursor-not-allowed');
            document.getElementById('stopCamBtn').disabled = true;
            document.getElementById('stopCamBtn').classList.add('text-zinc-500', 'cursor-not-allowed');
            document.getElementById('stopCamBtn').classList.remove('text-white', 'hover:bg-zinc-800');
            document.getElementById('cameraPlaceholder').classList.remove('hidden');
            document.getElementById('cameraStatusText').textContent = 'Camera stopped';
        } catch (err) {
            console.error('Stop camera failed:', err);
        }
    }

    function onQrCodeScanned(decodedText) {
        if (!decodedText) return;
        // Pause scanning while showing result
        if (isCameraRunning && html5QrScanner) {
            html5QrScanner.pause();
        }
        processScannedPayload(decodedText);
    }

    async function processScannedPayload(rawCode) {
        try {
            const res = await fetch(scanEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ code: rawCode })
            });

            const data = await res.json();
            if (data.success && data.worker) {
                showWorkerPopUpModal(data.worker);
            } else {
                alert(data.message || 'Worker not recognized or invalid QR code.');
                if (isCameraRunning && html5QrScanner) {
                    html5QrScanner.resume();
                }
            }
        } catch (err) {
            console.error('Verification request failed:', err);
            alert('Failed to verify worker QR code. Please check connection and try again.');
            if (isCameraRunning && html5QrScanner) {
                html5QrScanner.resume();
            }
        }
    }

    async function handleQrFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const feedback = document.getElementById('fileScanFeedback');
        feedback.classList.remove('hidden');
        feedback.textContent = 'Reading QR image...';

        try {
            const html5Qr = new Html5Qrcode("employerQrReader");
            const imageResult = await html5Qr.scanFile(file, true);
            feedback.textContent = 'QR detected! Loading worker info...';
            processScannedPayload(imageResult);
        } catch (err) {
            console.error('File scan error:', err);
            feedback.textContent = 'Could not find a valid QR code in this image. Please try another.';
        }
    }

    function handleManualQrSubmit() {
        const val = document.getElementById('manualQrInput').value.trim();
        if (!val) {
            alert('Please enter a QR code payload or token.');
            return;
        }
        processScannedPayload(val);
    }

    function showWorkerPopUpModal(worker) {
        document.getElementById('modalWorkerName').textContent = worker.name || 'Worker';
        document.getElementById('modalWorkerId').textContent = `#${String(worker.worker_id).padStart(5, '0')}`;
        document.getElementById('modalWorkerAvatar').textContent = (worker.name || 'W').substring(0, 2).toUpperCase();
        document.getElementById('modalWorkerSex').textContent = `👤 ${worker.sex ? worker.sex.charAt(0).toUpperCase() + worker.sex.slice(1) : 'Not specified'}`;
        document.getElementById('modalWorkerAddress').textContent = `📍 ${worker.address || 'Philippines'}`;
        document.getElementById('modalWorkerContact').textContent = `📞 ${worker.contact_number || 'N/A'}`;
        document.getElementById('modalWorkerEmail').textContent = `✉️ ${worker.email || ''}`;

        const messageBtn = document.getElementById('modalMessageWorkerBtn');
        messageBtn.href = `/messages?user_id=${worker.worker_id}`;

        const statusContainer = document.getElementById('modalStatusContainer');
        const glowBackdrop = document.getElementById('modalGlowBackdrop');

        // Status Rendering based on Hired, Fired, Pending rules:
        // - Hired: status hired + work title + employer name
        // - Fired: status fired + work title + employer name
        // - Pending: status pending + DO NOT display work title or employer name
        if (worker.status === 'hired') {
            glowBackdrop.className = 'absolute -top-16 -right-16 w-56 h-56 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none';
            statusContainer.className = 'p-5 rounded-2xl border bg-emerald-950/40 border-emerald-700/80 text-emerald-100 space-y-3';
            statusContainer.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Current Work Status</span>
                    <span class="px-3.5 py-1 rounded-full bg-emerald-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Hired
                    </span>
                </div>
                <div class="space-y-1.5 pt-1 text-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="font-semibold text-emerald-300">Active Work Position:</span>
                        <span class="font-extrabold text-white text-sm">💼 ${worker.work_title || 'N/A'}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="font-semibold text-emerald-300">Employer:</span>
                        <span class="font-extrabold text-white text-sm">🏢 ${worker.employer_name || 'N/A'}</span>
                    </div>
                    ${worker.hired_at ? `
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-zinc-400">
                        <span>Hired Date:</span>
                        <span>${worker.hired_at}</span>
                    </div>` : ''}
                </div>
            `;
        } else if (worker.status === 'fired') {
            glowBackdrop.className = 'absolute -top-16 -right-16 w-56 h-56 bg-red-500/20 rounded-full blur-3xl pointer-events-none';
            statusContainer.className = 'p-5 rounded-2xl border bg-red-950/40 border-red-700/80 text-red-100 space-y-3';
            statusContainer.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-red-400">Current Work Status</span>
                    <span class="px-3.5 py-1 rounded-full bg-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Fired
                    </span>
                </div>
                <div class="space-y-1.5 pt-1 text-xs">
                    <div class="p-2.5 rounded-xl bg-red-950/80 border border-red-800 text-xs font-bold text-red-200">
                        ⚠️ Worker position was terminated (Fired).
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="font-semibold text-red-300">Previous Work:</span>
                        <span class="font-extrabold text-white text-sm">💼 ${worker.work_title || 'N/A'}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <span class="font-semibold text-red-300">Former Employer:</span>
                        <span class="font-extrabold text-white text-sm">🏢 ${worker.employer_name || 'N/A'}</span>
                    </div>
                    ${worker.ended_at ? `
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-zinc-400">
                        <span>Date Terminated:</span>
                        <span>${worker.ended_at}</span>
                    </div>` : ''}
                    ${worker.termination_reason ? `
                    <div class="text-[11px] text-zinc-400 pt-1">
                        <span class="font-bold text-zinc-300">Reason:</span> ${worker.termination_reason}
                    </div>` : ''}
                </div>
            `;
        } else {
            // Pending: DO NOT display work or employer name
            glowBackdrop.className = 'absolute -top-16 -right-16 w-56 h-56 bg-amber-500/15 rounded-full blur-3xl pointer-events-none';
            statusContainer.className = 'p-5 rounded-2xl border bg-amber-950/30 border-amber-700/70 text-amber-100 space-y-2';
            statusContainer.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Current Work Status</span>
                    <span class="px-3.5 py-1 rounded-full bg-amber-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Pending
                    </span>
                </div>
                <div class="pt-1 text-xs space-y-1">
                    <p class="font-extrabold text-white text-sm">Seeking Employment / Ready for Work</p>
                    <p class="text-xs font-medium text-zinc-400">This worker is currently looking for employment opportunities and is available for hire.</p>
                </div>
            `;
        }

        document.getElementById('workerScanPopUpModal').classList.remove('hidden');
    }

    function closeWorkerPopUpModal(resumeCamera = false) {
        document.getElementById('workerScanPopUpModal').classList.add('hidden');
        if (resumeCamera && isCameraRunning && html5QrScanner) {
            html5QrScanner.resume();
        }
    }
</script>
@endpush
@endsection
