<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_email_unique');
                $table->renameColumn('email', 'username');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->unique('username');
            });
        }

        if (Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('email_verified_at');
            });
        }

        if (Schema::hasColumn('password_reset_tokens', 'email')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->renameColumn('email', 'username');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('password_reset_tokens', 'username')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->renameColumn('username', 'email');
            });
        }

        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_username_unique');
                $table->renameColumn('username', 'email');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->unique('email');
                $table->timestamp('email_verified_at')->nullable()->after('email');
            });
        }
    }
};
