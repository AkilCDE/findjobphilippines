<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile page.
     */
    public function show(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Parse address components (Barangay, City/Municipality, Province)
        $addressParts = array_map('trim', explode(',', (string) $user->address));
        $addressBrgy = $addressParts[0] ?? '';
        $addressCity = $addressParts[1] ?? '';
        $addressProvince = $addressParts[2] ?? '';

        // Worker specific data: QR profile details and live status
        $qrProfile = null;
        $qrPayload = null;
        $workerStats = null;

        if ($user->isWorker()) {
            $qrProfile = $user->getQrProfileData();
            $qrPayload = $user->getQrPayloadString();
            $workerStats = [
                'total_applications' => $user->applications()->count(),
                'active_jobs' => $user->applications()->where('status', 'hired')->count(),
                'pending_applications' => $user->applications()->where('status', 'pending')->count(),
            ];
        }

        // Employer specific data: job posts, applicants, hired count
        $employerStats = null;
        if ($user->isEmployer()) {
            $employerStats = [
                'total_posts' => $user->jobPosts()->count(),
                'active_posts' => $user->jobPosts()->where('status', 'open')->count(),
                'total_hired' => $user->jobPosts()->sum('workers_hired'),
            ];
        }

        return view('profile.show', compact(
            'user',
            'addressBrgy',
            'addressCity',
            'addressProvince',
            'qrProfile',
            'qrPayload',
            'workerStats',
            'employerStats'
        ));
    }

    /**
     * Update the authenticated user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Normalize Philippine contact number
        $rawContact = (string) $request->input('contact_number', '');
        $cleanedContact = preg_replace('/[^0-9]/', '', $rawContact);
        if (str_starts_with($cleanedContact, '639') && strlen($cleanedContact) === 12) {
            $cleanedContact = '0'.substr($cleanedContact, 2);
        }
        if (! empty($cleanedContact)) {
            $request->merge(['contact_number' => $cleanedContact]);
        }

        // Base validation rules
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'string', 'in:Male,Female,Other,Prefer not to say'],
            'contact_number' => ['required', 'regex:/^09\d{9}$/'],
            'address_brgy' => ['required', 'string', 'max:255'],
            'address_city' => ['required', 'string', 'max:255'],
            'address_province' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'confirmed'],
        ];

        if ($user->isEmployer()) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
        }

        $messages = [
            'first_name.required' => 'Your first name is required.',
            'last_name.required' => 'Your last name is required.',
            'sex.required' => 'Please select your sex.',
            'contact_number.required' => 'Philippine mobile number is required.',
            'contact_number.regex' => 'The mobile number must be an 11-digit Philippine number starting with 09 (e.g., 09123456789).',
            'address_brgy.required' => 'Barangay is required.',
            'address_city.required' => 'Municipality or City is required.',
            'address_province.required' => 'Province is required.',
            'company_name.required' => 'Company or Business name is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];

        $validated = $request->validate($rules, $messages);

        $address = trim($validated['address_brgy']).', '.trim($validated['address_city']).', '.trim($validated['address_province']);

        $personFullName = trim("{$validated['first_name']} ".(! empty($validated['middle_name']) ? "{$validated['middle_name']} " : '')."{$validated['last_name']}");

        $updateData = [
            'name' => $personFullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'sex' => $validated['sex'],
            'contact_number' => $validated['contact_number'],
            'address' => $address,
        ];

        if ($user->isEmployer()) {
            $updateData['company_name'] = ! empty($validated['company_name']) ? trim($validated['company_name']) : null;
        }

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('profile.show')->with('success', 'Your profile has been updated successfully.');
    }
}
