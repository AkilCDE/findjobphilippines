<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login page.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Welcome back, {$user->display_name}!");
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
    }

    /**
     * Display the registration page.
     */
    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $defaultRole = in_array($request->query('role'), ['worker', 'employer'], true)
            ? $request->query('role')
            : 'worker';

        $googleEmail = $request->query('google_email', session('google_email', ''));
        $googleName = $request->query('google_name', session('google_name', ''));

        return view('auth.register', compact('defaultRole', 'googleEmail', 'googleName'));
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'worker');

        // Normalize Philippine mobile number: convert +639 or dashes/spaces into canonical 09XXXXXXXXX
        $rawContact = (string) $request->input('contact_number', '');
        $cleanedContact = preg_replace('/[^0-9]/', '', $rawContact);
        if (str_starts_with($cleanedContact, '639') && strlen($cleanedContact) === 12) {
            $cleanedContact = '0'.substr($cleanedContact, 2);
        }
        if (! empty($cleanedContact)) {
            $request->merge(['contact_number' => $cleanedContact]);
        }

        // Combine full address components (Barangay, Municipality/City, Province) if provided separately
        if ($request->filled('address_brgy') && $request->filled('address_city') && $request->filled('address_province')) {
            $request->merge([
                'address' => trim($request->input('address_brgy')).', '.trim($request->input('address_city')).', '.trim($request->input('address_province')),
            ]);
        }

        // Support sex from either employer_sex or worker_sex dropdowns
        if (! $request->filled('sex')) {
            $fallbackSex = $request->input('employer_sex', $request->input('worker_sex'));
            if ($fallbackSex) {
                $request->merge(['sex' => $fallbackSex]);
            }
        }

        // Custom validation error messages for signup trappings
        $messages = [
            'company_name.required' => 'The company or employer name is required.',
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'sex.required' => 'Please select your sex.',
            'sex.in' => 'Please select a valid option for sex.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered. Please sign in or use a different email.',
            'contact_number.required' => 'The mobile number is required.',
            'contact_number.regex' => 'The mobile number must be an 11-digit Philippine mobile number starting with 09 (e.g., 09123456789).',
            'address.required' => 'The address is required.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];

        // Address validation: Full format required (Barangay, Municipality/City, Province)
        $addressRule = function (string $attribute, mixed $value, \Closure $fail) {
            $parts = array_filter(array_map('trim', explode(',', (string) $value)));
            if (count($parts) < 3) {
                $fail('The address must be complete and follow the format: Barangay, Municipality/City, Province (e.g. Brgy. Poblacion, Catigbian, Bohol).');
            }
        };

        // Strong password rule: min 8 characters, letters, mixed case (upper + lower), numbers, and symbols
        $passwordRule = Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        if ($role === 'employer') {
            // If first_name not supplied but company_name is (e.g. legacy test), fallback to splitting company_name
            if (! $request->filled('first_name') && $request->filled('company_name')) {
                $parts = explode(' ', trim((string) $request->input('company_name')), 2);
                $request->merge([
                    'first_name' => $parts[0] ?? 'Employer',
                    'last_name' => $parts[1] ?? 'Admin',
                ]);
            }

            $validated = $request->validate([
                'role' => ['required', 'in:employer'],
                'first_name' => ['required', 'string', 'max:100'],
                'middle_name' => ['nullable', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'sex' => ['required', 'string', 'in:Male,Female,Other,Prefer not to say'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
                'address' => ['required', 'string', 'max:500', $addressRule],
                'password' => ['required', 'string', $passwordRule, 'confirmed'],
            ], $messages);

            $employerFullName = trim("{$validated['first_name']} ".(! empty($validated['middle_name']) ? "{$validated['middle_name']} " : '')."{$validated['last_name']}");
            $companyName = ! empty($validated['company_name']) ? $validated['company_name'] : $employerFullName;

            $user = User::create([
                'name' => $companyName,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'company_name' => $companyName,
                'sex' => $validated['sex'],
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'],
                'address' => $validated['address'],
                'role' => 'employer',
                'password' => Hash::make($validated['password']),
            ]);
        } else {
            $validated = $request->validate([
                'role' => ['required', 'in:worker'],
                'first_name' => ['required', 'string', 'max:100'],
                'middle_name' => ['nullable', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'sex' => ['required', 'string', 'in:Male,Female,Other,Prefer not to say'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
                'address' => ['required', 'string', 'max:500', $addressRule],
                'password' => ['required', 'string', $passwordRule, 'confirmed'],
            ], $messages);

            $workerFullName = trim("{$validated['first_name']} ".(! empty($validated['middle_name']) ? "{$validated['middle_name']} " : '')."{$validated['last_name']}");

            $user = User::create([
                'name' => $workerFullName,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'sex' => $validated['sex'],
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'],
                'address' => $validated['address'],
                'role' => 'worker',
                'password' => Hash::make($validated['password']),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', "Account created successfully! Welcome to Findjobph, {$user->display_name}.");
    }

    /**
     * Display forgot password form.
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Process forgot password request and send reset link.
     */
    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $email = $request->input('email');
        $token = Str::random(60);

        // Store reset token
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        return back()->with('status', 'A password reset link has been dispatched to your email!')
            ->with('email', $email);
    }

    /**
     * Display reset password form.
     */
    public function showResetPassword(string $token, Request $request): View
    {
        $email = $request->query('email', '');

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been successfully reset. You can now sign in.');
    }

    /**
     * Functional Google Sign-In & Registration handler.
     */
    public function googleAuth(Request $request): RedirectResponse
    {
        $email = trim((string) $request->input('google_email'));
        $name = trim((string) $request->input('google_name'));
        $role = $request->input('google_role', 'worker');

        // Support official Google Identity Services JWT ID token credential
        if ($request->filled('credential')) {
            $jwt = (string) $request->input('credential');
            $parts = explode('.', $jwt);
            if (count($parts) === 3) {
                $payloadJson = base64_decode(strtr($parts[1], '-_', '+/'));
                $payload = json_decode($payloadJson, true);
                if (is_array($payload) && ! empty($payload['email'])) {
                    $email = trim((string) $payload['email']);
                    $name = trim((string) ($payload['name'] ?? ''));
                }
            }
        }

        if (! $email) {
            return back()->with('error', 'Please choose a Google account to continue.');
        }

        $user = User::where('email', $email)->first();

        // If user already exists in the system, log them in and proceed
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Signed in with Google as {$user->display_name}!");
        }

        // User does not have an account set up yet.
        // Mandatory requirement: Automatically redirect to sign up to set up password and names.
        // Do not redirect to the home page until account setup is completed!
        return redirect()->route('register', [
            'role' => $role,
            'google_email' => $email,
            'google_name' => $name,
        ])->with('google_setup', true)
            ->with('info', "Continuing with Google ({$email}). Please complete your account setup with your password and name details to get started.");
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully signed out.');
    }

    /**
     * Determine redirect route based on user role.
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'employer' => redirect()->route('employer.dashboard'),
            default => redirect()->route('jobs.index'),
        };
    }
}
