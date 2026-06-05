<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKomoditas extends Model
{
    protected $table      = 'master_komoditas';
    protected $primaryKey = 'id_master_komoditas';
    public    $timestamps = false;

    protected $fillable = [
        'nama',
        'satuan',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function rataRataProvinsi(): HasMany
    {
        return $this->hasMany(
            KomoditasRataRataProvinsi::class,
            'komoditas_id',
            'id_master_komoditas'
        );
    }

    public function hargaPasar(): HasMany
    {
        return $this->hasMany(
            Komoditas::class,
            'komoditas_id',
            'id_master_komoditas'
        );
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(
            MappingKomoditasScraper::class,
            'komoditas_master_id',
            'id_master_komoditas'
        );
    }
}
