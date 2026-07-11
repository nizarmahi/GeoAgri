<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provinsi;
use App\Models\KabupatenKota;
use App\Models\Pasar;
use App\Models\MasterKomoditas;
use App\Models\KategoriKomoditas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMasterController extends Controller
{
    // ── Provinsi ──────────────────────────────────────────────

    public function indexProvinsi(Request $request)
    {
        $query = Provinsi::query();

        if ($request->filled('search')) {
            $query->where('nama', 'ilike', "%{$request->search}%");
        }

        $data = $query->orderBy('nama')->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function storeProvinsi(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keycode' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $provinsi = Provinsi::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $provinsi,
            'message' => 'Provinsi berhasil ditambahkan.',
        ], 201);
    }

    public function updateProvinsi(Request $request, $id)
    {
        $provinsi = Provinsi::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keycode' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $provinsi->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $provinsi,
            'message' => 'Provinsi berhasil diperbarui.',
        ]);
    }

    public function destroyProvinsi($id)
    {
        $provinsi = Provinsi::findOrFail($id);
        $provinsi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Provinsi berhasil dihapus.',
        ]);
    }

    // ── Kabupaten/Kota ────────────────────────────────────────

    public function indexKabkota(Request $request)
    {
        $query = KabupatenKota::with('provinsi:id_provinsi,nama');

        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->provinsi_id);
        }

        if ($request->filled('search')) {
            $query->where('kab_nama', 'ilike', "%{$request->search}%");
        }

        $data = $query->orderBy('kab_nama')->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function storeKabkota(Request $request)
    {
        $validated = $request->validate([
            'provinsi_id' => 'required|integer|exists:provinsi,id_provinsi',
            'kab_nama' => 'required|string|max:255',
            'kab_keycode' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $kabkota = KabupatenKota::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $kabkota,
            'message' => 'Kabupaten/Kota berhasil ditambahkan.',
        ], 201);
    }

    public function updateKabkota(Request $request, $id)
    {
        $kabkota = KabupatenKota::findOrFail($id);

        $validated = $request->validate([
            'provinsi_id' => 'required|integer|exists:provinsi,id_provinsi',
            'kab_nama' => 'required|string|max:255',
            'kab_keycode' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $kabkota->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $kabkota,
            'message' => 'Kabupaten/Kota berhasil diperbarui.',
        ]);
    }

    public function destroyKabkota($id)
    {
        $kabkota = KabupatenKota::findOrFail($id);
        $kabkota->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kabupaten/Kota berhasil dihapus.',
        ]);
    }

    // ── Pasar ─────────────────────────────────────────────────

    public function indexPasar(Request $request)
    {
        $query = Pasar::with('kabupatenKota:id,kab_nama,provinsi_id');

        if ($request->filled('kabkota_id')) {
            $query->where('kabkota_id', $request->kabkota_id);
        }

        if ($request->filled('provinsi_id')) {
            $query->whereHas('kabupatenKota', function ($q) use ($request) {
                $q->where('provinsi_id', $request->provinsi_id);
            });
        }

        if ($request->filled('search')) {
            $query->where('psr_nama', 'ilike', "%{$request->search}%");
        }

        $data = $query->orderBy('psr_nama')->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function storePasar(Request $request)
    {
        $validated = $request->validate([
            'psr_nama' => 'required|string|max:255',
            'kabkota_id' => 'required|integer|exists:kab_kota,id',
            'psr_status' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_virtual' => 'nullable|boolean',
        ]);

        $pasar = Pasar::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $pasar,
            'message' => 'Pasar berhasil ditambahkan.',
        ], 201);
    }

    public function updatePasar(Request $request, $id)
    {
        $pasar = Pasar::findOrFail($id);

        $validated = $request->validate([
            'psr_nama' => 'required|string|max:255',
            'kabkota_id' => 'required|integer|exists:kab_kota,id',
            'psr_status' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_virtual' => 'nullable|boolean',
        ]);

        $pasar->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $pasar,
            'message' => 'Pasar berhasil diperbarui.',
        ]);
    }

    public function destroyPasar($id)
    {
        $pasar = Pasar::findOrFail($id);
        $pasar->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pasar berhasil dihapus.',
        ]);
    }

    // ── Komoditas ─────────────────────────────────────────────

    public function indexKomoditas(Request $request)
    {
        $query = MasterKomoditas::with('kategori:id,kategori');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'ilike', "%{$request->search}%");
        }

        $data = $query->orderBy('nama')->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function storeKomoditas(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'kategori_id' => 'nullable|integer|exists:kategori_komoditas,id',
        ]);

        $komoditas = MasterKomoditas::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $komoditas,
            'message' => 'Komoditas berhasil ditambahkan.',
        ], 201);
    }

    public function updateKomoditas(Request $request, $id)
    {
        $komoditas = MasterKomoditas::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'kategori_id' => 'nullable|integer|exists:kategori_komoditas,id',
        ]);

        $komoditas->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $komoditas,
            'message' => 'Komoditas berhasil diperbarui.',
        ]);
    }

    public function destroyKomoditas($id)
    {
        $komoditas = MasterKomoditas::findOrFail($id);
        $komoditas->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Komoditas berhasil dihapus.',
        ]);
    }

    // ── Kategori Komoditas ────────────────────────────────────

    public function indexKategori(Request $request)
    {
        $query = KategoriKomoditas::query();

        if ($request->filled('search')) {
            $query->where('kategori', 'ilike', "%{$request->search}%");
        }

        $data = $query->orderBy('kategori')->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        $kategori = KategoriKomoditas::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $kategori,
            'message' => 'Kategori berhasil ditambahkan.',
        ], 201);
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = KategoriKomoditas::findOrFail($id);

        $validated = $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        $kategori->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $kategori,
            'message' => 'Kategori berhasil diperbarui.',
        ]);
    }

    public function destroyKategori($id)
    {
        $kategori = KategoriKomoditas::findOrFail($id);
        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
