<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komoditas extends Model
{
    protected $table      = 'komoditas';
    protected $primaryKey = 'id_komoditas';
    public    $timestamps = false;

    protected $fillable = [
        'tanggal',
        'pasar_id',
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

    public function pasar(): BelongsTo
    {
        return $this->belongsTo(
            Pasar::class,
            'pasar_id',
            'id_pasar'
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

    /**
     * Filter berdasarkan komoditas_id
     */
    public function scopeFilterKomoditas(Builder $query, int $id): Builder
    {
        return $query->where('komoditas_id', $id);
    }

    /**
     * Filter berdasarkan rentang tanggal
     */
    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) $query->whereDate('tanggal', '>=', $from);
        if ($to)   $query->whereDate('tanggal', '<=', $to);
        return $query;
    }

    /**
     * Filter berdasarkan provinsi melalui relasi pasar → kabupaten_kota
     */
    public function scopeFilterProvinsi(Builder $query, int $provinsiId): Builder
    {
        return $query->whereHas('pasar.kabupatenKota', function ($q) use ($provinsiId) {
            $q->where('provinsi_id', $provinsiId);
        });
    }

    /**
     * Hanya data dengan harga valid (tidak NULL dan > 0)
     */
    public function scopeValidHarga(Builder $query): Builder
    {
        return $query->whereNotNull('harga')->where('harga', '>', 0);
    }
}
