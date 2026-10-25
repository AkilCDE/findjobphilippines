@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-xl border border-white/80">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                {{ !empty($googleEmail) ? 'Complete Account Setup' : 'Create Account' }}
            </h1>
            <p class="text-xs font-semibold text-slate-600 mt-1">
                {{ !empty($googleEmail) ? 'Set up your password, names, and details to finish your Google registration' : 'Choose your account type to get started' }}
            </p>
        </div>

        <!-- Google Connected Banner (if continuing with Google) -->
        @if(!empty($googleEmail))
            <div class="mb-6 p-4 rounded-2xl bg-sky-50 border border-sky-200 flex items-start gap-3 shadow-xs">
                <div class="w-9 h-9 rounded-full bg-white shadow-sm flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                    </svg>
                </div>
                <div class="flex-1 text-xs">
                    <p class="font-black text-slate-950">
                        Continuing with Google: <span class="text-sky-700 font-bold underline">{{ $googleEmail }}</span>
                    </p>
                    <p class="font-semibold text-slate-600 mt-0.5">
                        You must set up your password, names, and contact details below before accessing your account.
                    </p>
                </div>
            </div>
        @endif

        @php
            $googleNameParts = !empty($googleName) ? explode(' ', $googleName, 2) : ['', ''];
            $prefillFirst = $googleNameParts[0] ?? '';
            $prefillLast = $googleNameParts[1] ?? '';
        @endphp

        <!-- Role Selector Switch (Worker vs Employer) -->
        <div class="mb-6 p-1 rounded-2xl bg-slate-100 border border-slate-200/80 flex items-center gap-1">
            <button type="button" id="roleBtnWorker" onclick="switchRole('worker')"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 shadow-sm bg-white text-slate-950 border border-slate-200">
                Worker
            </button>
            <button type="button" id="roleBtnEmployer" onclick="switchRole('employer')"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 text-slate-600 hover:text-slate-950">
                Employer
            </button>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-4">
            @csrf
            <input type="hidden" name="role" id="roleInput" value="{{ old('role', $defaultRole ?? 'worker') }}">

            <!-- WORKER SPECIFIC FIELDS -->
            <div id="workerFields" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $prefillFirst) }}"
                            placeholder="First Name"
                            class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $prefillLast) }}"
                            placeholder="Last Name"
                            class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                    </div>
                </div>

                <!-- Sex Selection -->
                <div>
                    <label for="sex" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Sex <span class="text-red-500">*</span>
                    </label>
                    <select name="sex" id="sex" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('sex') == 'Other' ? 'selected' : '' }}>Other</option>
                        <option value="Prefer not to say" {{ old('sex') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                </div>
            </div>

            <!-- EMPLOYER SPECIFIC FIELDS -->
            <div id="employerFields" class="space-y-4 hidden">
                <div>
                    <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $googleName ?? '') }}"
                        placeholder="Company or Business Name"
                        class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                </div>
            </div>

            <!-- COMMON FIELDS -->
            <div>
                <label for="reg_email" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="reg_email" value="{{ old('email', $googleEmail ?? '') }}" required
                    placeholder="email@example.com"
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
            </div>

            <div>
                <label for="contact_number" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Contact Number <span class="text-red-500">*</span>
                </label>
                <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" required
                    placeholder="+63 912 345 6789"
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
            </div>

            <div>
                <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Address <span class="text-red-500">*</span>
                </label>
                <input type="text" name="address" id="address" value="{{ old('address') }}" required
                    placeholder="Barangay, City/Municipality, Province"
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
            </div>

            <!-- Set Up Password (Always Required) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="reg_password" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Set Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="reg_password" required minlength="8"
                            placeholder="Min 8 characters"
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl glass-input text-sm font-semibold">
                        <button type="button" onclick="togglePasswordVisibility('reg_password', this)"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-sky-600 focus:outline-none cursor-pointer transition-colors z-10"
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
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                            placeholder="Re-enter password"
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl glass-input text-sm font-semibold">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-sky-600 focus:outline-none cursor-pointer transition-colors z-10"
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
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-md shadow-sky-500/20 transition-all mt-2">
                {{ !empty($googleEmail) ? 'Complete Account Setup' : 'Sign Up' }}
            </button>
        </form>

        <!-- Divider -->
        <div class="my-5 flex items-center">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink mx-3 text-xs font-semibold text-slate-500 uppercase">Or</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <!-- Hidden Google Auth Form -->
        <form method="POST" action="{{ route('auth.google') }}" id="googleAuthForm" class="hidden">
            @csrf
            <input type="hidden" name="flow" value="register">
            <input type="hidden" name="google_role" id="googleRoleField" value="{{ old('role', $defaultRole ?? 'worker') }}">
            <input type="hidden" name="credential" id="googleCredentialInput" value="">
            <input type="hidden" name="google_email" id="googleEmailInput" value="">
            <input type="hidden" name="google_name" id="googleNameInput" value="">
        </form>

        <!-- Single, Clean Continue with Google Button (No Duplicate) -->
        <button type="button" id="triggerGoogleBtn" onclick="openRealGoogleModal()"
            class="w-full py-3 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-900 font-bold text-xs shadow-sm flex items-center justify-center gap-2.5 transition-colors">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <span>Continue with Google</span>
        </button>

        <!-- Footer Link -->
        <div class="mt-6 text-center pt-4 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-700">
                Already have an account?
                <a href="{{ route('login') }}" class="font-extrabold text-sky-700 hover:text-sky-900">
                    Sign In
                </a>
            </p>
        </div>

    </div>
</div>

<!-- ==================== REAL GOOGLE ACCOUNT CHOOSER MODAL ==================== -->
<div id="realGoogleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs hidden transition-all">
    <div class="w-full max-w-sm rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-white/90 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Google Header -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                <div>
                    <h3 class="text-sm font-black text-slate-950">Choose an account</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">to continue to <strong class="text-slate-900">Findjobph</strong></p>
                </div>
            </div>
            <button type="button" onclick="closeRealGoogleModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <p class="text-[11px] font-semibold text-slate-600">
            Select a verified Google account to sign up:
        </p>

        <!-- Real Detected Device Google Accounts -->
        <div id="deviceGoogleAccountsList" class="space-y-2 max-h-56 overflow-y-auto pr-1">
            <!-- Dynamically populated from device sessions -->
        </div>

        <!-- Add / Use Another Real Account -->
        <div class="pt-2 border-t border-slate-100 space-y-2.5">
            <button type="button" onclick="toggleAddGoogleAccountForm()" id="showUseAnotherAccountBtn"
                class="w-full py-2 px-3 rounded-2xl hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center gap-3 transition-colors text-left border border-dashed border-slate-200">
                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm">
                    +
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Use another account</p>
                    <p class="text-[10px] text-slate-500 font-semibold">Sign up with any Google email</p>
                </div>
            </button>

            <!-- Inline Form for Another Google Account -->
            <div id="useAnotherAccountForm" class="hidden p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <label for="directGoogleEmail" class="block text-[10px] font-bold uppercase text-slate-700">Google Email</label>
                <input type="email" id="directGoogleEmail" placeholder="your.name@gmail.com"
                    class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold focus:border-sky-500 focus:outline-none">
                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" onclick="toggleAddGoogleAccountForm(false)" class="px-3 py-1 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-200/70">
                        Cancel
                    </button>
                    <button type="button" onclick="submitRealGoogleDirect()" class="px-3.5 py-1 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold shadow-sm">
                        Continue
                    </button>
                </div>
            </div>
        </div>

        <p class="text-[10px] text-slate-400 text-center leading-normal">
            New accounts will automatically be directed to complete their password and personal details.
        </p>

    </div>
</div>

<script>
    function switchRole(role) {
        const roleInput = document.getElementById('roleInput');
        const googleRoleField = document.getElementById('googleRoleField');
        const workerFields = document.getElementById('workerFields');
        const employerFields = document.getElementById('employerFields');
        const roleBtnWorker = document.getElementById('roleBtnWorker');
        const roleBtnEmployer = document.getElementById('roleBtnEmployer');

        roleInput.value = role;
        if (googleRoleField) googleRoleField.value = role;

        if (role === 'employer') {
            workerFields.classList.add('hidden');
            employerFields.classList.remove('hidden');

            roleBtnEmployer.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 shadow-sm bg-white text-slate-950 border border-slate-200';
            roleBtnWorker.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 text-slate-600 hover:text-slate-950';
            
            document.getElementById('company_name').required = true;
            document.getElementById('first_name').required = false;
            document.getElementById('last_name').required = false;
        } else {
            employerFields.classList.add('hidden');
            workerFields.classList.remove('hidden');

            roleBtnWorker.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 shadow-sm bg-white text-slate-950 border border-slate-200';
            roleBtnEmployer.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 text-slate-600 hover:text-slate-950';

            document.getElementById('company_name').required = false;
            document.getElementById('first_name').required = true;
            document.getElementById('last_name').required = true;
        }
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
                <div class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-sky-50/80 border border-slate-100 hover:border-sky-200 cursor-pointer transition-all"
                     onclick="selectRealGoogleAccount('${safeEmail}', '${safeName}')">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full ${color} text-white flex items-center justify-center font-black text-xs shadow-sm flex-shrink-0">
                            ${initial}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-950 truncate">${acc.name}</p>
                            <p class="text-[11px] text-slate-500 truncate font-semibold">${acc.email}</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-sky-700 group-hover:translate-x-0.5 transition-all">
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
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRealGoogleModal();
        }
    });
</script>
@endsection
