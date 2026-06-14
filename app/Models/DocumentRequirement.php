<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'onboarding_invite_id',
        'label',
        'description',
        'status',
        'sort_order',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            'missing' => 'Missing',
            'provided' => 'Provided',
            'reviewed' => 'Reviewed',
            'not_required' => 'Not Required',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function invite()
    {
        return $this->belongsTo(OnboardingInvite::class, 'onboarding_invite_id');
    }
}