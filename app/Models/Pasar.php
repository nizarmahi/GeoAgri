<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Pasar extends Model
{
    protected $table      = 'pasar';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_scraper_pasar',
        'psr_nama',
        'kabkota_id',
        'psr_status',
        'latitude',
        'longitude',
        'is_virtual',
        'geom',
    ];

    protected $casts = [
        'is_virtual' => 'boolean',
        'latitude'   => 'float',
        'longitude'  => 'float',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function kabupatenKota(): BelongsTo
    {
        return $this->belongsTo(
            KabupatenKota::class,
            'kabkota_id',
            'id'
        );
    }

    /**
     * Shortcut: Pasar → Provinsi (melalui KabupatenKota)
     */
    public function provinsi(): HasOneThrough
    {
        return $this->hasOneThrough(
            Provinsi::class,
            KabupatenKota::class,
            'id',            // firstKey: kab_kota.id = pasar.kabkota_id
            'provinsi_id',   // secondKey: kab_kota.provinsi_id = provinsi.id_provinsi
            'kabkota_id',    // localKey: pasar.kabkota_id
            'id_provinsi'    // secondLocalKey: provinsi.id_provinsi
        );
    }

    public function komoditas(): HasMany
    {
        return $this->hasMany(
            Komoditas::class,
            'pasar_id',
            'id'
        );
    }

    public static function getTotalPasar($provinsiId = null): int
    {
        $query = self::query()
            ->with('provinsi:id_provinsi,nama'); // Eager load provinsi untuk filter

        if ($provinsiId) {
            $query->whereHas('provinsi', function ($q) use ($provinsiId) {
                $q->where('id_provinsi', $provinsiId);
            });
        }

        return $query->count();
    }
}
