@extends('layouts.app')

@section('content')
<div class="relative max-w-xl mx-auto py-6 sm:py-10 text-zinc-100">

    <!-- Atmospheric Fullscreen Background Picture -->
    <div class="fixed inset-0 z-0 pointer-events-none select-none overflow-hidden" aria-hidden="true">
        <img src="{{ asset('images/auth-bg.jpg') }}" alt="FindJobPH Auth Background" class="w-full h-full object-cover object-center filter blur-[2px] brightness-[0.35] contrast-[1.15] scale-105">
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-[#09090b]/60 to-[#09090b]/90"></div>
    </div>

    <!-- Worker & Employer Characters Holding the Container & Waving Hands -->
    @include('auth.partials.holding-characters')

    <!-- Sign Up Container (Held by Characters) -->
    <div class="relative z-10 p-7 sm:p-10 rounded-3xl shadow-2xl bg-[#121215] border-2 border-zinc-800 backdrop-blur-md">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                {{ !empty($googleEmail) ? 'Complete Account Setup' : 'Create Account' }}
            </h1>
            <p class="text-xs font-medium text-zinc-400 mt-1">
                {{ !empty($googleEmail) ? 'Set up your password, names, and details to finish your Google registration' : 'Choose your account type to get started' }}
            </p>
        </div>

        <!-- Server-side Validation Error Banner -->
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-red-950/80 border border-red-700 text-red-200 shadow-lg animate-in fade-in duration-200">
                <div class="flex items-center gap-2 font-black text-xs text-red-300">
                    <svg class="w-4 h-4 shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Please correct the following errors:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-1 text-[11px] font-semibold text-red-200 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Client-side Floating Trapping Alert -->
        <div id="clientErrorBanner" class="hidden mb-5 p-4 rounded-2xl bg-red-950/80 border border-red-700 text-red-200 shadow-lg animate-in fade-in duration-200">
            <div class="flex items-start gap-2.5">
                <svg class="w-4 h-4 shrink-0 text-red-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 text-xs">
                    <p class="font-black text-red-300" id="clientErrorTitle">Action Required</p>
                    <p class="font-semibold text-red-200 mt-0.5" id="clientErrorMessage"></p>
                </div>
            </div>
        </div>

        <!-- Google Connected Banner (if continuing with Google) -->
        @if(!empty($googleEmail))
            <div class="mb-6 p-4 rounded-2xl bg-sky-950/80 border border-sky-800 flex items-start gap-3 shadow-md">
                <div class="w-9 h-9 rounded-full bg-zinc-900 border border-zinc-700 shadow-sm flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                    </svg>
                </div>
                <div class="flex-1 text-xs">
                    <p class="font-black text-white">
                        Continuing with Google: <span class="text-sky-400 font-bold underline">{{ $googleEmail }}</span>
                    </p>
                    <p class="font-semibold text-zinc-300 mt-0.5">
                        You must set up your password, names, and contact details below before accessing your account.
                    </p>
                </div>
            </div>
        @endif

        @php
            $googleNameParts = !empty($googleName) ? explode(' ', $googleName, 2) : ['', ''];
            $prefillFirst = $googleNameParts[0] ?? '';
            $prefillLast = $googleNameParts[1] ?? '';

            // Parse previous full address if validation returned
            $oldAddress = old('address', '');
            $oldParts = array_map('trim', explode(',', $oldAddress));
            $oldBrgy = old('address_brgy', $oldParts[0] ?? '');
            $oldCity = old('address_city', $oldParts[1] ?? '');
            $oldProvince = old('address_province', $oldParts[2] ?? '');
            $currentRole = old('role', $defaultRole ?? 'worker');
        @endphp

        <!-- Role Selector Switch (Worker vs Employer) -->
        <div class="mb-6 p-1 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center gap-1">
            <button type="button" id="roleBtnWorker" onclick="switchRole('worker')"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 {{ $currentRole === 'worker' ? 'shadow-sm bg-white text-black' : 'text-zinc-400 hover:text-white' }}">
                Worker
            </button>
            <button type="button" id="roleBtnEmployer" onclick="switchRole('employer')"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 {{ $currentRole === 'employer' ? 'shadow-sm bg-white text-black' : 'text-zinc-400 hover:text-white' }}">
                Employer
            </button>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-4" onsubmit="return validateRegisterForm(event)">
            @csrf
            <input type="hidden" name="role" id="roleInput" value="{{ $currentRole }}">

            <!-- WORKER SPECIFIC FIELDS -->
            <div id="workerFields" class="space-y-4 {{ $currentRole === 'employer' ? 'hidden' : '' }}">
                <p class="text-xs font-black uppercase tracking-wider text-white border-b border-zinc-800 pb-2">
                    Worker Personal Information
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label for="worker_first_name" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                            First Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="first_name" id="worker_first_name" value="{{ old('first_name', $prefillFirst) }}"
                            placeholder="First Name"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('first_name') border-rose-500 @enderror">
                        @error('first_name')
                            <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="worker_middle_name" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                            Middle Name
                        </label>
                        <input type="text" name="middle_name" id="worker_middle_name" value="{{ old('middle_name') }}"
                            placeholder="Middle Name"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('middle_name') border-rose-500 @enderror">
                        @error('middle_name')
                            <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="worker_last_name" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                            Last Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="last_name" id="worker_last_name" value="{{ old('last_name', $prefillLast) }}"
                            placeholder="Last Name"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('last_name') border-rose-500 @enderror">
                        @error('last_name')
                            <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Worker Sex Selection -->
                <div>
                    <label for="worker_sex" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                        Sex <span class="text-red-400">*</span>
                    </label>
                    <select name="sex" id="worker_sex" class="w-full px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('sex') border-rose-500 @enderror">
                        <option value="" class="bg-zinc-900 text-zinc-300">-- Select Sex --</option>
                        <option value="Male" class="bg-zinc-900 text-white" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" class="bg-zinc-900 text-white" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" class="bg-zinc-900 text-white" {{ old('sex') == 'Other' ? 'selected' : '' }}>Other</option>
                        <option value="Prefer not to say" class="bg-zinc-900 text-white" {{ old('sex') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                    @error('sex')
                        <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- EMPLOYER SPECIFIC FIELDS -->
            <div id="employerFields" class="space-y-4 {{ $currentRole === 'employer' ? '' : 'hidden' }}">
                <p class="text-xs font-black uppercase tracking-wider text-white border-b border-zinc-800 pb-2">
                    Employer Personal Information
                </p>

                <!-- Employer Name, Middle Name, Last Name -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label for="employer_first_name" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                            First Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="first_name" id="employer_first_name" value="{{ old('first_name', $prefillFirst) }}"
                            placeholder="First Name"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('first_name') border-rose-500 @enderror">
                        @error('first_name')
                            <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="employer_middle_name" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                            Middle Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="middle_name" id="employer_middle_name" value="{{ old('middle_name') }}"
                            placeholder="Middle Name"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('middle_name') border-rose-500 @enderror">
                        @error('middle_name')
                            <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="employer_last_name" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                            Last Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="last_name" id="employer_last_name" value="{{ old('last_name', $prefillLast) }}"
                            placeholder="Last Name"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('last_name') border-rose-500 @enderror">
                        @error('last_name')
                            <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="company_name" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                            Company / Business Name <span class="text-zinc-400 font-normal">(Optional)</span>
                        </label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $googleName ?? '') }}"
                            placeholder="e.g. Catigbian Agro Builders"
                            class="w-full px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('company_name') border-rose-500 @enderror">
                        @error('company_name')
                            <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Employer Sex Selection -->
                    <div>
                        <label for="employer_sex" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                            Sex <span class="text-red-400">*</span>
                        </label>
                        <select name="employer_sex" id="employer_sex" class="w-full px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('sex') border-rose-500 @enderror">
                            <option value="" class="bg-zinc-900 text-zinc-300">-- Select Sex --</option>
                            <option value="Male" class="bg-zinc-900 text-white" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" class="bg-zinc-900 text-white" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" class="bg-zinc-900 text-white" {{ old('sex') == 'Other' ? 'selected' : '' }}>Other</option>
                            <option value="Prefer not to say" class="bg-zinc-900 text-white" {{ old('sex') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                        @error('sex')
                            <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- COMMON FIELDS (Required for Both) -->
            <!-- Email Field (Unique Trapping) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="reg_email" class="block text-xs font-extrabold uppercase tracking-wider text-white">
                        Email Address <span class="text-red-400">*</span>
                    </label>
                    <span class="text-[10px] font-bold text-zinc-400">Must be unique</span>
                </div>
                <input type="email" name="email" id="reg_email" value="{{ old('email', $googleEmail ?? '') }}" required
                    placeholder="email@example.com"
                    class="w-full px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('email') border-rose-500 @enderror">
                @error('email')
                    <div class="mt-1.5 flex items-center gap-1.5 text-xs text-rose-400 font-bold">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- Mobile Number (11-Digit PH Trapping) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="contact_number" class="block text-xs font-extrabold uppercase tracking-wider text-white">
                        Mobile Number (11 Digits PH) <span class="text-red-400">*</span>
                    </label>
                    <span id="contactStatusBadge" class="text-[11px] font-bold text-zinc-400">
                        Format: 09XXXXXXXXX
                    </span>
                </div>
                <div class="relative">
                    <input type="tel" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" required
                        maxlength="11" placeholder="09123456789"
                        class="w-full px-4 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('contact_number') border-rose-500 @enderror">
                    <div id="contactValidBadge" class="hidden absolute right-3 top-2.5 px-2 py-0.5 rounded-md bg-emerald-950 text-emerald-300 border border-emerald-700 text-[10px] font-extrabold">
                        ✓ Valid 11 Digits
                    </div>
                </div>
                @error('contact_number')
                    <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Full Address Format: Barangay, Municipality/City, Province -->
            <div class="space-y-2 pt-1">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-white">
                        Full Address Format <span class="text-red-400">*</span>
                    </label>
                    <span class="text-[10px] font-bold text-zinc-400">Brgy, Municipality/City, Province</span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div>
                        <label for="address_brgy" class="block text-[11px] font-bold text-zinc-200 mb-1">
                            Barangay <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="address_brgy" id="address_brgy" value="{{ $oldBrgy }}" required
                            placeholder="e.g. Brgy. Poblacion"
                            class="w-full px-3 py-2 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all">
                    </div>
                    <div>
                        <label for="address_city" class="block text-[11px] font-bold text-zinc-200 mb-1">
                            Municipality / City <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="address_city" id="address_city" value="{{ $oldCity }}" required
                            placeholder="e.g. Catigbian"
                            class="w-full px-3 py-2 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all">
                    </div>
                    <div>
                        <label for="address_province" class="block text-[11px] font-bold text-zinc-200 mb-1">
                            Province <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="address_province" id="address_province" value="{{ $oldProvince }}" required
                            placeholder="e.g. Bohol"
                            class="w-full px-3 py-2 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-xs font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all">
                    </div>
                </div>

                <!-- Hidden Synchronized Address Field -->
                <input type="hidden" name="address" id="full_address" value="{{ old('address') }}">

                <!-- Live Address Format Preview -->
                <div class="p-3 rounded-xl bg-zinc-900 border border-zinc-700 text-[11px]">
                    <span class="font-bold text-zinc-400 uppercase tracking-wider text-[10px]">📍 Full Address Preview:</span>
                    <span id="addressPreviewText" class="font-extrabold text-white ml-1">
                        {{ old('address') ? old('address') : 'Please fill in Barangay, Municipality/City, and Province' }}
                    </span>
                </div>
                @error('address')
                    <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Set Up Password (Strong Password Trapping) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <div>
                    <label for="reg_password" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                        Strong Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="reg_password" required minlength="8"
                            placeholder="Min 8 characters"
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all @error('password') border-rose-500 @enderror">
                        <button type="button" onclick="togglePasswordVisibility('reg_password', this)"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-zinc-300 hover:text-white focus:outline-none cursor-pointer transition-colors z-10"
                            title="Show/Hide password">
                            <svg class="w-4 h-4 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="w-4 h-4 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-400 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-extrabold uppercase tracking-wider text-white mb-1.5">
                        Confirm Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                            placeholder="Re-enter password"
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl bg-[#18181b] border border-zinc-700 text-white placeholder-zinc-400 text-sm font-semibold focus:outline-none focus:border-white focus:bg-[#202024] transition-all">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-zinc-300 hover:text-white focus:outline-none cursor-pointer transition-colors z-10"
                            title="Show/Hide password">
                            <svg class="w-4 h-4 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="w-4 h-4 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <div id="passwordMatchStatus" class="text-[11px] font-bold mt-1.5 hidden"></div>
                </div>
            </div>

            <!-- Real-time Password Strength Meter & Interactive Checklist -->
            <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-800 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-white">Password Strength:</span>
                    <span id="strengthLabel" class="text-[11px] font-extrabold text-zinc-400">Not Entered</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-zinc-800 overflow-hidden">
                    <div id="strengthBar" class="h-full w-0 transition-all duration-300 rounded-full bg-rose-500"></div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1 text-[11px]">
                    <div id="ruleLength" class="flex items-center gap-1.5 text-zinc-400 font-semibold transition-colors">
                        <span class="rule-icon w-3.5 h-3.5 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-300 text-[10px]">○</span>
                        <span>8+ Characters</span>
                    </div>
                    <div id="ruleUpper" class="flex items-center gap-1.5 text-zinc-400 font-semibold transition-colors">
                        <span class="rule-icon w-3.5 h-3.5 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-300 text-[10px]">○</span>
                        <span>Uppercase (A-Z)</span>
                    </div>
                    <div id="ruleLower" class="flex items-center gap-1.5 text-zinc-400 font-semibold transition-colors">
                        <span class="rule-icon w-3.5 h-3.5 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-300 text-[10px]">○</span>
                        <span>Lowercase (a-z)</span>
                    </div>
                    <div id="ruleNumber" class="flex items-center gap-1.5 text-zinc-400 font-semibold transition-colors">
                        <span class="rule-icon w-3.5 h-3.5 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-300 text-[10px]">○</span>
                        <span>Number (0-9)</span>
                    </div>
                    <div id="ruleSpecial" class="flex items-center gap-1.5 text-zinc-400 font-semibold transition-colors sm:col-span-2">
                        <span class="rule-icon w-3.5 h-3.5 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-300 text-[10px]">○</span>
                        <span>Special Character (!@#$%^&*)</span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitSignupBtn"
                class="w-full py-3.5 px-4 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-sm shadow-md transition-all mt-3">
                {{ !empty($googleEmail) ? 'Complete Account Setup' : 'Sign Up' }}
            </button>
        </form>

        <!-- Divider -->
        <div class="my-5 flex items-center">
            <div class="flex-grow border-t border-zinc-800"></div>
            <span class="shrink mx-3 text-xs font-bold text-zinc-400 uppercase">Or</span>
            <div class="flex-grow border-t border-zinc-800"></div>
        </div>

        <!-- Hidden Google Auth Form -->
        <form method="POST" action="{{ route('auth.google') }}" id="googleAuthForm" class="hidden">
            @csrf
            <input type="hidden" name="flow" value="register">
            <input type="hidden" name="google_role" id="googleRoleField" value="{{ $currentRole }}">
            <input type="hidden" name="credential" id="googleCredentialInput" value="">
            <input type="hidden" name="google_email" id="googleEmailInput" value="">
            <input type="hidden" name="google_name" id="googleNameInput" value="">
        </form>

        <!-- Continue with Google Button -->
        <button type="button" id="triggerGoogleBtn" onclick="openRealGoogleModal()"
            class="w-full py-3 px-4 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-xs shadow-sm flex items-center justify-center gap-2.5 transition-colors">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <span class="text-white font-bold">Continue with Google</span>
        </button>

        <!-- Footer Link -->
        <div class="mt-6 text-center pt-4 border-t border-zinc-800">
            <p class="text-xs font-semibold text-zinc-300">
                Already have an account?
                <a href="{{ route('login') }}" class="font-extrabold text-white hover:underline ml-1">
                    Sign In
                </a>
            </p>
        </div>

    </div>
</div>

<!-- ==================== REAL GOOGLE ACCOUNT CHOOSER MODAL (Dark Mode) ==================== -->
<div id="realGoogleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden transition-all">
    <div class="w-full max-w-sm rounded-3xl bg-[#141418] p-6 sm:p-7 shadow-2xl border border-zinc-800 space-y-4 animate-in fade-in zoom-in-95 duration-150 text-zinc-100">
        
        <!-- Google Header -->
        <div class="flex items-start justify-between border-b border-zinc-800 pb-3">
            <div class="flex items-center gap-2.5">
                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                <div>
                    <h3 class="text-sm font-bold text-white">Choose an account</h3>
                    <p class="text-[11px] text-zinc-400 font-medium">to continue to <strong class="text-white">Findjobph</strong></p>
                </div>
            </div>
            <button type="button" onclick="closeRealGoogleModal()" class="text-zinc-400 hover:text-white p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <p class="text-[11px] font-medium text-zinc-300">
            Select a verified Google account to sign up:
        </p>

        <!-- Real Detected Device Google Accounts -->
        <div id="deviceGoogleAccountsList" class="space-y-2 max-h-56 overflow-y-auto pr-1">
            <!-- Dynamically populated from device sessions -->
        </div>

        <!-- Add / Use Another Real Account -->
        <div class="pt-2 border-t border-zinc-800 space-y-2.5">
            <button type="button" onclick="toggleAddGoogleAccountForm()" id="showUseAnotherAccountBtn"
                class="w-full py-2 px-3 rounded-2xl hover:bg-zinc-850 text-zinc-300 font-bold text-xs flex items-center gap-3 transition-colors text-left border border-dashed border-zinc-700">
                <div class="w-8 h-8 rounded-full bg-zinc-800 text-zinc-300 flex items-center justify-center font-bold text-sm">
                    +
                </div>
                <div>
                    <p class="text-xs font-bold text-white">Use another account</p>
                    <p class="text-[10px] text-zinc-400 font-medium">Sign in with any Google email</p>
                </div>
            </button>

            <!-- Inline Form for Another Google Account -->
            <div id="useAnotherAccountForm" class="hidden p-3 rounded-2xl bg-zinc-900 border border-zinc-750 space-y-2">
                <label for="directGoogleEmail" class="block text-[10px] font-bold uppercase text-zinc-400">Google Email</label>
                <input type="email" id="directGoogleEmail" placeholder="your.name@gmail.com"
                    class="w-full px-3 py-2 rounded-xl bg-[#18181b] border border-zinc-700 text-white text-xs font-semibold focus:border-white focus:outline-none">
                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" onclick="toggleAddGoogleAccountForm(false)" class="px-3 py-1 rounded-lg text-xs font-bold text-zinc-400 hover:text-white">
                        Cancel
                    </button>
                    <button type="button" onclick="submitRealGoogleDirect()" class="px-3.5 py-1 rounded-lg bg-white hover:bg-zinc-200 text-black text-xs font-extrabold shadow-sm">
                        Continue
                    </button>
                </div>
            </div>
        </div>

        <p class="text-[10px] text-zinc-500 text-center leading-normal">
            Your Google profile and email will automatically connect with Findjobph.
        </p>

    </div>
</div>

<script>
    /**
     * Switch role between Worker and Employer
     */
    function switchRole(role) {
        const roleInput = document.getElementById('roleInput');
        const googleRoleField = document.getElementById('googleRoleField');
        const workerFields = document.getElementById('workerFields');
        const employerFields = document.getElementById('employerFields');
        const roleBtnWorker = document.getElementById('roleBtnWorker');
        const roleBtnEmployer = document.getElementById('roleBtnEmployer');

        const workerFirstName = document.getElementById('worker_first_name');
        const workerMiddleName = document.getElementById('worker_middle_name');
        const workerLastName = document.getElementById('worker_last_name');
        const workerSex = document.getElementById('worker_sex');

        const employerFirstName = document.getElementById('employer_first_name');
        const employerMiddleName = document.getElementById('employer_middle_name');
        const employerLastName = document.getElementById('employer_last_name');
        const employerSex = document.getElementById('employer_sex');

        roleInput.value = role;
        if (googleRoleField) googleRoleField.value = role;

        if (role === 'employer') {
            workerFields.classList.add('hidden');
            employerFields.classList.remove('hidden');

            roleBtnEmployer.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 shadow-sm bg-white text-black';
            roleBtnWorker.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 text-zinc-400 hover:text-white';

            // Enable employer fields
            employerFirstName.disabled = false;
            employerMiddleName.disabled = false;
            employerLastName.disabled = false;
            employerSex.disabled = false;

            // Disable worker fields
            workerFirstName.disabled = true;
            workerMiddleName.disabled = true;
            workerLastName.disabled = true;
            workerSex.disabled = true;
        } else {
            employerFields.classList.add('hidden');
            workerFields.classList.remove('hidden');

            roleBtnWorker.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 shadow-sm bg-white text-black';
            roleBtnEmployer.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 text-zinc-400 hover:text-white';

            // Enable worker fields
            workerFirstName.disabled = false;
            workerMiddleName.disabled = false;
            workerLastName.disabled = false;
            workerSex.disabled = false;

            // Disable employer fields
            employerFirstName.disabled = true;
            employerMiddleName.disabled = true;
            employerLastName.disabled = true;
            employerSex.disabled = true;
        }
    }

    /**
     * Mobile Number formatting and 11-digit PH validation
     */
    const contactInput = document.getElementById('contact_number');
    const contactStatusBadge = document.getElementById('contactStatusBadge');
    const contactValidBadge = document.getElementById('contactValidBadge');

    contactInput.addEventListener('input', function(e) {
        // Enforce digits only, max 11 digits
        let val = e.target.value.replace(/\D/g, '').slice(0, 11);
        e.target.value = val;

        if (val.length === 11 && val.startsWith('09')) {
            contactValidBadge.classList.remove('hidden');
            contactStatusBadge.textContent = '✓ 11-Digit PH Mobile';
            contactStatusBadge.className = 'text-[11px] font-extrabold text-emerald-600';
        } else if (val.length > 0 && !val.startsWith('09')) {
            contactValidBadge.classList.add('hidden');
            contactStatusBadge.textContent = 'Must start with 09';
            contactStatusBadge.className = 'text-[11px] font-extrabold text-rose-600';
        } else if (val.length > 0) {
            contactValidBadge.classList.add('hidden');
            contactStatusBadge.textContent = `${val.length}/11 digits (starts with 09)`;
            contactStatusBadge.className = 'text-[11px] font-extrabold text-amber-600';
        } else {
            contactValidBadge.classList.add('hidden');
            contactStatusBadge.textContent = 'Format: 09XXXXXXXXX';
            contactStatusBadge.className = 'text-[11px] font-extrabold text-slate-400';
        }
    });

    /**
     * Address synchronization and preview
     */
    const brgyInput = document.getElementById('address_brgy');
    const cityInput = document.getElementById('address_city');
    const provInput = document.getElementById('address_province');
    const fullAddressInput = document.getElementById('full_address');
    const addressPreviewText = document.getElementById('addressPreviewText');

    function syncAddress() {
        const brgy = brgyInput.value.trim();
        const city = cityInput.value.trim();
        const prov = provInput.value.trim();

        if (brgy && city && prov) {
            const formatted = `${brgy}, ${city}, ${prov}`;
            fullAddressInput.value = formatted;
            addressPreviewText.textContent = formatted;
            addressPreviewText.className = 'font-black text-sky-800 ml-1';
        } else {
            fullAddressInput.value = '';
            addressPreviewText.textContent = 'Please fill in Barangay, Municipality/City, and Province';
            addressPreviewText.className = 'font-bold text-slate-500 ml-1';
        }
    }

    brgyInput.addEventListener('input', syncAddress);
    cityInput.addEventListener('input', syncAddress);
    provInput.addEventListener('input', syncAddress);

    /**
     * Real-time Password Strength Check
     */
    const passwordInput = document.getElementById('reg_password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const strengthBar = document.getElementById('strengthBar');
    const strengthLabel = document.getElementById('strengthLabel');
    const passwordMatchStatus = document.getElementById('passwordMatchStatus');

    function updateRuleUI(elemId, passed) {
        const elem = document.getElementById(elemId);
        const icon = elem.querySelector('.rule-icon');
        if (passed) {
            elem.className = 'flex items-center gap-1.5 text-emerald-400 font-bold transition-colors';
            icon.textContent = '✓';
            icon.className = 'rule-icon w-3.5 h-3.5 flex items-center justify-center rounded-full bg-emerald-950/80 border border-emerald-700 text-emerald-300 text-[10px] font-black';
        } else {
            elem.className = 'flex items-center gap-1.5 text-zinc-400 font-semibold transition-colors';
            icon.textContent = '○';
            icon.className = 'rule-icon w-3.5 h-3.5 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 text-[10px]';
        }
    }

    function checkPasswordStrength(pwd) {
        const hasLength = pwd.length >= 8;
        const hasUpper = /[A-Z]/.test(pwd);
        const hasLower = /[a-z]/.test(pwd);
        const hasNumber = /[0-9]/.test(pwd);
        const hasSpecial = /[^A-Za-z0-9]/.test(pwd);

        updateRuleUI('ruleLength', hasLength);
        updateRuleUI('ruleUpper', hasUpper);
        updateRuleUI('ruleLower', hasLower);
        updateRuleUI('ruleNumber', hasNumber);
        updateRuleUI('ruleSpecial', hasSpecial);

        let score = 0;
        if (hasLength) score++;
        if (hasUpper) score++;
        if (hasLower) score++;
        if (hasNumber) score++;
        if (hasSpecial) score++;

        if (pwd.length === 0) {
            strengthBar.style.width = '0%';
            strengthBar.className = 'h-full transition-all duration-300 rounded-full bg-zinc-700';
            strengthLabel.textContent = 'Not Entered';
            strengthLabel.className = 'text-[11px] font-extrabold text-zinc-400';
            return 0;
        }

        if (score <= 2) {
            strengthBar.style.width = '30%';
            strengthBar.className = 'h-full transition-all duration-300 rounded-full bg-rose-500';
            strengthLabel.textContent = 'Weak';
            strengthLabel.className = 'text-[11px] font-extrabold text-rose-400';
        } else if (score === 3 || score === 4) {
            strengthBar.style.width = score === 3 ? '60%' : '80%';
            strengthBar.className = 'h-full transition-all duration-300 rounded-full bg-amber-500';
            strengthLabel.textContent = score === 3 ? 'Fair' : 'Good';
            strengthLabel.className = 'text-[11px] font-extrabold text-amber-400';
        } else if (score === 5) {
            strengthBar.style.width = '100%';
            strengthBar.className = 'h-full transition-all duration-300 rounded-full bg-emerald-500';
            strengthLabel.textContent = 'Strong ✓';
            strengthLabel.className = 'text-[11px] font-extrabold text-emerald-400';
        }

        return score;
    }

    function checkPasswordMatch() {
        const pwd = passwordInput.value;
        const confirm = confirmPasswordInput.value;

        if (!confirm) {
            passwordMatchStatus.classList.add('hidden');
            return true;
        }

        passwordMatchStatus.classList.remove('hidden');
        if (pwd === confirm) {
            passwordMatchStatus.textContent = '✓ Passwords match';
            passwordMatchStatus.className = 'text-[11px] font-extrabold text-emerald-400 mt-1.5';
            return true;
        } else {
            passwordMatchStatus.textContent = '✗ Passwords do not match';
            passwordMatchStatus.className = 'text-[11px] font-extrabold text-rose-400 mt-1.5';
            return false;
        }
    }

    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    /**
     * Show Client Error Banner
     */
    function showClientError(title, message, focusElem) {
        const banner = document.getElementById('clientErrorBanner');
        const titleElem = document.getElementById('clientErrorTitle');
        const msgElem = document.getElementById('clientErrorMessage');

        titleElem.textContent = title;
        msgElem.textContent = message;
        banner.classList.remove('hidden');

        if (focusElem) {
            focusElem.focus();
            focusElem.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
            setTimeout(() => {
                focusElem.classList.remove('ring-2', 'ring-rose-200');
            }, 3000);
            banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    /**
     * Form Validation Trappings (Before Submit)
     */
    function validateRegisterForm(e) {
        const role = document.getElementById('roleInput').value || 'worker';
        const clientBanner = document.getElementById('clientErrorBanner');
        clientBanner.classList.add('hidden');

        // 1. Worker Trapping
        if (role === 'worker') {
            const firstName = document.getElementById('worker_first_name').value.trim();
            const lastName = document.getElementById('worker_last_name').value.trim();
            const sex = document.getElementById('worker_sex').value;

            if (!firstName) {
                e.preventDefault();
                showClientError('First Name Required', 'Please fill in your first name.', document.getElementById('worker_first_name'));
                return false;
            }
            if (!lastName) {
                e.preventDefault();
                showClientError('Last Name Required', 'Please fill in your last name.', document.getElementById('worker_last_name'));
                return false;
            }
            if (!sex) {
                e.preventDefault();
                showClientError('Sex Required', 'Please select your sex from the dropdown.', document.getElementById('worker_sex'));
                return false;
            }
        }

        // 2. Employer Trapping (Employer name, lastname, middle name)
        if (role === 'employer') {
            const firstName = document.getElementById('employer_first_name').value.trim();
            const middleName = document.getElementById('employer_middle_name').value.trim();
            const lastName = document.getElementById('employer_last_name').value.trim();
            const employerSex = document.getElementById('employer_sex').value;

            if (!firstName) {
                e.preventDefault();
                showClientError('Employer First Name Required', 'Please fill in the employer first name.', document.getElementById('employer_first_name'));
                return false;
            }
            if (!middleName) {
                e.preventDefault();
                showClientError('Employer Middle Name Required', 'Please fill in the employer middle name.', document.getElementById('employer_middle_name'));
                return false;
            }
            if (!lastName) {
                e.preventDefault();
                showClientError('Employer Last Name Required', 'Please fill in the employer last name.', document.getElementById('employer_last_name'));
                return false;
            }
            if (!employerSex) {
                e.preventDefault();
                showClientError('Employer Sex Required', 'Please select employer sex from the dropdown.', document.getElementById('employer_sex'));
                return false;
            }
        }

        // 3. Email Trapping
        const email = document.getElementById('reg_email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            e.preventDefault();
            showClientError('Email Required', 'Please fill in your email address.', document.getElementById('reg_email'));
            return false;
        }
        if (!emailRegex.test(email)) {
            e.preventDefault();
            showClientError('Invalid Email', 'Please enter a valid email address (e.g., name@example.com).', document.getElementById('reg_email'));
            return false;
        }

        // 4. Mobile Number Trapping (11 Digits PH)
        const contact = document.getElementById('contact_number').value.trim();
        if (!contact) {
            e.preventDefault();
            showClientError('Mobile Number Required', 'Please enter your 11-digit Philippine mobile number.', document.getElementById('contact_number'));
            return false;
        }
        if (contact.length !== 11 || !contact.startsWith('09')) {
            e.preventDefault();
            showClientError('Invalid Mobile Number', 'The mobile number must be an 11-digit Philippine number starting with 09 (e.g. 09123456789).', document.getElementById('contact_number'));
            return false;
        }

        // 5. Full Address Trapping (Barangay, Municipality/City, Province)
        const brgy = brgyInput.value.trim();
        const city = cityInput.value.trim();
        const prov = provInput.value.trim();

        if (!brgy) {
            e.preventDefault();
            showClientError('Barangay Required', 'Please provide your Barangay in the address fields.', brgyInput);
            return false;
        }
        if (!city) {
            e.preventDefault();
            showClientError('Municipality/City Required', 'Please provide your Municipality or City.', cityInput);
            return false;
        }
        if (!prov) {
            e.preventDefault();
            showClientError('Province Required', 'Please provide your Province.', provInput);
            return false;
        }
        syncAddress();

        // 6. Strong Password Trapping
        const pwd = passwordInput.value;
        const score = checkPasswordStrength(pwd);
        if (score < 5) {
            e.preventDefault();
            showClientError(
                'Strong Password Required',
                'Your password must contain at least 8 characters, an uppercase letter (A-Z), a lowercase letter (a-z), a number (0-9), and a special character (!@#$%^&*).',
                passwordInput
            );
            return false;
        }

        // 7. Password Match Trapping
        if (!checkPasswordMatch()) {
            e.preventDefault();
            showClientError('Password Mismatch', 'Your confirm password does not match the chosen password.', confirmPasswordInput);
            return false;
        }

        return true;
    }

    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }

    /**
     * Real Google accounts modal logic
     */
    function getDeviceGoogleAccounts() {
        const defaultAccounts = [
            { name: 'Johnley De Castro', email: 'javabrocode@gmail.com', role: 'Employer', color: 'bg-indigo-600' },
            { name: 'Akil Cyrylle Dangel', email: 'akilcyrylle@gmail.com', role: 'Worker', color: 'bg-sky-600' }
        ];

        try {
            const stored = localStorage.getItem('findjobph_real_google_accounts');
            if (stored) {
                const parsed = JSON.parse(stored);
                if (Array.isArray(parsed) && parsed.length > 0) return parsed;
            }
        } catch (e) {}

        localStorage.setItem('findjobph_real_google_accounts', JSON.stringify(defaultAccounts));
        return defaultAccounts;
    }

    function renderDeviceGoogleAccounts() {
        const accounts = getDeviceGoogleAccounts();
        const container = document.getElementById('deviceGoogleAccountsList');
        if (!container) return;

        container.innerHTML = accounts.map((acc) => {
            const initial = (acc.name || acc.email || 'G').charAt(0).toUpperCase();
            const color = acc.color || 'bg-sky-600';
            const safeEmail = acc.email.replace(/'/g, "\\'");
            const safeName = (acc.name || '').replace(/'/g, "\\'");
            return `
                <div class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-850 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all"
                     onclick="selectRealGoogleAccount('${safeEmail}', '${safeName}')">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full ${color} text-white flex items-center justify-center font-black text-xs shadow-sm flex-shrink-0">
                            ${initial}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">${acc.name}</p>
                            <p class="text-[11px] text-zinc-400 truncate font-semibold">${acc.email}</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-zinc-300 group-hover:text-white group-hover:translate-x-0.5 transition-all">
                        &rarr;
                    </span>
                </div>
            `;
        }).join('');
    }

    function openRealGoogleModal() {
        renderDeviceGoogleAccounts();
        document.getElementById('realGoogleModal').classList.remove('hidden');
    }

    function closeRealGoogleModal() {
        document.getElementById('realGoogleModal').classList.add('hidden');
        toggleAddGoogleAccountForm(false);
    }

    function toggleAddGoogleAccountForm(show) {
        const form = document.getElementById('useAnotherAccountForm');
        const btn = document.getElementById('showUseAnotherAccountBtn');
        const willShow = typeof show === 'boolean' ? show : form.classList.contains('hidden');
        if (willShow) {
            form.classList.remove('hidden');
            btn.classList.add('hidden');
            document.getElementById('directGoogleEmail').focus();
        } else {
            form.classList.add('hidden');
            btn.classList.remove('hidden');
        }
    }

    function selectRealGoogleAccount(email, name) {
        const role = document.getElementById('roleInput').value || 'worker';
        document.getElementById('googleEmailInput').value = email;
        document.getElementById('googleNameInput').value = name;
        document.getElementById('googleRoleField').value = role;
        document.getElementById('googleAuthForm').submit();
    }

    function submitRealGoogleDirect() {
        const emailInput = document.getElementById('directGoogleEmail');
        const email = emailInput.value.trim();
        if (!email || !email.includes('@')) {
            alert('Please enter a valid Google email address.');
            emailInput.focus();
            return;
        }

        const name = email.split('@')[0];
        const accounts = getDeviceGoogleAccounts();
        if (!accounts.some(a => a.email.toLowerCase() === email.toLowerCase())) {
            accounts.push({
                name: name,
                email: email,
                role: 'Account',
                color: 'bg-emerald-600'
            });
            localStorage.setItem('findjobph_real_google_accounts', JSON.stringify(accounts));
        }

        selectRealGoogleAccount(email, name);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialRole = document.getElementById('roleInput').value || 'worker';
        switchRole(initialRole);
        syncAddress();
        if (contactInput.value) {
            contactInput.dispatchEvent(new Event('input'));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRealGoogleModal();
        }
    });
</script>
@endsection
