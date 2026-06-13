<?php

use App\Http\Controllers\Admin\OnboardingInviteController;
use App\Http\Controllers\PublicOnboardingFormController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.onboarding.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/dashboard', [OnboardingInviteController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/invites', [OnboardingInviteController::class, 'index'])
            ->name('invites.index');

        Route::get('/invites/create', [OnboardingInviteController::class, 'create'])
            ->name('invites.create');

        Route::post('/invites', [OnboardingInviteController::class, 'store'])
            ->name('invites.store');

        Route::get('/invites/{invite}', [OnboardingInviteController::class, 'show'])
            ->name('invites.show');

        Route::post('/invites/{invite}/status', [OnboardingInviteController::class, 'updateStatus'])
            ->name('invites.update-status');

        Route::post('/invites/{invite}/notes', [OnboardingInviteController::class, 'storeNote'])
            ->name('invites.notes.store');

        Route::get('/activity-log', [OnboardingInviteController::class, 'activityLog'])
            ->name('activity-log.index');
    });
});

Route::get('/onboarding/{token}', [PublicOnboardingFormController::class, 'show'])
    ->name('public.onboarding.show');

Route::post('/onboarding/{token}', [PublicOnboardingFormController::class, 'store'])
    ->name('public.onboarding.store');