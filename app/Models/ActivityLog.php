<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'onboarding_invite_id',
        'actor_name',
        'action',
        'description',
    ];

    public function invite()
    {
        return $this->belongsTo(OnboardingInvite::class, 'onboarding_invite_id');
    }
}