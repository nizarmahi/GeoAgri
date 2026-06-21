<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use Carbon\Carbon;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
        set_time_limit(0);
        ini_set('memory_limit', '512M');

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
            ->where('tanggal', '>=', $tanggal)
            ->where('tanggal', '<', Carbon::parse($tanggal)->addDay()->toDateString())
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
                // DB::raw("ST_AsGeoJSON(ST_Union(geom)) AS geojson")
                DB::raw("
                    ST_AsGeoJSON(
                        ST_SimplifyPreserveTopology(geom, 0.05)
                    ) AS geojson
                ")
            )
            ->whereNotNull('geom')
            ->groupBy('provinsi_id')
            ->get();

        $features = [];

        foreach ($geometries as $geo) {
            $geometry = json_decode($geo->geojson, true);
            if (! $geometry) continue;

            $harga = $hargaMap->get($geo->provinsi_id);

            // if (! $harga) continue;

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

    // ── GeoJSON Pasar (Point) ────────────────────────────────

    public function pasarMap(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'komoditas_id' => 'required|integer|exists:master_komoditas,id_master_komoditas',
            'tanggal'      => 'nullable|date',
            'provinsi_id'  => 'nullable|integer|exists:provinsi,id_provinsi',
        ]);

        $komoditasId = (int) $validated['komoditas_id'];
        $tanggal     = $validated['tanggal'] ?? now()->toDateString();
        $provinsiId  = $request->integer('provinsi_id') ?: null;

        $query = Komoditas::query()
            ->filterKomoditas($komoditasId)
            ->where('tanggal', '>=', $tanggal)
            ->where('tanggal', '<', Carbon::parse($tanggal)->addDay()->toDateString())
            ->validHarga()
            ->join('pasar', 'komoditas.pasar_id', '=', 'pasar.id')
            ->join('kab_kota', 'pasar.kabkota_id', '=', 'kab_kota.id')
            ->join('provinsi', 'kab_kota.provinsi_id', '=', 'provinsi.id_provinsi')
            ->select(
                'komoditas.pasar_id',
                'pasar.psr_nama as pasar_nama',
                'kab_kota.kab_nama as kabupaten_nama',
                'provinsi.nama as provinsi_nama',
                'provinsi.id_provinsi',
                DB::raw('ROUND(AVG(komoditas.harga)) as harga'),
                DB::raw('COUNT(*) as total_records'),
                DB::raw('MAX(ST_Y(ST_Centroid(pasar.geom))) as latitude'),
                DB::raw('MAX(ST_X(ST_Centroid(pasar.geom))) as longitude'),
            )
            ->whereNotNull('pasar.geom')
            ->groupBy(
                'komoditas.pasar_id',
                'pasar.psr_nama',
                'kab_kota.kab_nama',
                'provinsi.nama',
                'provinsi.id_provinsi'
            );
        // dd($query->toSql(), $query->getBindings());

        if ($provinsiId) {
            $query->where('kab_kota.provinsi_id', $provinsiId);
        }

        $pasarData = $query->get();

        $features = [];

        foreach ($pasarData as $item) {
            if (! $item->latitude || ! $item->longitude) continue;

            $features[] = [
                'type'       => 'Feature',
                'geometry'   => [
                    'type'        => 'Point',
                    'coordinates' => [(float) $item->longitude, (float) $item->latitude],
                ],
                'properties' => [
                    'pasar_id'       => $item->pasar_id,
                    'nama'           => $item->pasar_nama,
                    'kabupaten'      => $item->kabupaten_nama,
                    'provinsi'       => $item->provinsi_nama,
                    'provinsi_id'    => $item->id_provinsi,
                    'harga'          => $item->harga ? (int) $item->harga : null,
                    'total_records'  => (int) $item->total_records,
                ],
            ];
        }

        // dd($features);

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
            'meta'     => [
                'level'         => 'pasar',
                'komoditas_id'  => $komoditasId,
                'tanggal'       => $tanggal,
                'provinsi_id'   => $provinsiId,
                'total_features' => count($features),
            ],
        ]);
    }

    // ── GeoJSON Level Kabupaten ───────────────────────────────

    private function buildKabupatenGeoJSON(int $komoditasId, string $tanggal): array
    {
        // Harga rata-rata per kabupaten (agregat dari pasar di kabupaten tsb)
        $hargaMap = Komoditas::query()
            ->filterKomoditas($komoditasId)
            ->where('tanggal', '>=', $tanggal)
            ->where('tanggal', '<', Carbon::parse($tanggal)->addDay()->toDateString())
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

        // dd($hargaMap->toArray(), $geometries->pluck('id')->toArray());

        foreach ($geometries as $kab) {
            $geometry = json_decode($kab->geojson, true);
            if (! $geometry) continue;

            $harga = $hargaMap->get($kab->id);

            // if (! $harga) continue; // Hanya tampilkan kabupaten yang punya data harga

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
            // dd($features);
        }

        // dd($features);

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

    // ── Proxy Heatmap API Eksternal ──────────────────────────

    /**
     * GET /api/komoditas/heatmap
     *
     * Proxy ke API eksternal untuk menghindari CORS.
     * Forward: http://labai.polinema.ac.id:1901/api/heatmap?komoditas=...
     */
    public function heatmapProxy(Request $request): JsonResponse
    {
        $komoditas = $request->input('komoditas');

        if (! $komoditas) {
            return response()->json([
                'error' => 'Parameter "komoditas" wajib diisi.'
            ], 422);
        }

        $externalUrl = 'http://labai.polinema.ac.id:1901/api/heatmap?komoditas=' . urlencode($komoditas);

        try {
            $response = Http::timeout(30)->get($externalUrl);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Gagal mengambil data dari server eksternal.',
                    'status' => $response->status(),
                ], $response->status());
            }

            $data = $response->json();

            return response()->json($data);
        } catch (HttpClientException $e) {
            return response()->json([
                'error' => 'Koneksi ke server eksternal gagal: ' . $e->getMessage(),
            ], 502);
        }
    }
}
