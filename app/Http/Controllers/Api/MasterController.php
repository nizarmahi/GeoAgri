<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KabupatenKota;
use App\Models\MasterKomoditas;
use App\Models\Pasar;
use App\Models\Provinsi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MasterController extends Controller
{
    /**
     * GET /api/master/komoditas
     * Daftar semua komoditas (untuk dropdown filter)
     */
    public function komoditas(): JsonResponse
    {
        $data = Cache::remember('master:komoditas', 3600, function () {
            return MasterKomoditas::orderBy('nama')
                ->get(['id_master_komoditas as id', 'nama', 'satuan']);
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/master/provinsi
     * Daftar semua provinsi (untuk dropdown filter)
     */
    public function provinsi(): JsonResponse
    {
        $data = Cache::remember('master:provinsi', 3600, function () {
            return Provinsi::orderBy('nama')
                ->get(['id_provinsi as id', 'nama', 'latitude', 'longitude']);
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/master/pasar
     * Daftar pasar untuk filter dan pemetaan data.
     */
    public function pasar(): JsonResponse
    {
        $data = Cache::remember('master:pasar', 3600, function () {
            return Pasar::query()
                ->with('kabupatenKota:id,kab_nama,provinsi_id')
                ->orderBy('psr_nama')
                ->get([
                    'id',
                    'psr_nama as nama',
                    'kabkota_id as kabupaten_kota_id',
                ])
                ->map(function ($pasar) {
                    return [
                        'id' => $pasar->id,
                        'nama' => $pasar->nama,
                        'kabupaten_id' => $pasar->kabupaten_kota_id,
                        'kabupaten' => $pasar->kabupatenKota?->kab_nama,
                        'provinsi_id' => $pasar->kabupatenKota?->provinsi_id,
                    ];
                });
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * GET /api/master/kabkota?provinsi_id=35
     * Daftar kabupaten/kota untuk dropdown filter.
     */
    public function kabkota(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provinsi_id' => 'required|integer|exists:provinsi,id_provinsi',
        ]);

        $cacheKey = 'master:kabkota:' . $validated['provinsi_id'];

        $data = Cache::remember($cacheKey, 3600, function () use ($validated) {
            return KabupatenKota::where('provinsi_id', $validated['provinsi_id'])
                ->orderBy('kab_nama')
                ->get(['id', 'kab_nama as nama', 'provinsi_id']);
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }
}
