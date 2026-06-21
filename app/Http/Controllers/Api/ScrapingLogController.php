<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScrapingLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScrapingLogController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workflow_name' => 'required|string|max:255',
            'provinsi_id'   => 'nullable|integer|exists:provinsi,id_provinsi',
            'status'        => 'required|in:success,failed,running',
            'total_pasar'        => 'nullable|integer|min:0',
            'total_data'         => 'nullable|integer|min:0',
            'total_insert'       => 'nullable|integer|min:0',
            'total_skip'         => 'nullable|integer|min:0',
            'total_insert_pasar' => 'nullable|integer|min:0',
            'total_gagal'        => 'nullable|integer|min:0',
            'failed_markets'     => 'nullable|json',
            'error_message'      => 'nullable|string',
            'started_at'         => 'nullable|date',
            'finished_at'        => 'nullable|date|after_or_equal:started_at',
        ]);

        $started  = $validated['started_at'] ?? null;
        $finished = $validated['finished_at'] ?? null;

        if ($started && $finished) {
            $validated['duration_seconds'] = Carbon::parse($started)->diffInSeconds(Carbon::parse($finished));
        } elseif ($started && !$finished) {
            $validated['duration_seconds'] = Carbon::parse($started)->diffInSeconds(now());
        }

        $log = ScrapingLog::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Scraping log saved',
            'data'    => $log,
        ], 201);
    }
}
