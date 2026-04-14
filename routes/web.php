<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\PasswordResetController;

Route::get('/', [LandingController::class, 'index']);

// Login routes
Route::get('/login', [LoginController::class, 'show'])->name('login.show');
Route::get('/adminlogin', [LandingController::class, 'adminLogin'])->name('admin.login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Registration routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

// Email verification routes
Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail'])->name('verify.resend');

// Password reset routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.send-reset-link');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

// Dashboard routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

// Landing page admin edit
Route::middleware('auth')->group(function () {
    Route::get('/admin/mikrotik', [MikrotikController::class, 'index'])->name('admin.mikrotik');
    Route::get('/admin/mikrotik/create', [MikrotikController::class, 'create'])->name('admin.mikrotik.create');
    Route::post('/admin/mikrotik', [MikrotikController::class, 'store'])->name('admin.mikrotik.store');
    Route::get('/admin/mikrotik/{mikrotik}/edit', [MikrotikController::class, 'edit'])->name('admin.mikrotik.edit');
    Route::post('/admin/mikrotik/{mikrotik}/update', [MikrotikController::class, 'update'])->name('admin.mikrotik.update');
    Route::post('/admin/mikrotik/{mikrotik}/delete', [MikrotikController::class, 'destroy'])->name('admin.mikrotik.delete');
    Route::post('/admin/mikrotik/{mikrotik}/test', [MikrotikController::class, 'test'])->name('admin.mikrotik.test');
    Route::post('/admin/mikrotik/{mikrotik}/assign-clients', [MikrotikController::class, 'assignClients'])->name('admin.mikrotik.assign');
    Route::get('/admin/settings', [DashboardController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings/update', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
    Route::post('/admin/settings/password', [DashboardController::class, 'updatePassword'])->name('admin.settings.password');
    Route::get('/admin/settings/mikrotik', [DashboardController::class, 'mikrotikSettings'])->name('admin.settings.mikrotik');
    Route::post('/admin/settings/mikrotik/save', [DashboardController::class, 'saveMikrotik'])->name('admin.settings.mikrotik.save');
    Route::post('/admin/settings/mikrotik/test', [DashboardController::class, 'testMikrotik'])->name('admin.settings.mikrotik.test');
    Route::get('/admin/clients', [DashboardController::class, 'clients'])->name('admin.clients');
    Route::post('/admin/clients/store', [DashboardController::class, 'storeClient'])->name('admin.clients.store');
    Route::get('/admin/clients/{client}/edit', [DashboardController::class, 'editClient'])->name('admin.clients.edit');
    Route::post('/admin/clients/{client}/update', [DashboardController::class, 'updateClient'])->name('admin.clients.update');
    Route::post('/admin/clients/{client}/accept', [DashboardController::class, 'acceptClient'])->name('admin.clients.accept');
    Route::post('/admin/clients/{client}/reject', [DashboardController::class, 'rejectClient'])->name('admin.clients.reject');
    Route::post('/admin/clients/{client}/activate', [DashboardController::class, 'activateClient'])->name('admin.clients.activate');
    Route::post('/admin/clients/{client}/verify', [DashboardController::class, 'verifyClient'])->name('admin.clients.verify');
    Route::post('/admin/clients/{client}/delete', [DashboardController::class, 'deleteClient'])->name('admin.clients.delete');
    Route::get('/admin/landing', [LandingController::class, 'edit'])->name('admin.landing');
    Route::post('/admin/landing/update', [LandingController::class, 'update'])->name('admin.landing.update');
    Route::get('/profile/edit', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
});
