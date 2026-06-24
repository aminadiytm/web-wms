<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Demo',
                'email' => 'admin@gmail.com',
                'password' => 'Admin#123',
                'role' => 'ADMIN',
            ],
            [
                'name' => 'Staff Inbound Demo',
                'email' => 'staff.inbound@gmail.com',
                'password' => 'Password#123',
                'role' => 'STAFF_INBOUND',
            ],
            [
                'name' => 'Staff Outbound Demo',
                'email' => 'staff.outbound@gmail.com',
                'password' => 'Password#123',
                'role' => 'STAFF_OUTBOUND',
            ],
        ];

        foreach ($users as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => Hash::make($item['password']),
                ]
            );

            $user->syncRoles([$item['role']]);
        }
    }
}