<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Demo Admin',
                'email' => 'admin@example.com',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'name' => 'Demo Reviewer',
                'email' => 'reviewer@example.com',
                'role' => User::ROLE_REVIEWER,
            ],
            [
                'name' => 'Demo Read Only',
                'email' => 'readonly@example.com',
                'role' => User::ROLE_READONLY,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}