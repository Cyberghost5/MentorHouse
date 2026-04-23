<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\MentorProfileController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SessionRequestController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// ─── Public mentor discovery ───────────────────────────────────────────────
Volt::route('/mentors', 'pages.mentor.discovery')->name('mentors.index');

Route::get('/mentors/{username}', [MentorProfileController::class, 'show'])
    ->name('mentors.show');

// ─── Mentor-only: manage own profile ──────────────────────────────────────
Route::middleware(['auth', 'mentor'])->group(function () {
    Route::get('/mentor/profile/edit', [MentorProfileController::class, 'edit'])
        ->name('mentor.profile.edit');

    Route::put('/mentor/profile', [MentorProfileController::class, 'update'])
        ->name('mentor.profile.update');

    Route::view('mentor/dashboard', 'mentor.dashboard')->name('mentor.dashboard');
});

// ─── Mentee-only: submit session requests ─────────────────────────────────
Route::middleware(['auth', 'mentee'])->group(function () {
    Route::post('/mentors/{mentor}/request', [SessionRequestController::class, 'store'])
        ->name('session-requests.store');

    Route::view('mentee/dashboard', 'mentee.dashboard')->name('mentee.dashboard');
});

// ─── Shared auth: session request list, respond, cancel ───────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/session-requests', [SessionRequestController::class, 'index'])
        ->name('session-requests.index');

    Route::put('/session-requests/{sessionRequest}', [SessionRequestController::class, 'update'])
        ->name('session-requests.update');

    Route::patch('/session-requests/{sessionRequest}/cancel', [SessionRequestController::class, 'cancel'])
        ->name('session-requests.cancel');

    // Mentor marks session completed
    Route::patch('/session-requests/{sessionRequest}/complete', [SessionRequestController::class, 'complete'])
        ->name('session-requests.complete');

    // Reviews
    Route::post('/session-requests/{sessionRequest}/review', [ReviewController::class, 'store'])
        ->name('reviews.store');

    // Messaging
    Route::get('/messages', [MessagesController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessagesController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessagesController::class, 'store'])->name('messages.store');
});

// ─── Mentee-only: payments ────────────────────────────────────────────────
Route::middleware(['auth', 'mentee'])->group(function () {
    Route::get('/payments/{sessionRequest}/pay', [PaymentController::class, 'pay'])
        ->name('payments.pay');
    Route::get('/payments/{sessionRequest}/simulate', [PaymentController::class, 'simulate'])
        ->name('payments.simulate');
    Route::post('/payments/{sessionRequest}/simulate', [PaymentController::class, 'confirmSimulate'])
        ->name('payments.confirm-simulate');
    Route::get('/payments/callback', [PaymentController::class, 'callback'])
        ->name('payments.callback');
});

// ─── Payment webhook (no auth — called by gateway) ────────────────────────
Route::post('/payments/webhook', [PaymentController::class, 'webhook'])
    ->name('payments.webhook');

// ─── Admin panel ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',         [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users',    [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/suspend',  [AdminController::class, 'suspend'])->name('users.suspend');
    Route::patch('/users/{user}/activate', [AdminController::class, 'activate'])->name('users.activate');
    Route::get('/sessions', [AdminController::class, 'sessions'])->name('sessions');
    Route::get('/reviews',  [AdminController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/{review}', [AdminController::class, 'deleteReview'])->name('reviews.destroy');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

require __DIR__.'/auth.php';
