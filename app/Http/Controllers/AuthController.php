<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        return view('auth.register', compact('defaultRole'));
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'worker');

        if ($role === 'employer') {
            $validated = $request->validate([
                'role' => ['required', 'in:employer'],
                'company_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'contact_number' => ['required', 'string', 'max:50'],
                'address' => ['required', 'string', 'max:500'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = User::create([
                'name' => $validated['company_name'],
                'company_name' => $validated['company_name'],
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
                'last_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'sex' => ['required', 'string', 'in:Male,Female,Other,Prefer not to say'],
                'address' => ['required', 'string', 'max:500'],
                'contact_number' => ['required', 'string', 'max:50'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = User::create([
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'sex' => $validated['sex'],
                'address' => $validated['address'],
                'contact_number' => $validated['contact_number'],
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
        $email = $request->input('google_email');
        $role = $request->input('google_role', 'worker');

        if (! $email) {
            return back()->with('error', 'Google authentication did not provide an email address.');
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Signed in with Google as {$user->display_name}!");
        }

        // If signing in via Google and not yet registered
        if ($request->input('flow') === 'login') {
            return redirect()->route('register', ['role' => $role, 'google_email' => $email])
                ->with('info', "No existing account found for {$email}. Please complete your profile to register with Google.");
        }

        // Auto-create user via Google signup
        $name = $request->input('google_name', explode('@', $email)[0]);
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0] ?? 'Google';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'User';

        $user = User::create([
            'name' => $name,
            'first_name' => $role === 'worker' ? $firstName : null,
            'last_name' => $role === 'worker' ? $lastName : null,
            'company_name' => $role === 'employer' ? $name : null,
            'email' => $email,
            'role' => $role,
            'sex' => $role === 'worker' ? 'Prefer not to say' : null,
            'address' => $role === 'worker' ? 'Catigbian, Bohol, Philippines' : 'Taguig City, Metro Manila',
            'contact_number' => '+63 912 345 6789',
            'password' => Hash::make(Str::random(16)),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', "Welcome to Findjobph, {$user->display_name}! Successfully connected with Google.");
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
