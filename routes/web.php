<?php

use App\Http\Controllers\Admin\OnboardingInviteController;
use App\Http\Controllers\PublicOnboardingFormController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OnboardingTemplateController;

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

        Route::post('/invites/{invite}/review-checklist/{item}/toggle', [OnboardingInviteController::class, 'toggleReviewChecklistItem'])
            ->name('invites.review-checklist.toggle');

        Route::post('/invites/{invite}/document-requirements/{requirement}/status', [OnboardingInviteController::class, 'updateDocumentRequirementStatus'])
            ->name('invites.document-requirements.update-status');

        Route::post('/invites/{invite}/status', [OnboardingInviteController::class, 'updateStatus'])
            ->name('invites.update-status');

        Route::post('/invites/{invite}/notes', [OnboardingInviteController::class, 'storeNote'])
            ->name('invites.notes.store');

        Route::get('/activity-log', [OnboardingInviteController::class, 'activityLog'])
            ->name('activity-log.index');

        Route::get('/exports/submissions.csv', [OnboardingInviteController::class, 'exportSubmissionsCsv'])
            ->name('exports.submissions');

        Route::get('/templates', [OnboardingTemplateController::class, 'index'])
            ->name('templates.index');

        Route::get('/templates/create', [OnboardingTemplateController::class, 'create'])
            ->name('templates.create');

        Route::post('/templates', [OnboardingTemplateController::class, 'store'])
            ->name('templates.store');

        Route::get('/templates/{template}', [OnboardingTemplateController::class, 'show'])
            ->name('templates.show');
    });
});

Route::get('/onboarding/{token}', [PublicOnboardingFormController::class, 'show'])
    ->name('public.onboarding.show');

Route::post('/onboarding/{token}', [PublicOnboardingFormController::class, 'store'])
    ->name('public.onboarding.store');