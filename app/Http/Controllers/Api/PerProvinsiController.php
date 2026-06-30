<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\MasterKomoditas;
use App\Models\Pasar;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PerProvinsiController extends Controller
{
    /**
     * GET /api/komoditas/per-provinsi
     *
     * Rata-rata harga seluruh provinsi pada tanggal tertentu.
     * Data dihitung langsung dari tabel komoditas (raw) melalui
     * agregasi per provinsi.
     *
     * Query params:
     *   komoditas_id   (required)  integer
     *   tanggal        (optional)  Y-m-d  (default: hari ini)
     *   provinsi_ids[] (optional)  array of integer
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'komoditas_id'  => 'required|integer|exists:master_komoditas,id_master_komoditas',
            'tanggal'       => 'nullable|date',
            'provinsi_ids'  => 'nullable|array',
            'provinsi_ids.*' => 'integer|exists:provinsi,id_provinsi',
        ]);

        $komoditasId  = (int) $validated['komoditas_id'];
        $tanggal      = $validated['tanggal'] ?? now()->toDateString();
        $provinsiIds  = $validated['provinsi_ids'] ?? null;

        // Fallback ke tanggal terakhir yang punya data
        $latestDate = Komoditas::query()
            ->filterKomoditas($komoditasId)
            ->validHarga()
            ->where('tanggal', '<=', $tanggal)
            ->orderBy('tanggal', 'desc')
            ->value('tanggal');

        if ($latestDate) {
            $tanggal = $latestDate instanceof Carbon
                ? $latestDate->toDateString()
                : $latestDate;
        }

        $cacheKey = "per_provinsi_v2:{$komoditasId}:{$tanggal}:" . ($provinsiIds ? implode(',', $provinsiIds) : 'all');

        $rows = Cache::remember($cacheKey, 600, function () use ($komoditasId, $tanggal, $provinsiIds) {
            $query = Komoditas::query()
                ->filterKomoditas($komoditasId)
                ->where('tanggal', '>=', $tanggal)
                ->where('tanggal', '<', Carbon::parse($tanggal)->addDay()->toDateString())
                ->validHarga()
                ->join('pasar', 'komoditas.pasar_id', '=', 'pasar.id')
                ->join('kab_kota', 'pasar.kabkota_id', '=', 'kab_kota.id')
                ->join('provinsi', 'kab_kota.provinsi_id', '=', 'provinsi.id_provinsi')
                ->select(
                    'kab_kota.provinsi_id',
                    'provinsi.nama as provinsi_nama',
                    'provinsi.latitude',
                    'provinsi.longitude',
                    DB::raw('ROUND(AVG(komoditas.harga)) AS harga'),
                    DB::raw('COUNT(DISTINCT komoditas.pasar_id) AS jumlah_pasar')
                );

            if ($provinsiIds) {
                $query->whereIn('kab_kota.provinsi_id', $provinsiIds);
            }

            return $query
                ->groupBy('kab_kota.provinsi_id', 'provinsi.nama', 'provinsi.latitude', 'provinsi.longitude')
                ->orderBy('harga')
                ->get();
        });

        $data = $rows->map(fn($row) => [
            'provinsi_id' => $row->provinsi_id,
            'provinsi'    => $row->provinsi_nama,
            'latitude'    => $row->latitude,
            'longitude'   => $row->longitude,
            'harga'       => (int) $row->harga,
            'tanggal'     => $tanggal,
        ]);

        // ── Statistik ringkasan ────────────────────────────
        $hargaList  = $rows->pluck('harga');
        $statistik  = [
            'min'    => $hargaList->min(),
            'max'    => $hargaList->max(),
            'rata'   => (int) round($hargaList->avg()),
            'median' => $this->median($hargaList->toArray()),
        ];

        $totalPasar     = Pasar::getTotalPasar();
        $totalKomoditas = MasterKomoditas::count();
        $totalProvinsi  = $provinsiIds ? count($provinsiIds) : DB::table('provinsi')->count();
        $dataValid      = $data->count();
        $dataNull       = $totalProvinsi - $dataValid;
        $dataValidPct   = $totalProvinsi > 0 ? ($dataValid / $totalProvinsi) * 100 : 0;

        return response()->json([
            'status'    => 'success',
            'meta'      => [
                'komoditas_id'    => $komoditasId,
                'tanggal'         => $tanggal,
                'total_provinsi'  => $totalProvinsi,
                'total_pasar'     => $totalPasar,
                'total_komoditas' => $totalKomoditas,
                'data_valid'      => $dataValid,
                'data_valid_pct'  => round($dataValidPct, 1),
                'data_null'       => $dataNull,
            ],
            'statistik' => $statistik,
            'data'      => $data,
        ]);
    }

    private function median(array $arr): ?float
    {
        if (empty($arr)) return null;
        sort($arr);
        $n   = count($arr);
        $mid = (int) floor($n / 2);
        return $n % 2 === 1
            ? (float) $arr[$mid]
            : ($arr[$mid - 1] + $arr[$mid]) / 2;
    }
}
