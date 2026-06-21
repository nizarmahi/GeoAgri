<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapingLog extends Model
{
    protected $table = 'scraping_logs';

    protected $fillable = [
        'workflow_name',
        'provinsi_id',
        'status',
        'total_pasar',
        'total_data',
        'total_insert',
        'total_skip',
        'total_insert_pasar',
        'total_gagal',
        'failed_markets',
        'error_message',
        'started_at',
        'finished_at',
        'duration_seconds',
    ];

    protected $casts = [
        'started_at'         => 'datetime',
        'finished_at'        => 'datetime',
        'total_pasar'        => 'integer',
        'total_data'         => 'integer',
        'total_insert'       => 'integer',
        'total_skip'         => 'integer',
        'total_insert_pasar' => 'integer',
        'total_gagal'        => 'integer',
        'failed_markets'     => 'array',
        'duration_seconds'   => 'integer',
    ];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id_provinsi');
    }
}
