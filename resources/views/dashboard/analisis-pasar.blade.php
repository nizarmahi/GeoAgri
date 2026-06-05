{{-- resources/views/dashboard/analisis-pasar.blade.php --}}
@extends('layouts.app')

@section('title', 'Analisis Pasar')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -.6px;
            color: var(--text);
        }

        .page-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .filter-bar {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .filter-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .filter-select {
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 7px 32px 7px 12px;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            transition: border-color .15s;
            min-width: 160px;
        }

        .filter-date-input {
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 7px 12px;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: border-color .15s;
            min-width: 140px;
        }

        .filter-date-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .filter-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 7px 18px;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .filter-btn:hover {
            background: var(--primary-hover, #1d2bb3);
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 16px;
            margin-bottom: 20px;
        }

        .panel {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 24px;
            box-shadow: var(--shadow-sm);
        }

        .panel-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--text-muted);
            text-align: left;
            padding: 0 0 12px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .data-table td {
            padding: 12px 0;
            font-size: 13px;
            color: var(--text);
            border-bottom: 1px solid var(--bg-muted);
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .badge-null {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            font-family: var(--mono);
            padding: 2px 8px;
            border-radius: 20px;
        }

        .badge-null.high {
            background: rgba(220, 38, 38, .1);
            color: #dc2626;
        }

        .badge-null.medium {
            background: rgba(234, 88, 12, .1);
            color: #ea580c;
        }

        .badge-null.low {
            background: rgba(22, 163, 74, .1);
            color: #16a34a;
        }

        .mono {
            font-family: var(--mono);
            font-weight: 600;
        }

        .text-muted {
            color: var(--text-muted);
        }

        .progress-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .progress-item {}

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .progress-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
        }

        .progress-pct {
            font-size: 12px;
            font-weight: 700;
            font-family: var(--mono);
            color: var(--pct-color, var(--red));
        }

        .progress-track {
            height: 6px;
            background: var(--bg-muted);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--fill-color, var(--primary));
            transition: width .6s cubic-bezier(.25, .8, .25, 1);
        }

        .pagination-wrap {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        .pagination-wrap nav {
            display: flex;
            justify-content: center;
        }

        .pagination-wrap .pagination {
            display: flex;
            gap: 3px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .pagination-wrap .page-item {
            margin: 0;
        }

        .pagination-wrap .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 26px;
            padding: 0 6px;
            font-size: 11px;
            font-weight: 600;
            font-family: var(--mono);
            color: var(--text-muted);
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all .15s;
        }

        .pagination-wrap .page-link:hover {
            background: var(--bg-muted);
            color: var(--text);
            border-color: var(--text-light);
        }

        .pagination-wrap .active .page-link {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .pagination-wrap .disabled .page-link {
            opacity: .35;
            pointer-events: none;
        }

        .info-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            padding: 12px 16px;
            background: var(--bg-muted);
            border-radius: var(--radius-sm);
            font-size: 12px;
            color: var(--text-muted);
        }

        .info-bar strong {
            color: var(--text);
        }

        #pasarMap {
            height: 360px;
            border-radius: var(--radius-sm);
            z-index: 0;
        }

        .map-panel {
            margin-bottom: 20px;
        }

        .map-legend {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        .map-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--text-muted);
        }

        .map-legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────── --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Analisis Pasar</h1>
            <p class="page-desc">Kualitas dan statistik data harga per pasar komoditas.</p>
        </div>
    </div>

    {{-- ── Filter Bar ───────────────────────────────── --}}
    <form class="filter-bar" method="GET" action="{{ route('analisis-pasar') }}">
        <span class="filter-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            Filter:
        </span>

        <select name="komoditas_id" class="filter-select">
            <option value="">Semua Komoditas</option>
            @foreach ($komoditasList as $k)
                <option value="{{ $k->id_master_komoditas }}" {{ $komoditasId == $k->id_master_komoditas ? 'selected' : '' }}>
                    {{ $k->nama }}
                </option>
            @endforeach
        </select>

        <select name="provinsi_id" class="filter-select">
            <option value="">Semua Provinsi</option>
            @foreach ($provinsiList as $p)
                <option value="{{ $p->id_provinsi }}" {{ $provinsiId == $p->id_provinsi ? 'selected' : '' }}>
                    {{ $p->nama }}
                </option>
            @endforeach
        </select>

        <input type="date" name="tanggal" class="filter-date-input" value="{{ $tanggal ?? '' }}">

        <button type="submit" class="filter-btn">Terapkan</button>

        @if ($komoditasId || $provinsiId || $tanggal)
            <a href="{{ route('analisis-pasar') }}" style="font-size:12px;color:var(--text-muted)">Reset filter</a>
        @endif
    </form>

    {{-- ── Info Bar ─────────────────────────────────── --}}
    @if ($selectedKomoditas || $selectedProvinsi || $tanggal)
        <div class="info-bar">
            @if ($selectedKomoditas)
                <span>Komoditas: <strong>{{ $selectedKomoditas }}</strong></span>
            @endif
            @if ($selectedProvinsi)
                <span>Provinsi: <strong>{{ $selectedProvinsi }}</strong></span>
            @endif
            @if ($tanggal)
                <span>Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</strong></span>
            @endif
        </div>
    @endif

    {{-- ── Map ──────────────────────────────────────── --}}
    <div class="panel map-panel">
        <div class="panel-title">
            Sebaran Pasar
            <div class="map-legend">
                <span class="map-legend-item">
                    <span class="map-legend-dot" style="background:#16a34a"></span> NULL &le; 5%
                </span>
                <span class="map-legend-item">
                    <span class="map-legend-dot" style="background:#2d3bde"></span> 5% &lt; NULL &le; 8%
                </span>
                <span class="map-legend-item">
                    <span class="map-legend-dot" style="background:#ea580c"></span> 8% &lt; NULL &le; 15%
                </span>
                <span class="map-legend-item">
                    <span class="map-legend-dot" style="background:#dc2626"></span> NULL &gt; 15%
                </span>
                <span class="map-legend-item">
                    <span class="map-legend-dot" style="background:#e5e7eb"></span> Tidak ada data
                </span>
            </div>
        </div>
        <div id="pasarMap"></div>
    </div>

    {{-- ── Table + Progress ─────────────────────────── --}}
    <div class="two-col">
        <div class="panel">
            <div class="panel-title">Rekap Data per Pasar</div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pasar</th>
                            <th>Provinsi</th>
                            <th>Total Record</th>
                            <th>NULL</th>
                            <th>% NULL</th>
                            <th>Rata Harga</th>
                            <th>Update Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasarData as $item)
                            <tr>
                                <td><strong>{{ $item['nama'] }}</strong></td>
                                <td class="text-muted">{{ $item['provinsi'] }}</td>
                                <td class="mono">{{ number_format($item['total_records']) }}</td>
                                <td class="mono">{{ number_format($item['null_records']) }}</td>
                                <td>
                                    @php
                                        $badgeClass = $item['null_pct'] > 15 ? 'high' : ($item['null_pct'] > 5 ? 'medium' : 'low');
                                    @endphp
                                    <span class="badge-null {{ $badgeClass }}">{{ $item['null_pct'] }}%</span>
                                </td>
                                <td class="mono">{{ $item['avg_harga'] ? number_format($item['avg_harga']) : '—' }}</td>
                                <td class="text-muted" style="font-size:12px">
                                    {{ $item['last_update'] ? \Carbon\Carbon::parse($item['last_update'])->format('d/m/Y') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:32px 0;color:var(--text-muted)">
                                    Tidak ada data pasar
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">
                {{ $pasarData->links() }}
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Persentase NULL per Pasar</div>
            <div class="progress-list">
                @forelse ($pasarData as $item)
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">{{ $item['nama'] }}</span>
                            <span class="progress-pct" style="--pct-color:{{ $item['color'] }}">
                                {{ $item['null_pct'] }}%
                            </span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill"
                                style="width:{{ $item['null_pct'] * 4 }}%;--fill-color:{{ $item['color'] }}">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">Tidak ada data pasar</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const pasarData = @json($pasarData->items());

        function getMapColor(item) {
            if (!item.total_records) return '#e5e7eb';
            if (item.null_pct > 15) return '#dc2626';
            if (item.null_pct > 8) return '#ea580c';
            if (item.null_pct > 5) return '#2d3bde';
            return '#16a34a';
        }

        function initPasarMap() {
            const map = L.map('pasarMap').setView([-2.5, 118], 5);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap, &copy; CartoDB',
                subdomains: 'abcd',
                maxZoom: 19,
            }).addTo(map);

            const bounds = [];

            pasarData.forEach(item => {
                if (!item.latitude || !item.longitude) return;

                const lat = parseFloat(item.latitude);
                const lng = parseFloat(item.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                bounds.push([lat, lng]);

                const color = getMapColor(item);

                L.circleMarker([lat, lng], {
                    radius: 8,
                    fillColor: color,
                    fillOpacity: 0.8,
                    color: '#fff',
                    weight: 1.5,
                }).addTo(map).bindPopup(`
                    <div style="font-family:sans-serif;font-size:13px;line-height:1.5">
                        <strong style="font-size:14px">${item.nama}</strong><br>
                        <span style="color:#6b7280">${item.provinsi}</span><br>
                        <hr style="border:none;border-top:1px solid #e5e7eb;margin:6px 0">
                        Record: <strong>${item.total_records.toLocaleString()}</strong><br>
                        NULL: <strong style="color:${color}">${item.null_records.toLocaleString()} (${item.null_pct}%)</strong><br>
                        Rata-rata: <strong>${item.avg_harga ? 'Rp ' + Number(item.avg_harga).toLocaleString() : '—'}</strong>
                    </div>
                `);
            });

            if (bounds.length) {
                map.fitBounds(bounds, { padding: [30, 30] });
            }
        }

        document.addEventListener('DOMContentLoaded', initPasarMap);
    </script>
@endpush
