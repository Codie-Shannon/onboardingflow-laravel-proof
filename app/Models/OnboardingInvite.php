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
        'email_last_sent_at',
        'email_send_count',
        'email_provider',
        'email_last_error',
        'resubmitted_at',
        'resubmission_count',
        'last_follow_up_sent_at',
        'follow_up_send_count',
        'follow_up_last_error',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
        'email_last_sent_at' => 'datetime',
        'resubmitted_at' => 'datetime',
        'last_follow_up_sent_at' => 'datetime',
        'resubmission_count' => 'integer',
        'follow_up_send_count' => 'integer',
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

    public function reviewChecklistItems()
    {
        return $this->hasMany(ReviewChecklistItem::class, 'onboarding_invite_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function documentRequirements()
    {
        return $this->hasMany(DocumentRequirement::class, 'onboarding_invite_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function missingInfoFollowUps()
    {
        return $this->hasMany(MissingInfoFollowUp::class, 'onboarding_invite_id')
            ->latest();
    }
}