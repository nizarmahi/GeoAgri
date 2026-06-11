<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MappingKomoditasScraper extends Model
{
    protected $table      = 'mapping_komoditas_scraper';
    protected $primaryKey = 'id_mapping';
    public    $timestamps = false;

    protected $fillable = [
        'provinsi_id',
        'komoditas_master_id',
        'id_scraper_lokal',
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

    public function masterKomoditas(): BelongsTo
    {
        return $this->belongsTo(
            MasterKomoditas::class,
            'komoditas_id',
            'id_master_komoditas'
        );
    }
}
