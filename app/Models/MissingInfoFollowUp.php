<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingInfoFollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'onboarding_invite_id',
        'missing_info_item_id',
        'message',
        'status',
        'requested_by',
        'requested_at',
        'due_at',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'due_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            'open' => 'Open',
            'resolved' => 'Resolved',
            'cancelled' => 'Cancelled',
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

    public function missingInfoItem()
    {
        return $this->belongsTo(MissingInfoItem::class, 'missing_info_item_id');
    }
}