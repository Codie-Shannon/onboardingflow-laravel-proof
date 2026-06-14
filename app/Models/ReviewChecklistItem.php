<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'onboarding_invite_id',
        'label',
        'description',
        'sort_order',
        'is_completed',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function invite()
    {
        return $this->belongsTo(OnboardingInvite::class, 'onboarding_invite_id');
    }
}