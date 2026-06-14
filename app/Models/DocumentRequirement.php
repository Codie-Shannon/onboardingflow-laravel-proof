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
        'sharepoint_drive_id',
        'sharepoint_item_id',
        'sharepoint_web_url',
        'uploaded_original_name',
        'uploaded_mime_type',
        'uploaded_size',
        'uploaded_at',
        'upload_error',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'uploaded_at' => 'datetime',
        'uploaded_size' => 'integer',
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

    public function hasUploadedFile(): bool
    {
        return filled($this->sharepoint_item_id) && filled($this->sharepoint_web_url);
    }

    public function uploadedFileSizeLabel(): string
    {
        if (! $this->uploaded_size) {
            return '-';
        }

        $bytes = (int) $this->uploaded_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, $index === 0 ? 0 : 1) . ' ' . $units[$index];
    }

    public function invite()
    {
        return $this->belongsTo(OnboardingInvite::class, 'onboarding_invite_id');
    }
}
