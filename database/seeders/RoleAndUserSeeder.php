<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $dokterRole = Role::firstOrCreate(['name' => 'dokter']);
        $petugasRole = Role::firstOrCreate(['name' => 'petugas']);

        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Superadmin RSGM',
                'password' => Hash::make('superadmin12345'),
                'role_id' => $superadminRole->id,
            ]
        );

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin RSGM',
                'password' => Hash::make('admin12345'),
                'role_id' => $adminRole->id,
            ]
        );

        User::updateOrCreate(
            ['username' => 'dokter'],
            [
                'name' => 'Dokter RSGM',
                'password' => Hash::make('dokter12345'),
                'role_id' => $dokterRole->id,
            ]
        );

        User::updateOrCreate(
            ['username' => 'petugas'],
            [
                'name' => 'Petugas RSGM',
                'password' => Hash::make('petugas12345'),
                'role_id' => $petugasRole->id,
            ]
        );
    }
}
