<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\MasterKomoditas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasarAnalysisController extends Controller
{
    /**
     * GET /api/komoditas/analisis-pasar
     *
     * Market analysis with null records, avg price, coordinates.
     *
     * Query params:
     *   komoditas_id (optional)
     *   provinsi_id  (optional)
     *   tanggal      (optional, Y-m-d)
     *   per_page     (optional, default: 10)
     */
    public function __invoke(Request $request): JsonResponse
    {
        $komoditasId = $request->integer('komoditas_id', 0) ?: null;
        $provinsiId  = $request->integer('provinsi_id', 0) ?: null;
        $tanggal     = $request->input('tanggal');
        $perPage     = (int) $request->input('per_page', 10);

        $query = Komoditas::query()
            ->join('pasar', 'komoditas.pasar_id', '=', 'pasar.id')
            ->join('kab_kota', 'pasar.kabkota_id', '=', 'kab_kota.id')
            ->join('provinsi', 'kab_kota.provinsi_id', '=', 'provinsi.id_provinsi')
            ->select(
                'komoditas.pasar_id',
                'pasar.psr_nama as pasar_nama',
                'pasar.latitude',
                'pasar.longitude',
                'provinsi.nama as provinsi_nama',
                'kab_kota.kab_nama as kabupaten_nama',
                DB::raw('COUNT(*) as total_records'),
                DB::raw('SUM(CASE WHEN komoditas.harga IS NULL OR komoditas.harga <= 0 THEN 1 ELSE 0 END) as null_records'),
                DB::raw('ROUND(AVG(komoditas.harga)) as avg_harga'),
                DB::raw('MAX(komoditas.tanggal) as last_update')
            )
            ->groupBy('komoditas.pasar_id', 'pasar.psr_nama', 'pasar.latitude', 'pasar.longitude', 'provinsi.nama', 'kab_kota.kab_nama');

        if ($komoditasId) {
            $query->where('komoditas.komoditas_master_id', $komoditasId);
        }

        if ($provinsiId) {
            $query->where('kab_kota.provinsi_id', $provinsiId);
        }

        if ($tanggal) {
            $query->whereDate('komoditas.tanggal', $tanggal);
        } else {
            $query->where('komoditas.tanggal', '>=', now()->subDays(30));
        }

        $pasarData = $query->orderByDesc('null_records')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($item) {
                $pct = $item->total_records > 0
                    ? round($item->null_records / $item->total_records * 100, 1)
                    : 0;
                $color = $pct > 15 ? '#dc2626' : ($pct > 8 ? '#ea580c' : ($pct > 5 ? '#2d3bde' : ($pct > 2 ? '#7c3aed' : '#16a34a')));
                return [
                    'pasar_id'      => $item->pasar_id,
                    'nama'          => $item->pasar_nama,
                    'provinsi'      => $item->provinsi_nama,
                    'kabupaten'     => $item->kabupaten_nama,
                    'latitude'      => $item->latitude ? (float) $item->latitude : null,
                    'longitude'     => $item->longitude ? (float) $item->longitude : null,
                    'total_records' => (int) $item->total_records,
                    'null_records'  => (int) $item->null_records,
                    'avg_harga'     => $item->avg_harga ? (int) $item->avg_harga : null,
                    'last_update'   => $item->last_update,
                    'null_pct'      => $pct,
                    'color'         => $color,
                ];
            });

        $komoditasList = MasterKomoditas::orderBy('nama')->get(['id_master_komoditas', 'nama']);
        $provinsiList  = DB::table('provinsi')->orderBy('nama')->get(['id_provinsi', 'nama']);

        return response()->json([
            'status' => 'success',
            'data'   => $pasarData->items(),
            'meta'   => [
                'current_page' => $pasarData->currentPage(),
                'last_page'    => $pasarData->lastPage(),
                'per_page'     => $pasarData->perPage(),
                'total'        => $pasarData->total(),
                'komoditas_list' => $komoditasList,
                'provinsi_list'  => $provinsiList,
            ],
        ]);
    }
}
