<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingNote extends Model
{
    protected $fillable = [
        'onboarding_invite_id',
        'author_name',
        'note',
    ];

    public function invite()
    {
        return $this->belongsTo(OnboardingInvite::class, 'onboarding_invite_id');
    }
}