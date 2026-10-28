@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-4 sm:py-8 px-3 sm:px-6 lg:px-8 text-zinc-100">

    <!-- Page Header & Welcome Banner -->
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-800 pb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                    {{ $user->isWorker() ? 'bg-sky-950/80 text-sky-400 border border-sky-800/60' : 'bg-emerald-950/80 text-emerald-400 border border-emerald-800/60' }}">
                    {{ $user->role }} Account
                </span>
                <span class="text-xs text-zinc-500 font-bold">•</span>
                <span class="text-xs text-zinc-400 font-medium">Profile & Account Settings</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                {{ $user->isEmployer() ? ($user->company_name ?: $user->display_name) : $user->display_name }}
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400 mt-1">
                Manage your personal information, contact details, and account security.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            @if($user->isWorker())
                <a href="{{ route('worker.applications') }}"
                    class="px-3.5 py-2 rounded-xl border border-zinc-800 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    My Applications
                </a>
                <a href="{{ route('worker.qr-pass') }}"
                    class="px-3.5 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black text-xs font-extrabold shadow-md transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    Full Pass
                </a>
            @elseif($user->isEmployer())
                <a href="{{ route('employer.posts.create') }}"
                    class="px-3.5 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black text-xs font-extrabold shadow-md transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Post a Job
                </a>
                <a href="{{ route('employer.scanner') }}"
                    class="px-3.5 py-2 rounded-xl border border-zinc-800 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    Scan Worker QR
                </a>
            @endif
        </div>
    </div>

    <!-- Main Grid Content: Responsive 1 col on mobile, 3 cols on large screens -->
    <div class="grid grid-cols-1 {{ $user->isWorker() ? 'lg:grid-cols-3' : 'lg:grid-cols-3' }} gap-6 sm:gap-8">

        <!-- Column 1 & 2: Personal Information & Profile Edit Form -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Profile Overview Summary Card -->
            <div class="p-5 sm:p-6 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl relative overflow-hidden">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
                    <!-- Avatar Badge -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-2xl sm:text-3xl font-black text-white shadow-inner flex-shrink-0">
                        {{ strtoupper(substr($user->first_name ?: $user->name, 0, 1)) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl sm:text-2xl font-black text-white truncate">
                                {{ $user->display_name }}
                            </h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                                {{ $user->isWorker() ? 'bg-sky-950/80 text-sky-300 border border-sky-800/80' : 'bg-emerald-950/80 text-emerald-300 border border-emerald-800/80' }}">
                                {{ $user->role }}
                            </span>

                            @if($user->isWorker() && isset($qrProfile))
                                @if($qrProfile['status'] === 'hired')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-950 text-emerald-300 border border-emerald-800">
                                        ● Hired
                                    </span>
                                @elseif($qrProfile['status'] === 'fired')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-950 text-red-300 border border-red-800">
                                        ● Fired
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-950 text-amber-300 border border-amber-800">
                                        ● Pending
                                    </span>
                                @endif
                            @endif
                        </div>

                        <p class="text-xs sm:text-sm text-zinc-300 mt-1 break-all">
                            {{ $user->email }}
                        </p>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-400 mt-2 font-medium">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                {{ $user->contact_number ?: 'No contact number set' }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $user->address ?: 'Philippines' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Ribbon -->
                @if($user->isWorker() && $workerStats)
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 mt-6 pt-5 border-t border-zinc-800 text-center">
                        <div class="p-3 rounded-2xl bg-zinc-900/60 border border-zinc-800/80">
                            <span class="text-[10px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider block">Total Applied</span>
                            <span class="text-lg sm:text-xl font-black text-white mt-0.5 block">{{ $workerStats['total_applications'] }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-zinc-900/60 border border-zinc-800/80">
                            <span class="text-[10px] sm:text-xs font-bold text-emerald-400 uppercase tracking-wider block">Active Jobs</span>
                            <span class="text-lg sm:text-xl font-black text-emerald-300 mt-0.5 block">{{ $workerStats['active_jobs'] }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-zinc-900/60 border border-zinc-800/80">
                            <span class="text-[10px] sm:text-xs font-bold text-amber-400 uppercase tracking-wider block">Pending</span>
                            <span class="text-lg sm:text-xl font-black text-amber-300 mt-0.5 block">{{ $workerStats['pending_applications'] }}</span>
                        </div>
                    </div>
                @elseif($user->isEmployer() && $employerStats)
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 mt-6 pt-5 border-t border-zinc-800 text-center">
                        <div class="p-3 rounded-2xl bg-zinc-900/60 border border-zinc-800/80">
                            <span class="text-[10px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider block">Total Posts</span>
                            <span class="text-lg sm:text-xl font-black text-white mt-0.5 block">{{ $employerStats['total_posts'] }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-zinc-900/60 border border-zinc-800/80">
                            <span class="text-[10px] sm:text-xs font-bold text-emerald-400 uppercase tracking-wider block">Active Openings</span>
                            <span class="text-lg sm:text-xl font-black text-emerald-300 mt-0.5 block">{{ $employerStats['active_posts'] }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-zinc-900/60 border border-zinc-800/80">
                            <span class="text-[10px] sm:text-xs font-bold text-sky-400 uppercase tracking-wider block">Workers Hired</span>
                            <span class="text-lg sm:text-xl font-black text-sky-300 mt-0.5 block">{{ $employerStats['total_hired'] }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Profile Edit & Management Form Card -->
            <div class="p-5 sm:p-7 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-4 mb-6">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-white tracking-tight">
                            Edit Profile Information
                        </h2>
                        <p class="text-xs text-zinc-400 mt-0.5">
                            Keep your personal information and contact details accurate.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Employer Company Name (Only for Employer) -->
                    @if($user->isEmployer())
                        <div>
                            <label for="company_name" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                                Company / Business Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text"
                                name="company_name"
                                id="company_name"
                                value="{{ old('company_name', $user->company_name) }}"
                                required
                                class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                placeholder="e.g. Acme Bohol Construction & Supplies">
                            @error('company_name')
                                <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Names Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="first_name" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                                First Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text"
                                name="first_name"
                                id="first_name"
                                value="{{ old('first_name', $user->first_name) }}"
                                required
                                class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                placeholder="First Name">
                            @error('first_name')
                                <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="middle_name" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                                Middle Name <span class="text-zinc-500 font-normal">(Optional)</span>
                            </label>
                            <input type="text"
                                name="middle_name"
                                id="middle_name"
                                value="{{ old('middle_name', $user->middle_name) }}"
                                class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                placeholder="Middle Name">
                            @error('middle_name')
                                <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                                Last Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text"
                                name="last_name"
                                id="last_name"
                                value="{{ old('last_name', $user->last_name) }}"
                                required
                                class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                placeholder="Last Name">
                            @error('last_name')
                                <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Contact & Demographics Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="sex" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                                Sex <span class="text-red-400">*</span>
                            </label>
                            <select name="sex"
                                id="sex"
                                required
                                class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors">
                                <option value="" disabled>-- Select Sex --</option>
                                <option value="Male" {{ old('sex', $user->sex) === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex', $user->sex) === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('sex', $user->sex) === 'Other' ? 'selected' : '' }}>Other</option>
                                <option value="Prefer not to say" {{ old('sex', $user->sex) === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            @error('sex')
                                <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_number" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                                Philippine Mobile Number <span class="text-red-400">*</span>
                            </label>
                            <input type="tel"
                                name="contact_number"
                                id="contact_number"
                                value="{{ old('contact_number', $user->contact_number) }}"
                                required
                                maxlength="11"
                                class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                placeholder="09123456789">
                            <p class="text-[11px] text-zinc-500 mt-1">11-digit mobile number starting with 09.</p>
                            @error('contact_number')
                                <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address Components Breakdown -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                            Philippine Address <span class="text-red-400">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <input type="text"
                                    name="address_brgy"
                                    id="address_brgy"
                                    value="{{ old('address_brgy', $addressBrgy) }}"
                                    required
                                    class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                    placeholder="Barangay (e.g. Poblacion)">
                                @error('address_brgy')
                                    <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="text"
                                    name="address_city"
                                    id="address_city"
                                    value="{{ old('address_city', $addressCity) }}"
                                    required
                                    class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                    placeholder="Municipality / City">
                                @error('address_city')
                                    <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="text"
                                    name="address_province"
                                    id="address_province"
                                    value="{{ old('address_province', $addressProvince) }}"
                                    required
                                    class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                    placeholder="Province (e.g. Bohol)">
                                @error('address_province')
                                    <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Change Password (Optional) Accordion -->
                    <div class="pt-4 border-t border-zinc-800" x-data="{ changePass: false }">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-white block">Change Password</span>
                                <span class="text-[11px] text-zinc-400">Leave blank if you do not wish to update your password.</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="password" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5">
                                    New Password
                                </label>
                                <input type="password"
                                    name="password"
                                    id="password"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                    placeholder="••••••••">
                                @error('password')
                                    <p class="text-xs font-bold text-red-400 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5">
                                    Confirm New Password
                                </label>
                                <input type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-medium text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-colors"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-zinc-800 flex justify-end">
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white hover:bg-zinc-200 text-black font-black text-sm shadow-md transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Column 3: Role Specific Card (Worker QR Pass OR Employer Info & Quota) -->
        <div class="lg:col-span-1 space-y-6">

            @if($user->isWorker() && isset($qrProfile))
                <!-- WORKER: DIGITAL QR PASS (Live employment status & QR Code) -->
                <div id="workerProfileQrCard" class="p-6 rounded-3xl bg-[#121215] border-2 border-zinc-800 shadow-2xl relative overflow-hidden flex flex-col items-center text-center">
                    
                    <!-- Decorative Top Header -->
                    <div class="w-full flex items-center justify-between border-b border-zinc-800 pb-4 mb-4">
                        <div class="flex items-center gap-2 text-left">
                            <img src="{{ asset('favicon.svg') }}" alt="Findjobph Logo" class="w-8 h-8 rounded-full shadow shrink-0">
                            <div>
                                <p class="text-xs font-black text-white uppercase tracking-wider">Worker Pass</p>
                                <p class="text-[10px] text-zinc-400 font-bold uppercase">ID #{{ str_pad($qrProfile['worker_id'], 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($qrProfile['status'] === 'hired')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-600 text-white font-black text-[10px] uppercase tracking-wider shadow">
                                    Hired
                                </span>
                            @elseif($qrProfile['status'] === 'fired')
                                <span class="px-2.5 py-1 rounded-full bg-red-600 text-white font-black text-[10px] uppercase tracking-wider shadow">
                                    Fired
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-amber-600 text-white font-black text-[10px] uppercase tracking-wider shadow">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Live QR Code Element -->
                    <div class="p-3 bg-white rounded-2xl shadow-xl border border-zinc-200 my-2">
                        <div id="profileWorkerQr"></div>
                    </div>

                    <p class="text-xs font-bold text-white mt-3">
                        {{ $qrProfile['name'] }}
                    </p>
                    <p class="text-[11px] text-zinc-400 font-medium">
                        Scan with Employer Camera to Verify
                    </p>

                    <!-- Status Details Callout -->
                    <div class="w-full mt-4 p-3.5 rounded-2xl text-left border text-xs
                        @if($qrProfile['status'] === 'hired')
                            bg-emerald-950/40 border-emerald-800/80 text-emerald-100
                        @elseif($qrProfile['status'] === 'fired')
                            bg-red-950/40 border-red-800/80 text-red-100
                        @else
                            bg-amber-950/30 border-amber-800/80 text-amber-100
                        @endif">

                        @if($qrProfile['status'] === 'hired')
                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Currently Hired</p>
                            <p class="text-xs font-extrabold text-white mt-0.5">💼 {{ $qrProfile['work_title'] }}</p>
                            <p class="text-[11px] text-zinc-300 mt-0.5">🏢 {{ $qrProfile['employer_name'] }}</p>
                            @if(!empty($qrProfile['hired_at']))
                                <p class="text-[10px] text-zinc-400 mt-1">Since {{ $qrProfile['hired_at'] }}</p>
                            @endif
                        @elseif($qrProfile['status'] === 'fired')
                            <p class="text-[10px] font-bold uppercase tracking-wider text-red-400">Position Ended (Fired)</p>
                            <p class="text-xs font-extrabold text-white mt-0.5">💼 {{ $qrProfile['work_title'] }}</p>
                            <p class="text-[11px] text-zinc-300 mt-0.5">🏢 {{ $qrProfile['employer_name'] }}</p>
                            @if(!empty($qrProfile['ended_at']))
                                <p class="text-[10px] text-zinc-400 mt-1">Ended: {{ $qrProfile['ended_at'] }}</p>
                            @endif
                        @else
                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Status: Pending</p>
                            <p class="text-xs font-bold text-white mt-0.5">Available for Hire / Ready for Work</p>
                        @endif
                    </div>

                    <!-- Pass Actions: Download / Print -->
                    <div class="w-full grid grid-cols-2 gap-2 mt-4">
                        <button type="button" onclick="downloadProfileQr()"
                            class="px-3 py-2 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Save QR
                        </button>
                        <a href="{{ route('worker.qr-pass') }}"
                            class="px-3 py-2 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs flex items-center justify-center gap-1.5 transition-all shadow">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            Print Pass
                        </a>
                    </div>
                </div>

            @elseif($user->isEmployer())
                <!-- EMPLOYER: COMPANY & HIRING PORTFOLIO INFO (NO QR CODE as requested) -->
                <div class="space-y-6">
                    <div class="p-6 rounded-3xl bg-[#121215] border border-zinc-800 shadow-xl">
                        <div class="flex items-center gap-3 border-b border-zinc-800 pb-4 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-950/80 border border-emerald-800/80 flex items-center justify-center text-emerald-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-white">Employer Profile</h3>
                                <p class="text-xs text-zinc-400">Verified Direct Hiring Employer</p>
                            </div>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Registered Company</span>
                                <p class="text-sm font-bold text-white mt-0.5">{{ $user->company_name ?: 'Individual Employer' }}</p>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Authorized Representative</span>
                                <p class="font-bold text-white mt-0.5">{{ $user->first_name }} {{ $user->last_name }}</p>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Official Contact</span>
                                <p class="font-bold text-white mt-0.5">{{ $user->contact_number ?: 'Not provided' }}</p>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Primary Business Location</span>
                                <p class="font-bold text-white mt-0.5">{{ $user->address ?: 'Philippines' }}</p>
                            </div>
                        </div>

                        <!-- Quick Navigation for Employer -->
                        <div class="pt-5 mt-5 border-t border-zinc-800 space-y-2">
                            <a href="{{ route('employer.posts.create') }}"
                                class="w-full py-2.5 px-3 rounded-xl bg-white hover:bg-zinc-200 text-black font-extrabold text-xs flex items-center justify-center gap-2 shadow transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                Post a Job Opening
                            </a>
                            <a href="{{ route('employer.posts.index') }}"
                                class="w-full py-2.5 px-3 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                                <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                Manage My Job Posts
                            </a>
                            <a href="{{ route('employer.scanner') }}"
                                class="w-full py-2.5 px-3 rounded-xl border border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                Open Worker QR Scanner
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

@if($user->isWorker() && isset($qrPayload))
    @push('scripts')
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qrContainer = document.getElementById('profileWorkerQr');
            if (qrContainer && typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: @json($qrPayload),
                    width: 160,
                    height: 160,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        });

        function downloadProfileQr() {
            const img = document.querySelector('#profileWorkerQr img');
            const canvas = document.querySelector('#profileWorkerQr canvas');
            let dataUrl = null;
            if (img && img.src && img.src.startsWith('data:')) {
                dataUrl = img.src;
            } else if (canvas) {
                dataUrl = canvas.toDataURL('image/png');
            }
            if (dataUrl) {
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = 'worker-qr-{{ $user->id }}.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        }
    </script>
    @endpush
@endif
@endsection

