<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\MasterKomoditas;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NullAnalysisController extends Controller
{
    /**
     * GET /api/komoditas/null-stats
     * Overall null analysis KPIs.
     */
    public function stats(): JsonResponse
    {
        $totalRecords = Komoditas::count();
        $totalNull = Komoditas::whereNull('harga')->orWhere('harga', '<=', 0)->count();
        $totalValid = $totalRecords - $totalNull;
        $completionRate = $totalRecords > 0 ? round($totalValid / $totalRecords * 100, 1) : 0;

        $latestDate = Komoditas::max('tanggal');
        $lastScanTime = $latestDate ? Carbon::parse($latestDate)->format('d/m/Y H:i') : '-';

        $stabilityLabel = $completionRate >= 85 ? 'Solid' : ($completionRate >= 60 ? 'Stabil' : 'Kritis');
        $stabilitySources = MasterKomoditas::count();
        $stabilityDesc = $stabilitySources . '/' . max($stabilitySources, 10) . ' sumber stabil';

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_records'    => $totalRecords,
                'total_null'       => $totalNull,
                'total_valid'      => $totalValid,
                'completion_rate'  => $completionRate,
                'last_scan_time'   => $lastScanTime,
                'stability_label'  => $stabilityLabel,
                'stability_desc'   => $stabilityDesc,
            ],
        ]);
    }

    /**
     * GET /api/komoditas/null-timeline
     * Null percentage per day for the timeline chart.
     *
     * Query params:
     *   days (optional, default: 7)
     */
    public function timeline(Request $request): JsonResponse
    {
        $days = (int) ($request->input('days', 7));

        $nullPerDate = Komoditas::select(
            'tanggal',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END) as null_count')
        )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->take($days)
            ->get()
            ->reverse()
            ->values();

        $data = $nullPerDate->map(function ($item) {
            $pct = $item->total > 0 ? round($item->null_count / $item->total * 100, 1) : 0;
            $stability = $item->total > 0 ? round(($item->total - $item->null_count) / $item->total * 100, 1) : 0;
            return [
                'tanggal'       => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'label'         => Carbon::parse($item->tanggal)->format('d/m'),
                'total'         => (int) $item->total,
                'null_count'    => (int) $item->null_count,
                'null_pct'      => $pct,
                'stability'     => $stability,
            ];
        });

        return response()->json([
            'status' => 'success',
            'meta'   => ['days' => $days],
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/komoditas/null-by-pasar
     * Null percentage per market (paginated).
     *
     * Query params:
     *   per_page (optional, default: 5)
     *   page     (optional)
     */
    public function byPasar(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 5);

        $pasarNull = Komoditas::select(
            'pasar_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END) as null_count')
        )
            ->with('pasar:id,psr_nama,kabkota_id')
            ->groupBy('pasar_id')
            ->orderByDesc(DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END)'))
            ->paginate($perPage)
            ->through(function ($item) {
                $pct = $item->total > 0 ? round($item->null_count / $item->total * 100, 1) : 0;
                $color = $pct > 15 ? '#dc2626' : ($pct > 8 ? '#ea580c' : ($pct > 5 ? '#2d3bde' : ($pct > 2 ? '#7c3aed' : '#16a34a')));
                return [
                    'nama'  => $item->pasar?->psr_nama ?? 'Pasar #' . $item->pasar_id,
                    'pct'   => $pct,
                    'color' => $color,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $pasarNull->items(),
            'meta'   => [
                'current_page' => $pasarNull->currentPage(),
                'last_page'    => $pasarNull->lastPage(),
                'per_page'     => $pasarNull->perPage(),
                'total'        => $pasarNull->total(),
            ],
        ]);
    }

    /**
     * GET /api/komoditas/null-batches
     * Top null batches (missing data).
     *
     * Query params:
     *   limit (optional, default: 4)
     */
    public function batches(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 4);

        $batches = Komoditas::select(
            'pasar_id',
            'tanggal',
            DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END) as null_count')
        )
            ->where(function ($q) {
                $q->whereNull('harga')->orWhere('harga', '<=', 0);
            })
            ->with('pasar:id,psr_nama,kabkota_id')
            ->groupBy('pasar_id', 'tanggal')
            ->orderBy('tanggal', 'desc')
            ->orderBy('null_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->pasar?->psr_nama ?? 'Sumber #' . $item->pasar_id,
                    'tgl'  => Carbon::parse($item->tanggal)->format('d F Y'),
                    'null' => (int) $item->null_count,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $batches,
        ]);
    }

    /**
     * GET /api/komoditas/null-by-komoditas
     * Null percentage per commodity (for donut chart).
     */
    public function byKomoditas(): JsonResponse
    {
        $nullPerKomoditas = Komoditas::select(
            'komoditas_master_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END) as null_count')
        )
            ->with('masterKomoditas:id_master_komoditas,nama')
            ->groupBy('komoditas_master_id')
            ->get()
            ->sortByDesc('null_count');

        $colors = ['#2d3bde', '#a5b4fc', '#e5e7eb', '#d1d5db', '#f97316', '#22c55e', '#ef4444', '#8b5cf6'];

        $items = $nullPerKomoditas->values()->map(function ($item, $i) use ($colors) {
            $pct = $item->total > 0 ? round($item->null_count / $item->total * 100, 1) : 0;
            return [
                'label' => $item->masterKomoditas?->nama ?? 'Unknown',
                'pct'   => $pct,
                'color' => $colors[$i % count($colors)],
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $items,
        ]);
    }
}
