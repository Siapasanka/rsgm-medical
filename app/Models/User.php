<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function createdRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'created_by');
    }

    public function medicalRecordsAsDoctor(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'dokter_id');
    }

    public function uploadedPhotos(): HasMany
    {
        return $this->hasMany(MedicalRecordPhoto::class, 'uploaded_by');
    }
}
