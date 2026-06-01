<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $dokterRoleId = DB::table('roles')->where('name', 'dokter')->value('id');
        $petugasRoleId = DB::table('roles')->where('name', 'petugas')->value('id');

        if ($dokterRoleId && $petugasRoleId) {
            DB::table('users')
                ->where('role_id', $petugasRoleId)
                ->update(['role_id' => $dokterRoleId]);

            DB::table('roles')
                ->where('id', $petugasRoleId)
                ->delete();
        }
    }

    public function down(): void
    {
        $petugasRoleId = DB::table('roles')->where('name', 'petugas')->value('id');

        if (! $petugasRoleId) {
            $petugasRoleId = DB::table('roles')->insertGetId([
                'name' => 'petugas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $dokterRoleId = DB::table('roles')->where('name', 'dokter')->value('id');

        if ($dokterRoleId) {
            DB::table('users')
                ->where('username', 'petugas')
                ->update(['role_id' => $petugasRoleId]);
        }
    }
};
