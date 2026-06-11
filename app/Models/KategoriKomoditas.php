<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKomoditas extends Model
{
    protected $table      = 'kategori_komoditas';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'kategori',
    ];

    // ── Relasi ────────────────────────────────────────────────
    public function masterKomoditas(): HasMany
    {
        return $this->hasMany(
            MasterKomoditas::class,
            'kategori_id',
            'id'
        );
    }

    public function hargaPasar(): HasMany
    {
        return $this->hasMany(
            Komoditas::class,
            'kategori_id',
            'id'
        );
    }
}
