<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingInvite extends Model
{
    protected $fillable = [
        'recipient_name',
        'recipient_email',
        'role',
        'organisation',
        'token',
        'status',
        'expires_at',
        'submitted_at',
        'message',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->hasOne(OnboardingSubmission::class);
    }
}