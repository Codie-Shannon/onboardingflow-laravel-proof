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

    public static function statuses(): array
    {
        return [
            'sent' => 'Sent',
            'started' => 'Started',
            'submitted' => 'Submitted',
            'in_review' => 'In Review',
            'needs_info' => 'Needs Info',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }
}