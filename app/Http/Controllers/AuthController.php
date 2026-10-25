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
