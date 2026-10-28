<!-- Waving Hand Keyframes & Character Styling -->
<style>
    @keyframes waveHandLeft {
        0%, 100% {
            transform: rotate(0deg);
        }
        20% {
            transform: rotate(-22deg);
        }
        40% {
            transform: rotate(18deg);
        }
        60% {
            transform: rotate(-18deg);
        }
        80% {
            transform: rotate(12deg);
        }
    }

    @keyframes waveHandRight {
        0%, 100% {
            transform: rotate(0deg);
        }
        20% {
            transform: rotate(22deg);
        }
        40% {
            transform: rotate(-18deg);
        }
        60% {
            transform: rotate(18deg);
        }
        80% {
            transform: rotate(-12deg);
        }
    }

    @keyframes subtleFloat {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    .animate-worker-wave {
        transform-origin: 38px 90px;
        animation: waveHandLeft 2.2s infinite ease-in-out;
    }

    .animate-employer-wave {
        transform-origin: 142px 90px;
        animation: waveHandRight 2.4s infinite ease-in-out 0.3s;
    }

    .animate-character-float {
        animation: subtleFloat 4s ease-in-out infinite;
    }

    .animate-character-float-alt {
        animation: subtleFloat 4.4s ease-in-out infinite 0.5s;
    }
</style>

<!-- ========================================================================= -->
<!-- DESKTOP / TABLET: CHARACTERS STANDING AT SIDES HOLDING THE CONTAINER -->
<!-- ========================================================================= -->

<!-- LEFT: WORKER HOLDING THE CONTAINER & WAVING (Desktop / Tablet) -->
<div class="hidden md:block absolute -left-28 sm:-left-32 lg:-left-44 bottom-6 lg:bottom-12 z-20 pointer-events-none select-none animate-character-float">
    <div class="relative w-28 sm:w-32 lg:w-44">
        
        <!-- Speech / Welcoming Badge -->
        <div class="mb-2 text-center">
            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-lg backdrop-blur-sm">
                👷 Worker • Ready!
            </span>
        </div>

        <!-- Worker Vector SVG Illustration -->
        <svg viewBox="0 0 180 320" class="w-full h-auto drop-shadow-2xl overflow-visible">
            <!-- Shadow underneath -->
            <ellipse cx="90" cy="312" rx="45" ry="8" fill="#000000" fill-opacity="0.5" />

            <!-- Worker Legs & Work Jeans -->
            <path d="M 68,220 L 65,305 Q 65,310 75,310 L 88,310 L 88,235 L 96,235 L 96,310 L 109,310 Q 119,310 119,305 L 116,220 Z" fill="#1e293b" />
            <!-- Work Boots -->
            <rect x="62" y="300" width="28" height="12" rx="3" fill="#78350f" />
            <rect x="94" y="300" width="28" height="12" rx="3" fill="#78350f" />

            <!-- Body Torso (Blue Work Shirt) -->
            <path d="M 52,140 Q 90,135 128,140 L 122,230 L 62,230 Z" fill="#2563eb" />

            <!-- High-Vis Safety Vest (OSHA Orange) -->
            <path d="M 54,140 Q 90,135 126,140 L 122,228 L 105,228 L 108,165 Q 90,175 72,165 L 75,228 L 58,228 Z" fill="#ea580c" />
            <!-- Reflective Silver Stripes -->
            <rect x="62" y="180" width="16" height="6" fill="#f8fafc" />
            <rect x="102" y="180" width="16" height="6" fill="#f8fafc" />
            <rect x="62" y="200" width="56" height="6" fill="#f8fafc" />

            <!-- Head & Neck -->
            <rect x="80" y="115" width="20" height="25" fill="#d97706" rx="4" />
            <!-- Face -->
            <ellipse cx="90" cy="98" rx="22" ry="24" fill="#e0a97c" />
            <!-- Eyes (Friendly Smiling) -->
            <path d="M 78,92 Q 83,88 88,92" stroke="#1f2937" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <path d="M 94,92 Q 99,88 104,92" stroke="#1f2937" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <!-- Cheerful Smile -->
            <path d="M 81,105 Q 90,116 101,105" stroke="#991b1b" stroke-width="2.5" stroke-linecap="round" fill="#ffffff" />

            <!-- Safety Construction Hard Hat (Yellow) -->
            <path d="M 64,88 Q 90,52 116,88 Z" fill="#eab308" />
            <path d="M 58,88 Q 90,82 122,88 Q 90,92 58,88 Z" fill="#facc15" />
            <!-- Hard Hat Front Lamp/Stripe -->
            <rect x="84" y="66" width="12" height="12" rx="2" fill="#ffffff" fill-opacity="0.9" />

            <!-- RIGHT ARM & HAND: HOLDING THE CONTAINER BORDER! -->
            <!-- Arm reaching towards the right (container wall) -->
            <path d="M 125,145 Q 155,160 172,175 L 165,190 Q 145,175 120,165 Z" fill="#2563eb" />
            <!-- Right Hand Grasping Container Edge -->
            <!-- Gripping fingers clearly wrapping onto the container's left border! -->
            <g transform="translate(162, 168)">
                <ellipse cx="6" cy="14" rx="8" ry="10" fill="#e0a97c" />
                <!-- 4 Gripped Fingers wrapped over card edge -->
                <rect x="6" y="2" width="14" height="6" rx="3" fill="#f59e0b" />
                <rect x="8" y="9" width="15" height="6" rx="3" fill="#e0a97c" />
                <rect x="8" y="16" width="15" height="6" rx="3" fill="#d97706" />
                <rect x="6" y="23" width="13" height="6" rx="3" fill="#c2410c" />
            </g>

            <!-- LEFT ARM & HAND: WAVING TO USER! (Animated) -->
            <g class="animate-worker-wave">
                <!-- Upper Arm raised high -->
                <path d="M 55,148 Q 40,120 32,88 L 46,84 Q 52,112 65,138 Z" fill="#2563eb" />
                <!-- Forearm & Waving Hand -->
                <g transform="translate(16, 42)">
                    <rect x="14" y="28" width="12" height="26" rx="5" fill="#e0a97c" transform="rotate(-15, 20, 40)" />
                    <!-- Palm -->
                    <circle cx="25" cy="22" r="12" fill="#e0a97c" />
                    <!-- 5 Animated Waving Fingers -->
                    <rect x="14" y="6" width="4.5" height="14" rx="2" fill="#e0a97c" transform="rotate(-20, 16, 12)" />
                    <rect x="20" y="4" width="4.5" height="16" rx="2" fill="#e0a97c" transform="rotate(-5, 22, 12)" />
                    <rect x="26" y="5" width="4.5" height="15" rx="2" fill="#e0a97c" transform="rotate(10, 28, 12)" />
                    <rect x="32" y="8" width="4" height="13" rx="2" fill="#e0a97c" transform="rotate(22, 34, 14)" />
                    <!-- Thumb -->
                    <rect x="9" y="19" width="4.5" height="9" rx="2" fill="#e0a97c" transform="rotate(-50, 11, 23)" />
                </g>
            </g>
        </svg>
    </div>
</div>

<!-- RIGHT: EMPLOYER HOLDING THE CONTAINER & WAVING (Desktop / Tablet) -->
<div class="hidden md:block absolute -right-28 sm:-right-32 lg:-right-44 bottom-6 lg:bottom-12 z-20 pointer-events-none select-none animate-character-float-alt">
    <div class="relative w-28 sm:w-32 lg:w-44">
        
        <!-- Speech / Welcoming Badge -->
        <div class="mb-2 text-center">
            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-lg backdrop-blur-sm">
                🏢 Employer • Hiring!
            </span>
        </div>

        <!-- Employer Vector SVG Illustration -->
        <svg viewBox="0 0 180 320" class="w-full h-auto drop-shadow-2xl overflow-visible">
            <!-- Shadow underneath -->
            <ellipse cx="90" cy="312" rx="45" ry="8" fill="#000000" fill-opacity="0.5" />

            <!-- Formal Slacks -->
            <path d="M 68,220 L 66,305 Q 66,310 76,310 L 88,310 L 88,235 L 94,235 L 94,310 L 106,310 Q 116,310 116,305 L 114,220 Z" fill="#0f172a" />
            <!-- Polished Dress Shoes -->
            <rect x="62" y="302" width="28" height="10" rx="3" fill="#18181b" />
            <rect x="94" y="302" width="28" height="10" rx="3" fill="#18181b" />

            <!-- Business Suit Blazer (Navy/Charcoal) -->
            <path d="M 52,140 Q 90,135 128,140 L 124,228 L 56,228 Z" fill="#1e293b" />
            <!-- White Collared Dress Shirt & Emerald Tie -->
            <polygon points="90,140 82,175 98,175" fill="#f8fafc" />
            <polygon points="90,150 86,195 90,210 94,195" fill="#10b981" />
            <!-- Lapels -->
            <path d="M 54,140 L 80,185 L 75,225" stroke="#0f172a" stroke-width="2" fill="none" />
            <path d="M 126,140 L 100,185 L 105,225" stroke="#0f172a" stroke-width="2" fill="none" />

            <!-- Employer ID Lanyard -->
            <path d="M 78,142 Q 90,188 90,195 Q 90,188 102,142" stroke="#10b981" stroke-width="2.5" fill="none" />
            <rect x="85" y="195" width="10" height="14" rx="2" fill="#ffffff" stroke="#10b981" stroke-width="1" />

            <!-- Head & Neck -->
            <rect x="80" y="115" width="20" height="25" fill="#d97706" rx="4" />
            <!-- Face -->
            <ellipse cx="90" cy="98" rx="22" ry="24" fill="#dfa87b" />
            <!-- Styled Modern Haircut -->
            <path d="M 68,90 Q 70,68 90,66 Q 112,68 114,90 Q 110,76 90,74 Q 72,76 68,90 Z" fill="#18181b" />
            <path d="M 66,88 Q 66,74 76,70 Q 72,82 72,92 Z" fill="#18181b" />

            <!-- Eyes (Friendly Smiling) -->
            <path d="M 78,92 Q 83,88 88,92" stroke="#1f2937" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <path d="M 94,92 Q 99,88 104,92" stroke="#1f2937" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <!-- Cheerful Smile -->
            <path d="M 81,105 Q 90,116 101,105" stroke="#991b1b" stroke-width="2.5" stroke-linecap="round" fill="#ffffff" />

            <!-- LEFT ARM & HAND: HOLDING THE CONTAINER BORDER! -->
            <!-- Arm reaching towards the left (container wall) -->
            <path d="M 55,145 Q 25,160 8,175 L 15,190 Q 35,175 60,165 Z" fill="#1e293b" />
            <!-- Left Hand Grasping Container Edge -->
            <g transform="translate(0, 168)">
                <ellipse cx="14" cy="14" rx="8" ry="10" fill="#dfa87b" />
                <!-- 4 Gripped Fingers wrapped over card edge -->
                <rect x="0" y="2" width="14" height="6" rx="3" fill="#f59e0b" />
                <rect x="-3" y="9" width="15" height="6" rx="3" fill="#dfa87b" />
                <rect x="-3" y="16" width="15" height="6" rx="3" fill="#d97706" />
                <rect x="0" y="23" width="13" height="6" rx="3" fill="#c2410c" />
            </g>

            <!-- RIGHT ARM & HAND: WAVING TO USER! (Animated) -->
            <g class="animate-employer-wave">
                <!-- Upper Arm raised high -->
                <path d="M 125,148 Q 140,120 148,88 L 134,84 Q 128,112 115,138 Z" fill="#1e293b" />
                <!-- Forearm & Waving Hand -->
                <g transform="translate(126, 42)">
                    <rect x="14" y="28" width="12" height="26" rx="5" fill="#dfa87b" transform="rotate(15, 20, 40)" />
                    <!-- Palm -->
                    <circle cx="22" cy="22" r="12" fill="#dfa87b" />
                    <!-- 5 Animated Waving Fingers -->
                    <rect x="12" y="8" width="4" height="13" rx="2" fill="#dfa87b" transform="rotate(-22, 14, 14)" />
                    <rect x="18" y="5" width="4.5" height="15" rx="2" fill="#dfa87b" transform="rotate(-10, 20, 12)" />
                    <rect x="24" y="4" width="4.5" height="16" rx="2" fill="#dfa87b" transform="rotate(5, 26, 12)" />
                    <rect x="30" y="6" width="4.5" height="14" rx="2" fill="#dfa87b" transform="rotate(20, 32, 12)" />
                    <!-- Thumb -->
                    <rect x="34" y="19" width="4.5" height="9" rx="2" fill="#dfa87b" transform="rotate(50, 36, 23)" />
                </g>
            </g>
        </svg>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MOBILE: CHARACTERS RESTING ON TOP OF CONTAINER WAVING CHEERFULLY (< md)  -->
<!-- ========================================================================= -->
<div class="md:hidden flex items-center justify-between px-4 pb-2 text-xs select-none pointer-events-none">
    <!-- Worker Avatar & Waving Hand on Mobile Top-Left -->
    <div class="flex items-center gap-2">
        <div class="relative w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-xl shadow-md">
            👷
            <span class="absolute -top-1 -right-1 text-sm animate-worker-wave inline-block">👋</span>
        </div>
        <div class="text-left">
            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-amber-950 text-amber-300 border border-amber-800">Worker</span>
            <p class="text-[11px] font-bold text-white mt-0.5">Welcome!</p>
        </div>
    </div>

    <!-- Center Bridge -->
    <div class="flex items-center gap-1 text-[10px] font-bold text-zinc-500">
        <span>🤝</span>
        <span>Findjobph</span>
    </div>

    <!-- Employer Avatar & Waving Hand on Mobile Top-Right -->
    <div class="flex items-center gap-2">
        <div class="text-right">
            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-950 text-emerald-300 border border-emerald-800">Employer</span>
            <p class="text-[11px] font-bold text-white mt-0.5">Hiring Now!</p>
        </div>
        <div class="relative w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-xl shadow-md">
            🏢
            <span class="absolute -top-1 -left-1 text-sm animate-employer-wave inline-block">👋</span>
        </div>
    </div>
</div>

