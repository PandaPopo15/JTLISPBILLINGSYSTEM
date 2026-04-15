<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\InstallerController;

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
Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/sales', [DashboardController::class, 'sales'])->name('admin.sales');
    Route::post('/admin/sales/store', [DashboardController::class, 'storeSale'])->name('admin.sales.store');
    Route::post('/admin/sales/{sale}/delete', [DashboardController::class, 'deleteSale'])->name('admin.sales.delete');
    Route::post('/admin/expenses/store', [DashboardController::class, 'storeExpense'])->name('admin.expenses.store');
    Route::post('/admin/expenses/{expense}/delete', [DashboardController::class, 'deleteExpense'])->name('admin.expenses.delete');
});

// Landing page admin edit
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
});

// Admin routes
Route::middleware('admin')->group(function () {
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
    Route::post('/admin/clients/{client}/mark-paid', [DashboardController::class, 'markPaymentPaid'])->name('admin.mark-payment-paid');
    Route::post('/admin/payments/{payment}/mark-paid', [DashboardController::class, 'markPaymentPaidById'])->name('admin.payment.mark-paid');
    Route::get('/admin/plans', [PlanController::class, 'index'])->name('admin.plans');
    Route::get('/admin/plans/create', [PlanController::class, 'create'])->name('admin.plans.create');
    Route::post('/admin/plans', [PlanController::class, 'store'])->name('admin.plans.store');
    Route::get('/admin/plans/{plan}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
    Route::post('/admin/plans/{plan}/update', [PlanController::class, 'update'])->name('admin.plans.update');
    Route::post('/admin/plans/{plan}/delete', [PlanController::class, 'destroy'])->name('admin.plans.delete');
    Route::get('/admin/landing', [LandingController::class, 'edit'])->name('admin.landing');
    Route::post('/admin/landing/update', [LandingController::class, 'update'])->name('admin.landing.update');
    
    // Installer management
    Route::get('/admin/installers', [InstallerController::class, 'index'])->name('admin.installers');
    Route::post('/admin/installers/store', [InstallerController::class, 'store'])->name('admin.installers.store');
    Route::get('/admin/installers/{installer}/edit', [InstallerController::class, 'edit'])->name('admin.installers.edit');
    Route::post('/admin/installers/{installer}/update', [InstallerController::class, 'update'])->name('admin.installers.update');
    Route::post('/admin/installers/{installer}/delete', [InstallerController::class, 'destroy'])->name('admin.installers.delete');
    
    // Job orders management
    Route::get('/admin/job-orders', [InstallerController::class, 'jobOrders'])->name('admin.job-orders');
    Route::post('/admin/job-orders/store', [InstallerController::class, 'storeJobOrder'])->name('admin.job-orders.store');
    Route::post('/admin/job-orders/{jobOrder}/update', [InstallerController::class, 'updateJobOrder'])->name('admin.job-orders.update');
    Route::post('/admin/job-orders/{jobOrder}/delete', [InstallerController::class, 'deleteJobOrder'])->name('admin.job-orders.delete');
});

// Installer routes
Route::middleware('installer')->group(function () {
    Route::get('/installer/dashboard', [InstallerController::class, 'installerDashboard'])->name('installer.dashboard');
    Route::post('/installer/job-orders/{jobOrder}/update-status', [InstallerController::class, 'updateJobStatus'])->name('installer.job-orders.update-status');
    Route::get('/installer/profile', [InstallerController::class, 'profile'])->name('installer.profile');
    Route::post('/installer/profile/update', [InstallerController::class, 'updateProfile'])->name('installer.profile.update');
});
