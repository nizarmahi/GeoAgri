<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScrapingLog;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function status()
    {
        $totalHariIni = ScrapingLog::whereDate('created_at', today())->count();
        $totalSuccess = ScrapingLog::whereDate('created_at', today())->where('status', 'success')->count();
        $totalFailed  = ScrapingLog::whereDate('created_at', today())->where('status', 'failed')->count();
        $totalRunning = ScrapingLog::where('status', 'running')->count();
        $lastScraping = ScrapingLog::whereNotNull('finished_at')->latest('finished_at')->first();

        $recentLogs = ScrapingLog::with('provinsi:id_provinsi,nama')
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'workflow_name' => $log->workflow_name,
                    'provinsi' => $log->provinsi?->nama ?? '-',
                    'status' => $log->status,
                    'total_data' => $log->total_data,
                    'started_at' => $log->started_at?->toIso8601String(),
                    'finished_at' => $log->finished_at?->toIso8601String(),
                    'duration_seconds' => $log->duration_seconds,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_hari_ini' => $totalHariIni,
                'total_success' => $totalSuccess,
                'total_failed' => $totalFailed,
                'total_running' => $totalRunning,
                'last_scraping' => $lastScraping?->finished_at?->toIso8601String(),
                'recent_logs' => $recentLogs,
            ],
        ]);
    }

    public function triggerScraping(Request $request)
    {
        $request->validate([
            'provinsi_id' => 'nullable|integer',
        ]);

        $runningCount = ScrapingLog::where('status', 'running')->count();
        if ($runningCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Scraping workflow sedang berjalan. Tunggu hingga selesai.',
            ], 409);
        }

        $webhookUrl = env('N8N_SCRAPING_WEBHOOK_URL');

        if (!$webhookUrl) {
            return response()->json([
                'status' => 'error',
                'message' => 'Webhook URL belum dikonfigurasi. Set env N8N_SCRAPING_WEBHOOK_URL.',
            ], 500);
        }

        $payload = [];
        if ($request->filled('provinsi_id')) {
            $payload['provinsi_id'] = $request->provinsi_id;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->post($webhookUrl, $payload);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Scraping workflow berhasil ditrigger.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi webhook: ' . $response->body(),
            ], 502);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi webhook: ' . $e->getMessage(),
            ], 502);
        }
    }
}
