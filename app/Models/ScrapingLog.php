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
        'error_message',
        'started_at',
        'finished_at',
        'duration_seconds',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id_provinsi');
    }
}
