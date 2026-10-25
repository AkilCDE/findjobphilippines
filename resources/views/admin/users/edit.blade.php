@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/80 shadow-xl">
        <div class="flex items-center gap-2 mb-1.5">
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-purple-800 hover:text-purple-950 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Users List
            </a>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
            Edit User Profile
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-1">
            Updating account details for <span class="text-purple-900 font-bold">{{ $user->display_name }}</span> ({{ $user->email }}).
        </p>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="glass-panel p-6 sm:p-10 rounded-3xl border border-white/80 shadow-2xl space-y-5">
        @csrf
        @method('PUT')

        <!-- Role Selector -->
        <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200">
            <label for="role" class="block text-xs font-black uppercase tracking-wider text-purple-950 mb-1">
                Account Role <span class="text-red-500">*</span>
            </label>
            <select name="role" id="role" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-bold">
                <option value="worker" {{ old('role', $user->role) == 'worker' ? 'selected' : '' }}>Worker</option>
                <option value="employer" {{ old('role', $user->role) == 'employer' ? 'selected' : '' }}>Employer</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Full Privileges)</option>
            </select>
        </div>

        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                Full Display Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    First Name
                </label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}"
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
            </div>
            <div>
                <label for="last_name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Last Name
                </label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
            </div>
        </div>

        <div>
            <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                Company / Organization Name (For Employers)
            </label>
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $user->company_name) }}"
                class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
            </div>

            <div>
                <label for="sex" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                    Sex
                </label>
                <select name="sex" id="sex" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
                    <option value="">Select Sex</option>
                    <option value="Male" {{ old('sex', $user->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('sex', $user->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('sex', $user->sex) == 'Other' ? 'selected' : '' }}>Other</option>
                    <option value="Prefer not to say" {{ old('sex', $user->sex) == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                </select>
            </div>
        </div>

        <div>
            <label for="contact_number" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                Contact Number
            </label>
            <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $user->contact_number) }}"
                class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
        </div>

        <div>
            <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                Full Address
            </label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                class="w-full px-4 py-2.5 rounded-xl glass-input text-sm font-semibold">
        </div>

        <!-- Form Actions -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-md transition-all">
                Save Changes
            </button>
        </div>

    </form>

</div>
@endsection
