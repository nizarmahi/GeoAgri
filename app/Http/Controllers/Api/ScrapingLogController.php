<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScrapingLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScrapingLogController extends Controller
{
    /**
     * GET /api/scraping/logs
     *
     * Paginated list of scraping logs with filter params.
     *
     * Query params:
     *   status      (optional) success|failed|running
     *   provinsi_id (optional)
     *   tanggal     (optional, Y-m-d)
     *   per_page    (optional, default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = ScrapingLog::query()->with('provinsi:id_provinsi,nama');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->provinsi_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $perPage = (int) $request->input('per_page', 15);
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $totalHariIni = ScrapingLog::whereDate('created_at', today())->count();
        $totalSuccess = ScrapingLog::whereDate('created_at', today())->where('status', 'success')->count();
        $totalFailed  = ScrapingLog::whereDate('created_at', today())->where('status', 'failed')->count();
        $lastScraping = ScrapingLog::whereNotNull('finished_at')->max('finished_at');

        $provinsiList = \App\Models\Provinsi::orderBy('nama')->get(['id_provinsi', 'nama']);

        return response()->json([
            'status' => 'success',
            'data'   => $logs->items(),
            'meta'   => [
                'current_page'  => $logs->currentPage(),
                'last_page'     => $logs->lastPage(),
                'per_page'      => $logs->perPage(),
                'total'         => $logs->total(),
                'total_hari_ini' => $totalHariIni,
                'total_success'  => $totalSuccess,
                'total_failed'   => $totalFailed,
                'last_scraping'  => $lastScraping,
                'provinsi_list'  => $provinsiList,
            ],
        ]);
    }

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
