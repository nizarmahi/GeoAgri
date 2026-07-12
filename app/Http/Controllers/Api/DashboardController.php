<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\MasterKomoditas;
use App\Models\Pasar;
use App\Models\Provinsi;
use App\Models\KabupatenKota;
use App\Models\ScrapingLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $range = min((int) ($request->get('range', 30)), 90);

        $totalKomoditas = Cache::remember('dash:total_komoditas', 3600, fn() => MasterKomoditas::count());
        $totalProvinsi  = Cache::remember('dash:total_provinsi', 3600, fn() => Provinsi::count());
        $totalKabkota   = Cache::remember('dash:total_kabkota', 3600, fn() => KabupatenKota::count());
        $totalPasar     = Cache::remember('dash:total_pasar', 3600, fn() => Pasar::count());

        $lastScrape = ScrapingLog::where('status', 'success')
            ->orderBy('finished_at', 'desc')
            ->first(['workflow_name', 'provinsi_id', 'status', 'total_pasar', 'total_data', 'total_insert', 'started_at', 'finished_at', 'duration_seconds']);

        $trend = Cache::remember("dash:trend:{$range}", 600, function () use ($range) {
            return Komoditas::query()
                ->validHarga()
                ->where('tanggal', '>=', Carbon::today()->subDays($range))
                ->select(
                    'tanggal',
                    DB::raw('ROUND(AVG(harga)) as harga'),
                    DB::raw('COUNT(DISTINCT komoditas.pasar_id) as pasar_count')
                )
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get()
                ->map(fn($r) => [
                    'tanggal' => $r->tanggal->format('Y-m-d'),
                    'harga'   => (int) $r->harga,
                ]);
        });

        $today = Carbon::today();
        $weekAgo = Carbon::today()->subDays(7);

        $gainersLosers = Cache::remember("dash:gainers:{$today->toDateString()}", 600, function () use ($today, $weekAgo) {
            $latestDate = Komoditas::validHarga()->where('tanggal', '<=', $today)->max('tanggal');
            $prevDate   = Komoditas::validHarga()->where('tanggal', '<=', $weekAgo)->max('tanggal');

            if (!$latestDate || !$prevDate) return ['gainers' => [], 'losers' => []];

            $latestPrices = $this->avgPriceByKomoditas($latestDate);
            $prevPrices   = $this->avgPriceByKomoditas($prevDate);

            $changes = [];
            foreach ($latestPrices as $id => $latest) {
                $prev = $prevPrices[$id] ?? null;
                if ($prev && $prev > 0) {
                    $pct = (($latest - $prev) / $prev) * 100;
                    $changes[] = [
                        'komoditas_id' => $id,
                        'harga_awal'   => (int) $prev,
                        'harga_akhir'  => (int) $latest,
                        'pct'          => round($pct, 2),
                    ];
                }
            }

            usort($changes, fn($a, $b) => $b['pct'] <=> $a['pct']);

            $gainers = array_slice($changes, 0, 5);
            $losers  = array_slice(array_reverse($changes), 0, 5);

            $komMap = MasterKomoditas::pluck('nama', 'id_master_komoditas');

            $mapFn = fn($item) => [
                'komoditas'   => $komMap[$item['komoditas_id']] ?? 'Unknown',
                'harga_awal'  => $item['harga_awal'],
                'harga_akhir' => $item['harga_akhir'],
                'pct'         => $item['pct'],
            ];

            return [
                'gainers' => array_map($mapFn, $gainers),
                'losers'  => array_map($mapFn, $losers),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_komoditas' => $totalKomoditas,
                'total_provinsi'  => $totalProvinsi,
                'total_kabkota'   => $totalKabkota,
                'total_pasar'     => $totalPasar,
                'last_scrape'     => $lastScrape ? [
                    'workflow'    => $lastScrape->workflow_name,
                    'status'      => $lastScrape->status,
                    'total_pasar' => $lastScrape->total_pasar,
                    'total_data'  => $lastScrape->total_data,
                    'total_insert'=> $lastScrape->total_insert,
                    'started_at'  => $lastScrape->started_at?->format('Y-m-d H:i:s'),
                    'finished_at' => $lastScrape->finished_at?->format('Y-m-d H:i:s'),
                    'duration'    => $lastScrape->duration_seconds,
                ] : null,
                'trend'           => $trend,
                'gainers'         => $gainersLosers['gainers'],
                'losers'          => $gainersLosers['losers'],
            ],
        ]);
    }

    private function avgPriceByKomoditas(string $date): array
    {
        return Komoditas::query()
            ->validHarga()
            ->whereDate('tanggal', $date)
            ->join('master_komoditas', 'komoditas.komoditas_master_id', '=', 'master_komoditas.id_master_komoditas')
            ->select(
                'komoditas.komoditas_master_id',
                DB::raw('ROUND(AVG(komoditas.harga)) as avg_harga')
            )
            ->groupBy('komoditas.komoditas_master_id')
            ->pluck('avg_harga', 'komoditas_master_id')
            ->toArray();
    }
}
