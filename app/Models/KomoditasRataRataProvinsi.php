<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomoditasRataRataProvinsi extends Model
{
    protected $table      = 'komoditas_rata-rata_provinsi';
    protected $primaryKey = 'id_komoditas_rata-rata_provinsi';
    public    $timestamps = false;

    protected $fillable = [
        'tanggal',
        'provinsi_id',
        'komoditas_id',
        'harga',
        'created_at',
    ];

    protected $casts = [
        'tanggal'    => 'date:Y-m-d',
        'harga'      => 'integer',
        'created_at' => 'datetime',
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

    // ── Scopes ────────────────────────────────────────────────

    public function scopeFilterKomoditas(Builder $query, int $id): Builder
    {
        return $query->where('komoditas_id', $id);
    }

    public function scopeFilterProvinsi(Builder $query, int $id): Builder
    {
        return $query->where('provinsi_id', $id);
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) $query->whereDate('tanggal', '>=', $from);
        if ($to)   $query->whereDate('tanggal', '<=', $to);
        return $query;
    }

    public function scopeValidHarga(Builder $query): Builder
    {
        return $query->whereNotNull('harga')->where('harga', '>', 0);
    }
}
