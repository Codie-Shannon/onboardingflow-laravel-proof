<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_REVIEWER = 'reviewer';
    public const ROLE_READONLY = 'readonly';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_REVIEWER => 'Reviewer',
            self::ROLE_READONLY => 'Read-only',
        ];
    }

    public function roleLabel(): string
    {
        return self::roles()[$this->role] ?? ucfirst($this->role);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isReviewer(): bool
    {
        return $this->role === self::ROLE_REVIEWER;
    }

    public function isReadOnly(): bool
    {
        return $this->role === self::ROLE_READONLY;
    }

    public function canReview(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_REVIEWER,
        ], true);
    }

    public function canManage(): bool
    {
        return $this->isAdmin();
    }
}