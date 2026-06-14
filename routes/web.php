<?php

use App\Http\Controllers\Admin\OnboardingInviteController;
use App\Http\Controllers\Admin\OnboardingTemplateController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PublicOnboardingFormController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.onboarding.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,reviewer,readonly'])
    ->group(function () {
        Route::prefix('onboarding')
            ->name('onboarding.')
            ->group(function () {
                Route::get('/dashboard', [OnboardingInviteController::class, 'dashboard'])
                    ->name('dashboard');

                Route::get('/invites', [OnboardingInviteController::class, 'index'])
                    ->name('invites.index');

                Route::get('/invites/create', [OnboardingInviteController::class, 'create'])
                    ->name('invites.create')
                    ->middleware('role:admin');

                Route::post('/invites', [OnboardingInviteController::class, 'store'])
                    ->name('invites.store')
                    ->middleware('role:admin');

                Route::get('/invites/{invite}', [OnboardingInviteController::class, 'show'])
                    ->name('invites.show');

                Route::get('/invites/{invite}/email-preview', [OnboardingInviteController::class, 'previewInviteEmail'])
                    ->name('invites.email-preview');

                Route::post('/invites/{invite}/send-email', [OnboardingInviteController::class, 'sendInviteEmail'])
                    ->name('invites.send-email')
                    ->middleware('role:admin');

                Route::post('/invites/{invite}/review-checklist/{item}/toggle', [OnboardingInviteController::class, 'toggleReviewChecklistItem'])
                    ->name('invites.review-checklist.toggle')
                    ->middleware('role:admin,reviewer');

                Route::post('/invites/{invite}/document-requirements/{requirement}/status', [OnboardingInviteController::class, 'updateDocumentRequirementStatus'])
                    ->name('invites.document-requirements.update-status')
                    ->middleware('role:admin,reviewer');

                Route::post('/invites/{invite}/missing-info/{item}/follow-ups', [OnboardingInviteController::class, 'storeMissingInfoFollowUp'])
                    ->name('invites.missing-info.follow-ups.store')
                    ->middleware('role:admin,reviewer');

                Route::post('/invites/{invite}/missing-info/{item}/resolve', [OnboardingInviteController::class, 'resolveMissingInfoItem'])
                    ->name('invites.missing-info.resolve')
                    ->middleware('role:admin,reviewer');

                Route::post('/invites/{invite}/status', [OnboardingInviteController::class, 'updateStatus'])
                    ->name('invites.update-status')
                    ->middleware('role:admin,reviewer');

                Route::post('/invites/{invite}/notes', [OnboardingInviteController::class, 'storeNote'])
                    ->name('invites.notes.store')
                    ->middleware('role:admin,reviewer');

                Route::get('/activity-log', [OnboardingInviteController::class, 'activityLog'])
                    ->name('activity-log.index');

                Route::get('/reports', [OnboardingInviteController::class, 'reports'])
                    ->name('reports.index');

                Route::get('/invites/{invite}/needs-info-email-preview', [OnboardingInviteController::class, 'previewNeedsInfoEmail'])
                    ->name('invites.needs-info-email-preview');

                Route::post('/invites/{invite}/send-needs-info-email', [OnboardingInviteController::class, 'sendNeedsInfoEmail'])
                    ->name('invites.send-needs-info-email')
                    ->middleware('role:admin,reviewer');

                Route::get('/exports/submissions.csv', [OnboardingInviteController::class, 'exportSubmissionsCsv'])
                    ->name('exports.submissions')
                    ->middleware('role:admin,reviewer');

                Route::get('/templates', [OnboardingTemplateController::class, 'index'])
                    ->name('templates.index');

                Route::get('/templates/create', [OnboardingTemplateController::class, 'create'])
                    ->name('templates.create')
                    ->middleware('role:admin');

                Route::post('/templates', [OnboardingTemplateController::class, 'store'])
                    ->name('templates.store')
                    ->middleware('role:admin');

                Route::get('/templates/{template}', [OnboardingTemplateController::class, 'show'])
                    ->name('templates.show');
            });
    });

Route::get('/onboarding/{token}', [PublicOnboardingFormController::class, 'show'])
    ->name('public.onboarding.show');

Route::post('/onboarding/{token}', [PublicOnboardingFormController::class, 'store'])
    ->name('public.onboarding.store');