<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'dokter_id',
        'anamnesis',
        'pemeriksaan_fisik',
        'diagnosis',
        'tindakan',
        'resep',
        'catatan',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(MedicalRecordPhoto::class);
    }
}