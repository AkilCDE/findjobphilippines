@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl shadow-2xl border border-white/80">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-sky-600 to-sky-400 flex items-center justify-center text-white shadow-lg shadow-sky-500/25">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">
                Create an Account
            </h1>
            <p class="text-sm font-semibold text-slate-700 mt-1">
                Join thousands of Philippine workers and employers
            </p>
        </div>

        <!-- Role Selector Switch (Worker vs Employer) -->
        <div class="mb-6 p-1.5 rounded-2xl bg-sky-100/70 border border-sky-200/80 flex items-center gap-1.5">
            <button type="button" id="roleBtnWorker" onclick="switchRole('worker')"
                class="flex-1 py-2.5 px-4 rounded-xl text-sm font-extrabold transition-all flex items-center justify-center gap-2 shadow-sm bg-white text-sky-900 border border-white/90">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                I am a Worker
            </button>
            <button type="button" id="roleBtnEmployer" onclick="switchRole('employer')"
                class="flex-1 py-2.5 px-4 rounded-xl text-sm font-extrabold transition-all flex items-center justify-center gap-2 text-slate-700 hover:text-sky-900">
                <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                I am an Employer
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
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                            placeholder="e.g. Juan"
                            class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                            placeholder="e.g. Dela Cruz"
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
                        Company or Business Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                        placeholder="e.g. Bohol Agro-Builders Corp."
                        class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                </div>
            </div>

            <!-- COMMON FIELDS -->
            <div>
                <label for="reg_email" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="reg_email" value="{{ old('email', request('google_email')) }}" required
                    placeholder="e.g. contact@example.com"
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
            </div>

            <div>
                <label for="contact_number" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Contact Number <span class="text-red-500">*</span>
                </label>
                <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" required
                    placeholder="e.g. +63 912 345 6789"
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
            </div>

            <div>
                <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Full Address <span class="text-red-500">*</span>
                </label>
                <input type="text" name="address" id="address" value="{{ old('address', 'Ambuan, Catigbian, Bohol') }}" required
                    placeholder="e.g. Ambuan, Catigbian, Bohol, Philippines"
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                <p class="text-[11px] font-semibold text-slate-600 mt-1">
                    Include Barangay, Municipality/City, and Province.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="reg_password" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="reg_password" required minlength="8"
                        placeholder="At least 8 characters"
                        class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                        placeholder="Re-enter password"
                        class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-sm shadow-lg shadow-sky-500/25 transition-all hover:scale-[1.01] mt-2">
                Complete Registration
            </button>
        </form>

        <!-- Divider -->
        <div class="my-6 flex items-center">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink mx-4 text-xs font-bold text-slate-600 uppercase">Or continue with</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <!-- Functional Continue with Google Button -->
        <form method="POST" action="{{ route('auth.google') }}" id="googleRegisterForm">
            @csrf
            <input type="hidden" name="flow" value="register">
            <input type="hidden" name="google_role" id="googleRoleField" value="worker">
            <input type="hidden" name="google_email" id="googleRegisterEmail" value="">
            <input type="hidden" name="google_name" id="googleRegisterName" value="">
            <button type="button" onclick="promptGoogleRegister()"
                class="w-full py-3 px-4 rounded-xl border border-slate-300 bg-white/80 hover:bg-white text-slate-900 font-bold text-sm shadow-sm flex items-center justify-center gap-3 transition-all hover:border-slate-400">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
                Continue with Google
            </button>
        </form>

        <!-- Footer Link -->
        <div class="mt-6 text-center">
            <p class="text-xs font-semibold text-slate-800">
                Already have an account?
                <a href="{{ route('login') }}" class="font-extrabold text-sky-700 hover:text-sky-900 hover:underline">
                    Sign in here
                </a>
            </p>
        </div>

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
        googleRoleField.value = role;

        if (role === 'employer') {
            workerFields.classList.add('hidden');
            employerFields.classList.remove('hidden');

            roleBtnEmployer.className = 'flex-1 py-2.5 px-4 rounded-xl text-sm font-extrabold transition-all flex items-center justify-center gap-2 shadow-sm bg-white text-sky-900 border border-white/90';
            roleBtnWorker.className = 'flex-1 py-2.5 px-4 rounded-xl text-sm font-extrabold transition-all flex items-center justify-center gap-2 text-slate-700 hover:text-sky-900';
            
            document.getElementById('company_name').required = true;
            document.getElementById('first_name').required = false;
            document.getElementById('last_name').required = false;
        } else {
            employerFields.classList.add('hidden');
            workerFields.classList.remove('hidden');

            roleBtnWorker.className = 'flex-1 py-2.5 px-4 rounded-xl text-sm font-extrabold transition-all flex items-center justify-center gap-2 shadow-sm bg-white text-sky-900 border border-white/90';
            roleBtnEmployer.className = 'flex-1 py-2.5 px-4 rounded-xl text-sm font-extrabold transition-all flex items-center justify-center gap-2 text-slate-700 hover:text-sky-900';

            document.getElementById('company_name').required = false;
            document.getElementById('first_name').required = true;
            document.getElementById('last_name').required = true;
        }
    }

    // Initialize initial role if reloaded
    document.addEventListener('DOMContentLoaded', function() {
        const initialRole = document.getElementById('roleInput').value || 'worker';
        switchRole(initialRole);
    });

    function promptGoogleRegister() {
        const role = document.getElementById('roleInput').value;
        const email = prompt("Enter Google Account Email for registration:", "google.user@example.com");
        if (email) {
            const name = prompt("Enter your Name for Google profile:", "Google Juan");
            document.getElementById('googleRegisterEmail').value = email;
            document.getElementById('googleRegisterName').value = name || 'Google User';
            document.getElementById('googleRoleField').value = role;
            document.getElementById('googleRegisterForm').submit();
        }
    }
</script>
@endsection
