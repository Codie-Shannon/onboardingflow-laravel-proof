<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingInvite extends Model
{
    protected $fillable = [
        'onboarding_template_id',
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

    public function template()
    {
        return $this->belongsTo(OnboardingTemplate::class, 'onboarding_template_id');
    }

    public function submission()
    {
        return $this->hasOne(OnboardingSubmission::class);
    }

    public function missingInfoItems()
    {
        return $this->hasMany(MissingInfoItem::class);
    }

    public function unresolvedMissingInfoItems()
    {
        return $this->hasMany(MissingInfoItem::class)->where('resolved', false);
    }

    public function notes()
    {
        return $this->hasMany(OnboardingNote::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}