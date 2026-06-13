<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingSubmission extends Model
{
    protected $fillable = [
        'onboarding_invite_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'organisation',
        'role',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];

    public function invite()
    {
        return $this->belongsTo(OnboardingInvite::class, 'onboarding_invite_id');
    }
}