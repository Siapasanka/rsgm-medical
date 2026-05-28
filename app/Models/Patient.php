<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_rm',
        'nik',
        'nama',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'gol_darah',
        'alergi',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}