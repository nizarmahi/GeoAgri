<?php

namespace App\Http\Controllers;

use App\Models\Komoditas;
use App\Models\MasterKomoditas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index');
    }

    public function analisisHarga(): View
    {
        return view('dashboard.analisis-harga');
    }

    public function analisisNull(): View
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
        $stabilityChange = $completionRate >= 75 ? 'baik' : 'perlu perhatian';

        $nullPerDate = Komoditas::select(
            'tanggal',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END) as null_count')
        )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get()
            ->reverse()
            ->values();

        $timelineLabels = $nullPerDate->map(fn($item) => Carbon::parse($item->tanggal)->format('d/m'));
        $timelineNullPct = $nullPerDate->map(function ($item) {
            return $item->total > 0 ? round($item->null_count / $item->total * 100, 1) : 0;
        });
        $timelineStability = $nullPerDate->map(function ($item) {
            return $item->total > 0 ? round(($item->total - $item->null_count) / $item->total * 100, 1) : 0;
        });

        $nullPerKomoditas = Komoditas::select(
            'komoditas_master_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END) as null_count')
        )
            ->with('masterKomoditas:id_master_komoditas,nama')
            ->groupBy('komoditas_master_id')
            ->get()
            ->sortByDesc('null_count');

        $donutLabels = $nullPerKomoditas->map(fn($item) => $item->masterKomoditas?->nama ?? 'Unknown');
        $donutData = $nullPerKomoditas->map(function ($item) {
            return $item->total > 0 ? round($item->null_count / $item->total * 100, 1) : 0;
        });
        $donutColors = ['#2d3bde', '#a5b4fc', '#e5e7eb', '#d1d5db', '#f97316', '#22c55e', '#ef4444', '#8b5cf6'];
        $donutTopLabel = $donutLabels->first() ?? '—';
        $donutTopValue = $donutData->first() ?? 0;

        $pasarNull = Komoditas::select(
            'pasar_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END) as null_count')
        )
            ->with('pasar:id,psr_nama,kabkota_id')
            ->groupBy('pasar_id')
            ->orderByDesc(DB::raw('SUM(CASE WHEN harga IS NULL OR harga <= 0 THEN 1 ELSE 0 END)'))
            ->paginate(5)
            ->through(function ($item) {
                $pct = $item->total > 0 ? round($item->null_count / $item->total * 100, 1) : 0;
                $color = $pct > 15 ? '#dc2626' : ($pct > 8 ? '#ea580c' : ($pct > 5 ? '#2d3bde' : ($pct > 2 ? '#7c3aed' : '#16a34a')));
                return [
                    'nama' => $item->pasar?->psr_nama ?? 'Pasar #' . $item->pasar_id,
                    'pct'  => $pct,
                    'color' => $color,
                ];
            });

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
            ->take(4)
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->pasar?->psr_nama ?? 'Sumber #' . $item->pasar_id,
                    'tgl'  => Carbon::parse($item->tanggal)->format('d F Y'),
                    'null' => $item->null_count,
                ];
            })->toArray();

        $recentDays = Komoditas::select(DB::raw('DISTINCT tanggal'))
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get()
            ->reverse()
            ->values();

        return view('dashboard.analisis-null', compact(
            'totalRecords',
            'totalNull',
            'totalValid',
            'completionRate',
            'lastScanTime',
            'stabilityLabel',
            'stabilityDesc',
            'stabilityChange',
            'timelineLabels',
            'timelineNullPct',
            'timelineStability',
            'donutLabels',
            'donutData',
            'donutColors',
            'donutTopLabel',
            'donutTopValue',
            'pasarNull',
            'batches',
            'recentDays'
        ));
    }

    public function analisisPasar(Request $request): View
    {
        $komoditasId = $request->integer('komoditas_id', 0) ?: null;
        $provinsiId  = $request->integer('provinsi_id', 0) ?: null;
        $tanggal     = $request->input('tanggal');

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
        }

        $pasarData = $query->orderByDesc('null_records')
            ->paginate(10)
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
                    'latitude'      => $item->latitude,
                    'longitude'     => $item->longitude,
                    'total_records' => $item->total_records,
                    'null_records'  => $item->null_records,
                    'avg_harga'     => $item->avg_harga,
                    'last_update'   => $item->last_update,
                    'null_pct'      => $pct,
                    'color'         => $color,
                ];
            });

        $komoditasList = MasterKomoditas::orderBy('nama')->get(['id_master_komoditas', 'nama']);

        $provinsiList = DB::table('provinsi')->orderBy('nama')->get(['id_provinsi', 'nama']);

        $selectedKomoditas = $komoditasId
            ? MasterKomoditas::find($komoditasId)?->nama
            : null;
        $selectedProvinsi  = $provinsiId
            ? DB::table('provinsi')->where('id_provinsi', $provinsiId)->value('nama')
            : null;

        return view('dashboard.analisis-pasar', compact(
            'pasarData',
            'komoditasList',
            'provinsiList',
            'komoditasId',
            'provinsiId',
            'tanggal',
            'selectedKomoditas',
            'selectedProvinsi'
        ));
    }

    public function data(): View
    {
        return view('dashboard.data');
    }
}
