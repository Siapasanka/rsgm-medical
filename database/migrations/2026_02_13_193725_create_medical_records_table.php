<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id')->unique();
            $table->foreignId('dokter_id')->constrained('users');
            $table->longText('anamnesis')->nullable();
            $table->longText('pemeriksaan_fisik')->nullable();
            $table->longText('diagnosis')->nullable();
            $table->longText('tindakan')->nullable();
            $table->longText('resep')->nullable();
            $table->longText('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};