@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 text-zinc-100">

    <!-- Top Action Bar (hidden when printing) -->
    <div class="mb-6 flex items-center justify-between gap-4 print:hidden">
        <a href="{{ route('worker.applications') }}"
            class="px-4 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Applications
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()"
                class="px-4 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs shadow-md flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Print Pass
            </button>
            <button type="button" onclick="downloadQrImage()"
                class="px-4 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Save QR
            </button>
        </div>
    </div>

    <!-- The Digital Pass Card -->
    <div id="workerDigitalPassCard" class="p-8 rounded-3xl bg-[#121215] border-2 border-zinc-850 shadow-2xl space-y-6 relative overflow-hidden print:bg-white print:text-black print:border-black print:shadow-none">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-zinc-800 pb-5 print:border-black">
            <div class="flex items-center gap-3">
                <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-12 h-12 rounded-full shadow-md">
                <div>
                    <h2 class="text-base font-black text-white tracking-wide uppercase print:text-black">
                        Findjob<span class="text-zinc-500">ph</span>
                    </h2>
                    <p class="text-xs text-zinc-400 font-bold uppercase tracking-wider print:text-zinc-600">Official Worker Digital ID Pass</p>
                </div>
            </div>

            <!-- Live Status Badge -->
            <div>
                @if($qrProfile['status'] === 'hired')
                    <span class="px-3.5 py-1.5 rounded-full bg-emerald-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Hired
                    </span>
                @elseif($qrProfile['status'] === 'fired')
                    <span class="px-3.5 py-1.5 rounded-full bg-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Fired
                    </span>
                @else
                    <span class="px-3.5 py-1.5 rounded-full bg-amber-600 text-white font-black text-xs uppercase tracking-wider shadow-md">
                        Pending
                    </span>
                @endif
            </div>
        </div>

        <!-- Worker Information & QR Section -->
        <div class="flex flex-col md:flex-row items-center gap-8">
            
            <!-- Worker Details Column -->
            <div class="flex-1 space-y-4 w-full">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 print:text-zinc-600">Worker Name</span>
                    <h3 class="text-2xl font-black text-white print:text-black tracking-tight">
                        {{ $qrProfile['name'] }}
                    </h3>
                    <p class="text-xs font-semibold text-zinc-400 print:text-zinc-700">
                        Worker ID: <strong class="text-zinc-200 print:text-black">#{{ str_pad($qrProfile['worker_id'], 5, '0', STR_PAD_LEFT) }}</strong>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 print:text-zinc-600">Contact Number</span>
                        <p class="font-bold text-white print:text-black">{{ $qrProfile['contact_number'] ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 print:text-zinc-600">Sex</span>
                        <p class="font-bold text-white print:text-black">{{ $qrProfile['sex'] ? ucfirst($qrProfile['sex']) : 'N/A' }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 print:text-zinc-600">Email Address</span>
                        <p class="font-bold text-white print:text-black break-all">{{ $qrProfile['email'] }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 print:text-zinc-600">Address</span>
                        <p class="font-bold text-white print:text-black">{{ $qrProfile['address'] ?: 'Philippines' }}</p>
                    </div>
                </div>

                <!-- Live Employment Status Block -->
                <div class="p-4 rounded-2xl border
                    @if($qrProfile['status'] === 'hired')
                        bg-emerald-950/40 border-emerald-800 text-emerald-100 print:bg-gray-100 print:border-black print:text-black
                    @elseif($qrProfile['status'] === 'fired')
                        bg-red-950/40 border-red-800 text-red-100 print:bg-gray-100 print:border-black print:text-black
                    @else
                        bg-amber-950/30 border-amber-800 text-amber-100 print:bg-gray-100 print:border-black print:text-black
                    @endif">
                    
                    @if($qrProfile['status'] === 'hired')
                        <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 print:text-emerald-700">Currently Hired</p>
                        <p class="text-sm font-extrabold text-white print:text-black mt-0.5">💼 {{ $qrProfile['work_title'] }}</p>
                        <p class="text-xs font-semibold text-zinc-300 print:text-zinc-700 mt-0.5">🏢 {{ $qrProfile['employer_name'] }}</p>
                        @if(!empty($qrProfile['hired_at']))
                            <p class="text-[11px] text-zinc-400 print:text-zinc-600 mt-1">Since {{ $qrProfile['hired_at'] }}</p>
                        @endif

                    @elseif($qrProfile['status'] === 'fired')
                        <p class="text-[11px] font-bold uppercase tracking-wider text-red-400 print:text-red-700">Position Ended (Fired)</p>
                        <p class="text-sm font-extrabold text-white print:text-black mt-0.5">💼 {{ $qrProfile['work_title'] }}</p>
                        <p class="text-xs font-semibold text-zinc-300 print:text-zinc-700 mt-0.5">🏢 {{ $qrProfile['employer_name'] }}</p>
                        @if(!empty($qrProfile['ended_at']))
                            <p class="text-[11px] text-zinc-400 print:text-zinc-600 mt-1">Ended: {{ $qrProfile['ended_at'] }}</p>
                        @endif

                    @else
                        <!-- Pending: DO NOT display work or employer name -->
                        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-400 print:text-amber-700">Status: Pending</p>
                        <p class="text-xs font-bold text-white print:text-black mt-0.5">Available for Hire / Ready for Work</p>
                    @endif
                </div>
            </div>

            <!-- Auto-Generated QR Code Column -->
            <div class="flex flex-col items-center justify-center text-center shrink-0">
                <div class="p-3 bg-white rounded-2xl shadow-xl border border-zinc-200">
                    <div id="fullPassQr"></div>
                </div>
                <p class="text-[11px] font-bold text-zinc-400 print:text-zinc-600 mt-2 uppercase tracking-wider">
                    Scan with Employer Scanner
                </p>
                <p class="text-[10px] text-zinc-500 print:text-zinc-500">
                    Auto-synced with live database
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-zinc-800 pt-4 flex items-center justify-between text-[11px] font-medium text-zinc-400 print:border-black print:text-zinc-600">
            <span>Findjobph Verified Worker Authentication</span>
            <span>Live Sync Active</span>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qrContainer = document.getElementById('fullPassQr');
        if (qrContainer && typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: @json($qrPayload),
                width: 170,
                height: 170,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    });

    function downloadQrImage() {
        const img = document.querySelector('#fullPassQr img');
        const canvas = document.querySelector('#fullPassQr canvas');
        let dataUrl = null;
        if (img && img.src && img.src.startsWith('data:')) {
            dataUrl = img.src;
        } else if (canvas) {
            dataUrl = canvas.toDataURL('image/png');
        }
        if (dataUrl) {
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = 'worker-qr-pass-{{ $qrProfile["worker_id"] }}.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    }
</script>
@endpush
@endsection
