<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    protected $table      = 'provinsi';
    protected $primaryKey = 'id_provinsi';
    public    $timestamps = false;

    protected $fillable = [
        'nama',
        'keycode',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function kabupatenKota(): HasMany
    {
        return $this->hasMany(
            KabupatenKota::class,
            'provinsi_id',
            'id_provinsi'
        );
    }

    public function rataRataHarga(): HasMany
    {
        return $this->hasMany(
            KomoditasRataRataProvinsi::class,
            'provinsi_id',
            'id_provinsi'
        );
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(
            MappingKomoditasScraper::class,
            'provinsi_id',
            'id_provinsi'
        );
    }
}
