<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_expiry_days',
        'required_fields',
        'required_documents',
        'review_checklist',
        'is_active',
    ];

    protected $casts = [
        'required_fields' => 'array',
        'required_documents' => 'array',
        'review_checklist' => 'array',
        'is_active' => 'boolean',
    ];
}