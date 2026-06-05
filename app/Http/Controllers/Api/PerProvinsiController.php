<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KomoditasRataRataProvinsi;
use App\Models\MasterKomoditas;
use App\Models\Pasar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PerProvinsiController extends Controller
{
    /**
     * GET /api/komoditas/per-provinsi
     *
     * Rata-rata harga seluruh provinsi pada tanggal tertentu.
     * Digunakan untuk tabel perbandingan dan data peta.
     *
     * Query params:
     *   komoditas_id  (required)  integer
     *   tanggal       (optional)  Y-m-d  (default: hari ini)
     */
    public function __invoke(Request $request): JsonResponse
    {
        // ── Validasi ─────────────────────────────────────────
        $validated = $request->validate([
            'komoditas_id' => 'required|integer|exists:master_komoditas,id_master_komoditas',
            'tanggal'      => 'nullable|date',
        ]);

        $komoditasId = (int) $validated['komoditas_id'];
        $tanggal     = $validated['tanggal'] ?? now()->toDateString();

        // ── Query (cache 10 menit) ─────────────────────────
        $cacheKey = "per_provinsi:{$komoditasId}:{$tanggal}";

        $rows = Cache::remember($cacheKey, 600, function () use ($komoditasId, $tanggal) {
            return KomoditasRataRataProvinsi::query()
                ->filterKomoditas($komoditasId)
                ->whereDate('tanggal', $tanggal)
                ->validHarga()
                ->with('provinsi:id_provinsi,nama,latitude,longitude')
                ->orderBy('harga')
                ->get();
        });

        $data = $rows->map(fn($row) => [
            'provinsi_id' => $row->provinsi_id,
            'provinsi'    => $row->provinsi?->nama ?? 'Provinsi #' . $row->provinsi_id,
            'latitude'    => $row->provinsi?->latitude,
            'longitude'   => $row->provinsi?->longitude,
            'harga'       => $row->harga,
            'tanggal'     => $row->tanggal->format('Y-m-d'),
        ]);

        // ── Statistik ringkasan ────────────────────────────
        $hargaList  = $rows->pluck('harga');
        $statistik  = [
            'min'    => $hargaList->min(),
            'max'    => $hargaList->max(),
            'rata'   => (int) round($hargaList->avg()),
            'median' => $this->median($hargaList->toArray()),
        ];
        $totalPasar = Pasar::getTotalPasar();
        $totalKomoditas = MasterKomoditas::count();
        $dataValid = KomoditasRataRataProvinsi::query()
            // ->filterKomoditas($komoditasId)
            ->whereDate('tanggal', $tanggal)
            ->validHarga()
            ->count();
        $dataValidPct = $data->count() > 0 ? ($dataValid / $data->count()) * 100 : 0;
        $dataNull  = KomoditasRataRataProvinsi::query()
            // ->filterKomoditas($komoditasId)
            ->whereDate('tanggal', $tanggal)
            ->whereNull('harga')
            ->count();

        return response()->json([
            'status'    => 'success',
            'meta'      => [
                'komoditas_id'   => $komoditasId,
                'tanggal'        => $tanggal,
                'total_provinsi' => $data->count(),
                'total_pasar'    => $totalPasar,
                'total_komoditas' => $totalKomoditas,
                'data_valid'     => $dataValid,
                'data_valid_pct' => $dataValidPct,
                'data_null'      => $dataNull,
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
