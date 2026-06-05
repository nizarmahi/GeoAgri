<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KabupatenKota extends Model
{
    protected $table      = 'kabupaten_kota';
    protected $primaryKey = 'id_kabupaten_kota';
    public    $timestamps = false;

    protected $fillable = [
        'id_scraper_kab',
        'provinsi_id',
        'nama',
        'keycode',
        'latitude',
        'longitude',
        'batas_wilayah',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(
            Provinsi::class,
            'provinsi_id',
            'id_provinsi'
        );
    }

    public function pasar(): HasMany
    {
        return $this->hasMany(
            Pasar::class,
            'kabupaten_kota_id',
            'id_kabupaten_kota'
        );
    }
}
