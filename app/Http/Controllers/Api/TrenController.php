<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\KomoditasRataRataProvinsi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrenController extends Controller
{
    /**
     * GET /api/komoditas/tren
     *
     * Menampilkan tren harga berdasarkan tanggal.
     * - Jika provinsi_id diberikan  → tren 1 provinsi
     * - Jika provinsi_id tidak ada  → rata-rata nasional (semua provinsi)
     *
     * Query params:
     *   komoditas_id  (required)  integer
     *   provinsi_id   (optional)  integer
     *   from          (optional)  Y-m-d   (default: 30 hari lalu)
     *   to            (optional)  Y-m-d   (default: hari ini)
     */
    public function __invoke(Request $request): JsonResponse
    {
        // ── Validasi ─────────────────────────────────────────
        $validated = $request->validate([
            'komoditas_id' => 'required|integer|exists:master_komoditas,id_master_komoditas',
            'provinsi_id'  => 'nullable|integer|exists:provinsi,id_provinsi',
            'from'         => 'nullable|date',
            'to'           => 'nullable|date|after_or_equal:from',
        ]);

        $komoditasId = (int) $validated['komoditas_id'];
        $provinsiId  = isset($validated['provinsi_id']) ? (int) $validated['provinsi_id'] : null;
        $from        = $validated['from'] ?? now()->subDays(30)->toDateString();
        $to          = $validated['to']   ?? now()->toDateString();

        // ── Query ─────────────────────────────────────────────
        if ($provinsiId) {
            $data = $this->trenProvinsi($komoditasId, $provinsiId, $from, $to);
        } else {
            $data = $this->trenNasional($komoditasId, $from, $to);
        }

        return response()->json([
            'status' => 'success',
            'meta'   => [
                'komoditas_id' => $komoditasId,
                'provinsi_id'  => $provinsiId ?? 'nasional',
                'from'         => $from,
                'to'           => $to,
                'total_titik'  => $data->count(),
            ],
            'data' => $data,
        ]);
    }

    /**
     * GET /api/komoditas/data
     *
     * Mengembalikan data komoditas mentah dari database untuk katalog data.
     * Query params:
     *   komoditas_id  (optional) integer
     *   provinsi_id   (optional) integer
     *   pasar_id      (optional) integer
     *   from          (optional) Y-m-d
     *   to            (optional) Y-m-d
     *   limit         (optional) integer, default 500
     */
    public function data(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'komoditas_id' => 'nullable|integer|exists:master_komoditas,id_master_komoditas',
            'provinsi_id' => 'nullable|integer|exists:provinsi,id_provinsi',
            'pasar_id' => 'nullable|integer|exists:pasar,id',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'limit' => 'nullable|integer|min:1|max:1000',
        ]);

        $query = Komoditas::query()
            ->join('pasar', 'komoditas.pasar_id', '=', 'pasar.id')
            ->join('kab_kota', 'pasar.kabkota_id', '=', 'kab_kota.id')
            ->join('provinsi', 'kab_kota.provinsi_id', '=', 'provinsi.id_provinsi')
            ->join('master_komoditas', 'komoditas.komoditas_master_id', '=', 'master_komoditas.id_master_komoditas')
            ->select(
                'komoditas.id_komoditas as id',
                'komoditas.tanggal',
                'komoditas.harga',
                'pasar.id as pasar_id',
                'pasar.psr_nama as pasar',
                'kab_kota.id as kabupaten_id',
                'kab_kota.kab_nama as kabupaten',
                'provinsi.id_provinsi as provinsi_id',
                'provinsi.nama as provinsi',
                'master_komoditas.id_master_komoditas as komoditas_master_id',
                'master_komoditas.nama as komoditas',
                'master_komoditas.satuan as satuan'
            )
            ->orderByDesc('komoditas.tanggal')
            ->orderBy('provinsi.nama')
            ->orderBy('pasar.psr_nama');

        if (!empty($validated['komoditas_id'])) {
            $query->where('komoditas.komoditas_master_id', $validated['komoditas_id']);
        }

        if (!empty($validated['provinsi_id'])) {
            $query->where('provinsi.id_provinsi', $validated['provinsi_id']);
            // $query->where('provinsi.id_provinsi', 4);
        }

        if (! empty($validated['pasar_id'])) {
            $query->where('komoditas.pasar_id', $validated['pasar_id']);
        }

        if (! empty($validated['from'])) {
            $query->whereDate('komoditas.tanggal', '>=', $validated['from']);
        }

        if (! empty($validated['to'])) {
            $query->whereDate('komoditas.tanggal', '<=', $validated['to']);
        }

        $rows = $query->limit($validated['limit'] ?? 500)->get()->map(function ($row) {
            return [
                'id' => $row->id,
                'tanggal' => $row->tanggal->format('Y-m-d'),
                'provinsi' => $row->provinsi,
                'pasar' => $row->pasar,
                'komoditas' => $row->komoditas,
                'harga' => $row->harga,
                'satuan' => $row->satuan,
                'status' => ($row->harga !== null && (int) $row->harga > 0) ? 'VALID' : 'NULL',
                'provinsi_id' => $row->provinsi_id,
                'kabupaten_id' => $row->kabupaten_id,
                'pasar_id' => $row->pasar_id,
                'komoditas_master_id' => $row->komoditas_master_id,
            ];
        });

        return response()->json([
            'status' => 'success',
            'meta' => [
                'total' => $rows->count(),
            ],
            'data' => $rows,
        ]);
    }

    // ── Private helpers ───────────────────────────────────────

    private function trenProvinsi(int $komoditasId, int $provinsiId, string $from, string $to)
    {
        $data = KomoditasRataRataProvinsi::query()
            ->filterKomoditas($komoditasId)
            ->filterProvinsi($provinsiId)
            ->dateRange($from, $to)
            ->validHarga()
            ->with('provinsi:id_provinsi,nama')
            ->orderBy('tanggal')
            ->get()
            ->map(fn($row) => [
                'tanggal'     => $row->tanggal->format('Y-m-d'),
                'harga'       => $row->harga,
                'label'       => $row->provinsi?->nama ?? 'Provinsi #' . $row->provinsi_id,
                'provinsi_id' => $row->provinsi_id,
            ]);

        return $data;
        // return KomoditasRataRataProvinsi::query()
        //     ->filterKomoditas($komoditasId)
        //     ->filterProvinsi($provinsiId)
        //     ->dateRange($from, $to)
        //     ->validHarga()
        //     ->with('provinsi:id_provinsi,nama')
        //     ->orderBy('tanggal')
        //     ->get()
        //     ->map(fn($row) => [
        //         'tanggal'     => $row->tanggal->format('Y-m-d'),
        //         'harga'       => $row->harga,
        //         'label'       => $row->provinsi?->nama ?? 'Provinsi #' . $row->provinsi_id,
        //         'provinsi_id' => $row->provinsi_id,
        //     ]);
    }

    private function trenNasional(int $komoditasId, string $from, string $to)
    {
        $data = KomoditasRataRataProvinsi::query()
            ->filterKomoditas($komoditasId)
            ->dateRange($from, $to)
            ->validHarga()
            ->select(
                'tanggal',
                DB::raw('ROUND(AVG(harga)) AS harga'),
                DB::raw('COUNT(DISTINCT provinsi_id) AS jumlah_provinsi')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->map(fn($row) => [
                'tanggal'          => $row->tanggal->format('Y-m-d'),
                'harga'            => (int) $row->harga,
                'label'            => 'Nasional',
                'jumlah_provinsi'  => (int) $row->jumlah_provinsi,
            ]);

        return $data;
        // return KomoditasRataRataProvinsi::query()
        //     ->filterKomoditas($komoditasId)
        //     ->dateRange($from, $to)
        //     ->validHarga()
        //     ->select(
        //         'tanggal',
        //         DB::raw('ROUND(AVG(harga)) AS harga'),
        //         DB::raw('COUNT(DISTINCT provinsi_id) AS jumlah_provinsi')
        //     )
        //     ->groupBy('tanggal')
        //     ->orderBy('tanggal')
        //     ->get()
        //     ->map(fn($row) => [
        //         'tanggal'          => $row->tanggal,
        //         'harga'            => (int) $row->harga,
        //         'label'            => 'Nasional',
        //         'jumlah_provinsi'  => (int) $row->jumlah_provinsi,
        //     ]);
    }
}
