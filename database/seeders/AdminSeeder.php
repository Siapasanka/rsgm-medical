<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin RSGM',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole?->id,
            ]
        );
    }
}
