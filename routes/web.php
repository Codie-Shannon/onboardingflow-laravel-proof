<?php

use App\Http\Controllers\Admin\OnboardingInviteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicOnboardingFormController;

Route::get('/', function () {
    return redirect()->route('admin.onboarding.invites.index');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/invites', [OnboardingInviteController::class, 'index'])->name('invites.index');
        Route::get('/invites/create', [OnboardingInviteController::class, 'create'])->name('invites.create');
        Route::post('/invites', [OnboardingInviteController::class, 'store'])->name('invites.store');
        Route::get('/invites/{invite}', [OnboardingInviteController::class, 'show'])->name('invites.show');
    });
});

// Placeholder route. We build the real public form next.
Route::get('/onboarding/{token}', [PublicOnboardingFormController::class, 'show'])
    ->name('public.onboarding.show');

Route::post('/onboarding/{token}', [PublicOnboardingFormController::class, 'store'])
    ->name('public.onboarding.store');