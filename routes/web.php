<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes & Job Discovery
|--------------------------------------------------------------------------
*/
Route::get('/', [JobPostController::class, 'landing'])->name('home');

Route::get('/jobs', [JobPostController::class, 'index'])->name('jobs.index');
Route::get('/jobs/map', [JobPostController::class, 'map'])->name('jobs.map');
Route::get('/jobs/{jobPost}', [JobPostController::class, 'show'])->name('jobs.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/auth/google', [AuthController::class, 'googleAuth'])->name('auth.google');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Worker Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:worker'])->group(function () {
    Route::post('/jobs/{jobPost}/apply', [WorkerController::class, 'apply'])->name('worker.apply');
    Route::get('/worker/applications', [WorkerController::class, 'applications'])->name('worker.applications');
    Route::get('/worker/qr-pass', [WorkerController::class, 'qrPass'])->name('worker.qr-pass');
    Route::delete('/worker/applications/{application}', [WorkerController::class, 'cancelApplication'])->name('worker.applications.cancel');
    Route::post('/worker/applications/{application}/request-leave', [WorkerController::class, 'requestLeave'])->name('worker.applications.request-leave');
    Route::post('/worker/applications/{application}/cancel-leave', [WorkerController::class, 'cancelLeave'])->name('worker.applications.cancel-leave');
});

/*
|--------------------------------------------------------------------------
| Public QR Code Verification Route
|--------------------------------------------------------------------------
*/
Route::get('/qr/verify/{token}', [WorkerController::class, 'verifyQr'])->name('worker.qr.verify');

/*
|--------------------------------------------------------------------------
| Employer Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [EmployerController::class, 'dashboard'])->name('dashboard');
    Route::get('/scanner', [EmployerController::class, 'scanner'])->name('scanner');
    Route::post('/scan-worker-qr', [EmployerController::class, 'scanWorkerQr'])->name('scan-worker-qr');
    Route::get('/posts', [EmployerController::class, 'posts'])->name('posts.index');
    Route::get('/posts/create', [EmployerController::class, 'create'])->name('posts.create');
    Route::post('/posts', [EmployerController::class, 'store'])->name('posts.store');
    Route::get('/posts/{jobPost}/edit', [EmployerController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{jobPost}', [EmployerController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{jobPost}', [EmployerController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{jobPost}/archive', [EmployerController::class, 'archive'])->name('posts.archive');
    Route::post('/posts/{jobPost}/unarchive', [EmployerController::class, 'unarchive'])->name('posts.unarchive');

    Route::get('/posts/{jobPost}/applicants', [EmployerController::class, 'applicants'])->name('posts.applicants');
    Route::post('/posts/{jobPost}/applicants/{application}/hire', [EmployerController::class, 'hireApplicant'])->name('posts.applicants.hire');
    Route::post('/posts/{jobPost}/applicants/{application}/reject', [EmployerController::class, 'rejectApplicant'])->name('posts.applicants.reject');
    Route::post('/posts/{jobPost}/applicants/{application}/fire', [EmployerController::class, 'fireWorker'])->name('posts.applicants.fire');
    Route::post('/posts/{jobPost}/applicants/{application}/confirm-leave', [EmployerController::class, 'confirmLeave'])->name('posts.applicants.confirm-leave');
    Route::post('/posts/{jobPost}/applicants/{application}/reject-leave', [EmployerController::class, 'rejectLeave'])->name('posts.applicants.reject-leave');
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');

    Route::get('/posts', [AdminController::class, 'posts'])->name('posts.index');
    Route::post('/posts/{jobPost}/toggle-status', [AdminController::class, 'toggleStatus'])->name('posts.toggle-status');
    Route::delete('/posts/{jobPost}', [AdminController::class, 'deletePost'])->name('posts.destroy');
});

/*
|--------------------------------------------------------------------------
| User Profile & Account Management Routes (Worker & Employer)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Direct Messaging Routes (Worker <-> Employer)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
});
