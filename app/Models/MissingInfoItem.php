<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissingInfoItem extends Model
{
    protected $fillable = [
        'onboarding_invite_id',
        'onboarding_submission_id',
        'field_key',
        'label',
        'description',
        'severity',
        'resolved',
        'resolved_at',
    ];

    protected $casts = [
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function invite()
    {
        return $this->belongsTo(OnboardingInvite::class, 'onboarding_invite_id');
    }

    public function submission()
    {
        return $this->belongsTo(OnboardingSubmission::class, 'onboarding_submission_id');
    }

    public function followUps()
    {
        return $this->hasMany(MissingInfoFollowUp::class, 'missing_info_item_id')
            ->latest();
    }
}