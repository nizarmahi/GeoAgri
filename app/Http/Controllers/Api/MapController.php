<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    /**
     * GET /api/komoditas/map
     *
     * Mengembalikan GeoJSON untuk choropleth map di Leaflet.
     * - level=provinsi   → union geometri kabupaten per provinsi + harga rata-rata
     * - level=kabupaten  → geometri per kabupaten + harga rata-rata pasar
     *
     * Query params:
     *   komoditas_id  (required)  integer
     *   tanggal       (optional)  Y-m-d       (default: hari ini)
     *   level         (optional)  provinsi|kabupaten  (default: provinsi)
     */
    public function __invoke(Request $request): JsonResponse
    {
        // ── Validasi ─────────────────────────────────────────
        $validated = $request->validate([
            'komoditas_id' => 'required|integer|exists:master_komoditas,id_master_komoditas',
            'tanggal'      => 'nullable|date',
            'level'        => 'nullable|in:provinsi,kabupaten',
        ]);

        $komoditasId = (int) $validated['komoditas_id'];
        $tanggal     = $validated['tanggal'] ?? now()->toDateString();
        $level       = $validated['level']   ?? 'provinsi';

        $cacheKey = "map:{$level}:{$komoditasId}:{$tanggal}";

        $geojson = Cache::remember($cacheKey, 600, function () use ($komoditasId, $tanggal, $level) {
            return $level === 'provinsi'
                ? $this->buildProvinsiGeoJSON($komoditasId, $tanggal)
                : $this->buildKabupatenGeoJSON($komoditasId, $tanggal);
        });

        return response()->json($geojson);
    }

    // ── GeoJSON Level Provinsi ────────────────────────────────

    private function buildProvinsiGeoJSON(int $komoditasId, string $tanggal): array
    {
        $hargaMap = Komoditas::query()
            ->filterKomoditas($komoditasId)
            ->whereDate('tanggal', $tanggal)
            ->validHarga()
            ->join('pasar', 'komoditas.pasar_id', '=', 'pasar.id')
            ->join('kab_kota', 'pasar.kabkota_id', '=', 'kab_kota.id')
            ->select(
                'kab_kota.provinsi_id',
                DB::raw('ROUND(AVG(komoditas.harga)) AS harga'),
                DB::raw('COUNT(DISTINCT komoditas.pasar_id) AS jumlah_pasar')
            )
            ->groupBy('kab_kota.provinsi_id')
            ->get()
            ->keyBy('provinsi_id');

        $provinsiNama = DB::table('provinsi')
            ->pluck('nama', 'id_provinsi');

        $geometries = DB::table('kab_kota')
            ->select(
                'provinsi_id',
                DB::raw("ST_AsGeoJSON(ST_Union(geom)) AS geojson")
            )
            ->whereNotNull('geom')
            ->groupBy('provinsi_id')
            ->get();

        $features = [];

        foreach ($geometries as $geo) {
            $geometry = json_decode($geo->geojson, true);
            if (! $geometry) continue;

            $harga = $hargaMap->get($geo->provinsi_id);

            $features[] = [
                'type'       => 'Feature',
                'geometry'   => $geometry,
                'properties' => [
                    'provinsi_id' => $geo->provinsi_id,
                    'nama'        => $provinsiNama[$geo->provinsi_id] ?? 'Provinsi #' . $geo->provinsi_id,
                    'harga'       => $harga ? (int) $harga->harga : null,
                    'jumlah_pasar' => $harga ? (int) $harga->jumlah_pasar : 0,
                    'has_data'    => ! is_null($harga),
                ],
            ];
        }

        return [
            'type'     => 'FeatureCollection',
            'features' => $features,
            'meta'     => [
                'level'          => 'provinsi',
                'komoditas_id'   => $komoditasId,
                'tanggal'        => $tanggal,
                'total_features' => count($features),
            ],
        ];
    }

    // ── GeoJSON Level Kabupaten ───────────────────────────────

    private function buildKabupatenGeoJSON(int $komoditasId, string $tanggal): array
    {
        // Harga rata-rata per kabupaten (agregat dari pasar di kabupaten tsb)
        $hargaMap = Komoditas::query()
            ->filterKomoditas($komoditasId)
            ->whereDate('tanggal', $tanggal)
            ->validHarga()
            ->join('pasar', 'komoditas.pasar_id', '=', 'pasar.id')
            ->select(
                'pasar.kabkota_id as kabupaten_id',
                DB::raw('ROUND(AVG(komoditas.harga)) AS harga'),
                DB::raw('COUNT(DISTINCT komoditas.pasar_id) AS jumlah_pasar')
            )
            ->groupBy('pasar.kabkota_id')
            ->get()
            ->keyBy('kabupaten_id');

        // Ambil geometri tiap kabupaten
        $geometries = DB::table('kab_kota')
            ->select(
                'id',
                'provinsi_id',
                'kab_nama',
                DB::raw("ST_AsGeoJSON(geom) AS geojson")
            )
            ->whereNotNull('geom')
            ->get();

        $features = [];

        foreach ($geometries as $kab) {
            $geometry = json_decode($kab->geojson, true);
            if (! $geometry) continue;

            $harga = $hargaMap->get($kab->id);

            $features[] = [
                'type'       => 'Feature',
                'geometry'   => $geometry,
                'properties' => [
                    'kabupaten_id'  => $kab->id,
                    'provinsi_id'   => $kab->provinsi_id,
                    'nama'          => $kab->kab_nama,
                    'harga'         => $harga ? (int) $harga->harga : null,
                    'jumlah_pasar'  => $harga ? (int) $harga->jumlah_pasar : 0,
                    'has_data'      => ! is_null($harga),
                ],
            ];
        }

        return [
            'type'     => 'FeatureCollection',
            'features' => $features,
            'meta'     => [
                'level'          => 'kabupaten',
                'komoditas_id'   => $komoditasId,
                'tanggal'        => $tanggal,
                'total_features' => count($features),
            ],
        ];
    }
}
