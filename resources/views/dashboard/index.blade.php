{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* ─── Filter Bar ──────────────────────────────── */
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

        .filter-label-row {
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

        .filter-select[multiple] {
            padding: 8px 12px;
            min-width: 220px;
            min-height: 110px;
            background-image: none;
        }

        .komoditas-picker {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg);
            min-width: 260px;
        }

        .komoditas-picker-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            margin-right: 4px;
        }

        .komoditas-chip {
            border: 1px solid var(--border);
            background: var(--bg-white);
            color: var(--text-muted);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s ease;
            white-space: nowrap;
        }

        .komoditas-chip:hover {
            border-color: var(--primary-20);
            color: var(--text);
        }

        .komoditas-chip.active {
            background: var(--primary-10);
            border-color: var(--primary-20);
            color: var(--primary);
        }

        .komoditas-summary {
            width: 100%;
            font-size: 11px;
            color: var(--text-light);
            margin-top: 2px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .filter-date-group {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-left: auto;
        }

        .filter-date-btn {
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--bg);
            font-family: var(--font);
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            transition: all .15s;
        }

        .filter-map-date {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
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

        .filter-date-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .filter-date-btn.active,
        .filter-date-btn:hover {
            background: var(--primary-10);
            color: var(--primary);
            border-color: var(--primary-20);
        }

        /* ─── Stat Cards ──────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 22px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--card-color, var(--primary));
            border-radius: 2px 2px 0 0;
        }

        .stat-card-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -1.5px;
            color: var(--text);
            line-height: 1;
            font-family: var(--mono);
        }

        .stat-card-sub {
            margin-top: 8px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-tag {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .stat-tag.green {
            background: var(--green-10);
            color: var(--green);
        }

        .stat-tag.red {
            background: var(--red-10);
            color: var(--red);
        }

        .stat-tag.orange {
            background: var(--orange-10);
            color: var(--orange);
        }

        /* ─── Content Grid ────────────────────────────── */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 16px;
            margin-bottom: 20px;
            align-items: start;
        }

        /* ─── Chart Card ──────────────────────────────── */
        .chart-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
        }

        .chart-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -.3px;
        }

        .chart-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .chart-legend {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .chart-body {
            position: relative;
            height: 240px;
            flex: 1;
            min-height: 240px;
        }

        .chart-body canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
        }

        /* ─── Alert Card ──────────────────────────────── */
        .alert-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
        }

        .alert-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .alert-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .alert-item {
            border-left: 3px solid var(--item-color, var(--border));
            padding: 10px 12px;
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            background: var(--bg);
            margin-bottom: 10px;
            transition: background .15s;
        }

        .alert-item:hover {
            background: var(--bg-muted);
        }

        .alert-item-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .alert-commodity {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .alert-pct {
            font-size: 12px;
            font-weight: 700;
            font-family: var(--mono);
            color: var(--item-color, var(--text));
        }

        .alert-pasar {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .alert-time {
            display: inline-block;
            margin-top: 5px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-light);
        }

        .btn-full {
            width: 100%;
            justify-content: center;
            margin-top: auto;
            padding-top: 14px;
        }

        /* ─── Map Section ─────────────────────────────── */
        .map-section {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 24px;
            box-shadow: var(--shadow-sm);
        }

        .map-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .map-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -.3px;
        }

        .map-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .scale-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .scale-gradient {
            width: 100px;
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(to right, #dcfce7, #fef08a, #fca5a5, #ef4444);
        }

        #map {
            height: 380px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .map-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            justify-content: flex-end;
        }

        /* Leaflet popup override */
        .leaflet-popup-content-wrapper {
            border-radius: var(--radius-sm) !important;
            border: 1px solid var(--border) !important;
            box-shadow: var(--shadow) !important;
            font-family: var(--font) !important;
        }

        .map-popup {
            padding: 2px;
        }

        .map-popup-prov {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text);
            margin-bottom: 4px;
        }

        .popup-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--red);
        }

        .map-popup-price {
            font-size: 18px;
            font-weight: 700;
            font-family: var(--mono);
            color: var(--text);
            letter-spacing: -.5px;
        }

        .map-popup-note {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }
    </style>
@endpush

@section('content')

    {{-- ── Filter Bar ─────────────────────────────── --}}
    <div class="filter-bar">
        <div class="filter-label-row">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            Filter Data:
        </div>

        <select class="filter-select" id="filterProvinsi">
            <option value="">Semua Provinsi</option>
        </select>

        <select class="filter-select" id="filterPasar">
            <option value="">Semua Pasar</option>
        </select>

        <div class="komoditas-picker" id="filterKomoditas">
            <span class="komoditas-picker-label">Komoditas:</span>
            <span class="komoditas-summary" id="komoditasSummary">Memuat daftar komoditas...</span>
        </div>

        <div class="filter-map-date">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <input type="date" id="filterMapTanggal" class="filter-date-input">
        </div>

        <div class="filter-date-group">
            <button class="filter-date-btn" data-range="7">7 Hari</button>
            <button class="filter-date-btn active" data-range="30">30 Hari</button>
            <button class="filter-date-btn" data-range="90">90 Hari</button>
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────── --}}
    <div class="stats-grid">
        <div class="stat-card" style="--card-color:#2d3bde">
            <div class="stat-card-label">Total Komoditas</div>
            <div class="stat-card-value" id="statKomoditas">—</div>
        </div>

        <div class="stat-card" style="--card-color:#7c3aed">
            <div class="stat-card-label">Total Pasar</div>
            <div class="stat-card-value" id="statPasar">—</div>
            <div class="stat-card-sub" style="color:var(--text-muted)">Nasional</div>
        </div>

        <div class="stat-card" style="--card-color:#16a34a">
            <div class="stat-card-label">Data Valid</div>
            <div class="stat-card-value" id="statValid">—</div>
            <div class="stat-card-sub">
                <span class="stat-tag green" id="statValidPct">— %</span>
            </div>
        </div>

        <div class="stat-card" style="--card-color:#dc2626">
            <div class="stat-card-label">Data NULL</div>
            <div class="stat-card-value" id="statNull">—</div>
            <div class="stat-card-sub">
                <span class="stat-tag red" id="statNullNote">Butuh Sinkron</span>
            </div>
        </div>
    </div>

    {{-- ── Chart + Alert ───────────────────────────── --}}
    <div class="content-grid">

        {{-- Trend Chart --}}
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <div class="chart-title">Tren Harga Komoditas Utama</div>
                    <div class="chart-sub" id="chartSub">Pergerakan harga komoditas yang dipilih (30 Hari)</div>
                </div>
                <div class="chart-legend" id="chartLegend">
                    <div class="legend-item"><span class="legend-dot" style="background:#2d3bde"></span>Beras</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#a5b4fc"></span>Cabai</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#16a34a"></span>Bawang</div>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Alert Panel --}}
        <div class="alert-card">
            <div class="alert-header">
                <div class="alert-title">Peringatan Harga</div>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b"
                    stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
            </div>

            <div class="alert-item" style="--item-color:#dc2626">
                <div class="alert-item-top">
                    <div class="alert-commodity">Cabai Rawit Merah</div>
                    <div class="alert-pct">+12.5%</div>
                </div>
                <div class="alert-pasar">Pasar Induk Kramat Jati, Jakarta</div>
                <span class="alert-time">2 Jam yang lalu</span>
            </div>

            <div class="alert-item" style="--item-color:#ea580c">
                <div class="alert-item-top">
                    <div class="alert-commodity">Bawang Merah</div>
                    <div class="alert-pct">+5.2%</div>
                </div>
                <div class="alert-pasar">Pasar Caringin, Bandung</div>
                <span class="alert-time">5 Jam yang lalu</span>
            </div>

            <div class="alert-item" style="--item-color:#d97706">
                <div class="alert-item-top">
                    <div class="alert-commodity">Daging Sapi</div>
                    <div class="alert-pct">+8.1%</div>
                </div>
                <div class="alert-pasar">Pasar Wonokromo, Surabaya</div>
                <span class="alert-time">Kemarin</span>
            </div>

            <button class="btn btn-outline btn-full">Lihat Semua Laporan</button>
        </div>
    </div>

    {{-- ── Map Section ─────────────────────────────── --}}
    <div class="map-section">
        <div class="map-header">
            <div>
                <div class="map-title">Distribusi Harga Nasional</div>
                <div class="map-sub">Visualisasi sebaran harga berdasarkan rata-rata provinsi</div>
            </div>
            <div class="scale-bar">
                <span>SKALA HARGA:</span>
                <div class="scale-gradient"></div>
            </div>
        </div>

        <div id="map"></div>

        <div class="map-actions">
            <button class="btn btn-outline" onclick="map.setView([-2.5,118],5)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    <line x1="11" y1="8" x2="11" y2="14" />
                    <line x1="8" y1="11" x2="14" y2="11" />
                </svg>
                Perbesar Peta
            </button>
            <button class="btn btn-outline">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
                </svg>
                Ekspor Citra
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ── Helpers ────────────────────────────────────────────
        const api = (url) => fetch(url).then(r => r.json());
        const fmt = (n) => n ? 'Rp ' + Number(n).toLocaleString('id-ID') : '—';

        // ── Filter state ───────────────────────────────────────
        let state = {
            komoditas_ids: [],
            komoditas_id: null,
            provinsi_id: null,
            range: 30
        };

        let komoditasIndex = {};
        let komoditasItems = [];

        function getPrimaryKomoditasId() {
            return state.komoditas_ids[0] ?? null;
        }

        function getKomoditasLabel(id) {
            return komoditasIndex[id] ?? `Komoditas #${id}`;
        }

        function updateChartSubtitle() {
            const chartSub = document.getElementById('chartSub');
            if (!chartSub) return;

            if (!state.komoditas_ids.length) {
                chartSub.textContent = `Pergerakan harga komoditas yang dipilih (${state.range} Hari)`;
                return;
            }

            const labels = state.komoditas_ids.map(getKomoditasLabel);
            const preview = labels.slice(0, 2).join(', ');
            const suffix = labels.length > 2 ? ` +${labels.length - 2} lainnya` : '';
            chartSub.textContent = `Pergerakan harga ${preview}${suffix} (${state.range} Hari)`;
        }

        function updateChartLegend(labels) {
            const chartLegend = document.getElementById('chartLegend');
            if (!chartLegend) return;

            if (!labels.length) {
                chartLegend.innerHTML = '';
                return;
            }

            const palette = ['#2d3bde', '#a5b4fc', '#16a34a', '#ea580c', '#7c3aed'];
            const items = labels.slice(0, 5).map((label, index) => {
                const color = palette[index % palette.length];
                return `<div class="legend-item"><span class="legend-dot" style="background:${color}"></span>${label}</div>`;
            });

            if (labels.length > 5) {
                items.push(
                    `<div class="legend-item"><span class="legend-dot" style="background:#9ca3af"></span>+${labels.length - 5} lainnya</div>`
                );
            }

            chartLegend.innerHTML = items.join('');
        }

        function syncKomoditasPicker() {
            const picker = document.getElementById('filterKomoditas');
            const summary = document.getElementById('komoditasSummary');
            if (!picker) return;

            const chips = picker.querySelectorAll('[data-komoditas-id]');
            chips.forEach(chip => {
                chip.classList.toggle('active', state.komoditas_ids.includes(chip.dataset.komoditasId));
                chip.setAttribute('aria-pressed', state.komoditas_ids.includes(chip.dataset.komoditasId) ? 'true' :
                    'false');
            });

            if (summary) {
                if (!state.komoditas_ids.length) {
                    summary.textContent = 'Pilih satu atau lebih komoditas';
                    return;
                }

                const labels = state.komoditas_ids.map(getKomoditasLabel);
                const preview = labels.slice(0, 2).join(', ');
                summary.textContent = labels.length > 2 ?
                    `${preview} +${labels.length - 2} lainnya dipilih` :
                    `${preview} dipilih`;
            }
        }

        function setSelectedKomoditas(ids) {
            const uniqueIds = Array.from(new Set(ids.map(String)));
            state.komoditas_ids = uniqueIds;
            state.komoditas_id = uniqueIds[0] ?? null;
            syncKomoditasPicker();
            updateChartLegend(state.komoditas_ids.map(getKomoditasLabel));
            updateChartSubtitle();
        }

        // ── Load master data ───────────────────────────────────
        async function loadMaster() {
            const [kom, prov, pas] = await Promise.all([
                api('/api/master/komoditas'),
                api('/api/master/provinsi'),
                api('/api/master/pasar')
            ]);

            const selKom = document.getElementById('filterKomoditas');
            komoditasIndex = {};
            komoditasItems = kom.data;
            selKom.innerHTML =
                '<span class="komoditas-picker-label">Komoditas:</span><span class="komoditas-summary" id="komoditasSummary">Pilih satu atau lebih komoditas</span>';
            kom.data.forEach(k => {
                komoditasIndex[k.id] = `${k.nama} (${k.satuan ?? '-'})`;
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'komoditas-chip';
                chip.textContent = `${k.nama} (${k.satuan ?? '-'})`;
                chip.dataset.komoditasId = String(k.id);
                chip.setAttribute('aria-pressed', 'false');
                selKom.appendChild(chip);
            });

            const selProv = document.getElementById('filterProvinsi');
            prov.data.forEach(p => selProv.add(new Option(p.nama, p.id)));

            const selPasar = document.getElementById('filterPasar');
            pas.data.forEach(p => selPasar.add(new Option(p.nama, p.id)));

            // default: komoditas pertama
            if (kom.data.length) {
                setSelectedKomoditas([kom.data[0].id]);
            } else {
                syncKomoditasPicker();
            }

            loadAll();
        }

        // ── Stat cards ─────────────────────────────────────────
        async function loadStats() {
            // Gunakan endpoint per-provinsi untuk menghitung statistik sederhana
            const primaryKomoditasId = getPrimaryKomoditasId();
            if (!primaryKomoditasId) return;
            const res = await api(`/api/komoditas/per-provinsi?komoditas_id=${primaryKomoditasId}`);
            if (res.status !== 'success') return;

            console.log(res);
            const total = res.meta.total_provinsi;
            document.getElementById('statKomoditas').textContent = res.meta.total_komoditas;
            document.getElementById('statPasar').textContent = res.meta.total_pasar;
            document.getElementById('statValid').textContent = res.meta.data_valid;
            document.getElementById('statValidPct').textContent = '✓ ' + res.meta.data_valid_pct.toFixed(1) + '%';
            document.getElementById('statNull').textContent = res.meta.data_null;
            // document.getElementById('statKomoditas').textContent = '42';
            // document.getElementById('statPasar').textContent = '150';
            // document.getElementById('statValid').textContent = '12.403';
            // document.getElementById('statValidPct').textContent = '✓ 98.2%';
            // document.getElementById('statNull').textContent = '215';
        }

        // ── Trend Chart ────────────────────────────────────────
        let trendChart = null;
        let trendLoadSeq = 0;
        let trendRenderKey = null;

        function renderTrendChart(labels, datasets) {
            const key = JSON.stringify([state.komoditas_ids, state.provinsi_id, state.range, labels, datasets]);

            if (!trendChart) {
                const ctx = document.getElementById('trendChart').getContext('2d');
                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                titleColor: '#111827',
                                bodyColor: '#6b7280',
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                padding: 10,
                                callbacks: {
                                    label: (ctx) => `${ctx.dataset.label}: Rp ${ctx.raw.toLocaleString('id-ID')}`,
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: "'Sora'",
                                        size: 11
                                    },
                                    color: '#9ca3af',
                                    maxTicksLimit: 8
                                }
                            },
                            y: {
                                grid: {
                                    color: 'rgba(0,0,0,.05)'
                                },
                                border: {
                                    dash: [4, 4]
                                },
                                ticks: {
                                    font: {
                                        family: "'DM Mono'",
                                        size: 11
                                    },
                                    color: '#9ca3af',
                                    callback: v => 'Rp ' + (v / 1000).toFixed(0) + 'k'
                                }
                            }
                        }
                    }
                });

                trendRenderKey = key;
                return;
            }

            if (trendRenderKey === key) return;

            trendChart.data.labels = labels;
            trendChart.data.datasets = datasets;
            trendChart.update('none');
            trendRenderKey = key;
        }

        async function loadTrend() {
            const seq = ++trendLoadSeq;
            const selectedIds = state.komoditas_ids.filter(Boolean);
            if (!selectedIds.length) return;
            const to = new Date().toISOString().slice(0, 10);
            const from = new Date(Date.now() - state.range * 86400000).toISOString().slice(0, 10);

            const responses = await Promise.all(selectedIds.map(async (komoditasId) => ({
                komoditasId,
                res: await api(
                    `/api/komoditas/tren?komoditas_id=${komoditasId}&from=${from}&to=${to}`)
            })));

            if (seq !== trendLoadSeq) return;

            const series = responses
                .filter(item => item.res && item.res.status === 'success')
                .map((item, index) => ({
                    label: getKomoditasLabel(item.komoditasId),
                    color: ['#2d3bde', '#a5b4fc', '#16a34a', '#ea580c', '#7c3aed'][index % 5],
                    points: item.res.data.map(d => ({
                        tanggal: d.tanggal,
                        harga: d.harga,
                    })),
                }));

            if (!series.length) return;

            const labelSet = new Set();
            series.forEach(item => item.points.forEach(point => labelSet.add(point.tanggal)));
            const rawLabels = Array.from(labelSet).sort();
            const labels = rawLabels.map(date => {
                const dt = new Date(date);
                return dt.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const datasets = series.map(item => {
                const valueByDate = new Map(item.points.map(point => [point.tanggal, point.harga]));
                return {
                    label: item.label,
                    data: rawLabels.map(date => valueByDate.get(date) ?? null),
                    borderColor: item.color,
                    backgroundColor: item.color,
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    fill: false,
                    tension: 0.45,
                };
            });

            updateChartLegend(series.map(item => item.label));
            renderTrendChart(labels, datasets);
        }

        // ── Leaflet Map ────────────────────────────────────────
        let leafletMap = null;
        let geoLayer = null;

        function initMap() {
            leafletMap = L.map('map', {
                zoomControl: true
            }).setView([-2.5, 118], 5);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap, © CartoDB',
                subdomains: 'abcd',
                maxZoom: 19,
            }).addTo(leafletMap);
        }

        function getColor(harga, min, max) {
            if (!harga) return '#e5e7eb';
            const ratio = (harga - min) / (max - min || 1);
            if (ratio < 0.25) return '#86efac'; // hijau
            if (ratio < 0.50) return '#fde68a'; // kuning
            if (ratio < 0.75) return '#fca5a5'; // merah muda
            return '#ef4444'; // merah
        }

        async function loadMap() {
            const primaryKomoditasId = getPrimaryKomoditasId();
            if (!primaryKomoditasId || !leafletMap) return;

            const tanggal = document.getElementById('filterMapTanggal').value;
            const res = await api(
                `/api/komoditas/map?komoditas_id=${primaryKomoditasId}&tanggal=${tanggal}&level=provinsi`
            );

            if (geoLayer) {
                leafletMap.removeLayer(geoLayer);
            }

            const hargaList = res.features
                .map(f => f.properties.harga)
                .filter(Boolean);
            const min = hargaList.length ? Math.min(...hargaList) : 0;
            const max = hargaList.length ? Math.max(...hargaList) : 0;

            geoLayer = L.geoJSON(res, {
                style: (feature) => ({
                    fillColor: getColor(feature.properties.harga, min, max),
                    fillOpacity: 0.7,
                    color: '#fff',
                    weight: 1.2,
                }),
                onEachFeature: (feature, layer) => {
                    const p = feature.properties;
                    layer.bindPopup(`
                <div class="map-popup">
                    <div class="map-popup-prov">
                        <span class="popup-dot"></span>
                        ${p.nama}
                    </div>
                    <div class="map-popup-price">${fmt(p.harga)} /kg</div>
                    <div class="map-popup-note">
                        ${p.has_data ? 'Di atas rata-rata nasional' : 'Tidak ada data'}
                    </div>
                </div>
            `);
                    layer.on('mouseover', () => layer.setStyle({
                        fillOpacity: 0.9,
                        weight: 2
                    }));
                    layer.on('mouseout', () => geoLayer.resetStyle(layer));
                }
            }).addTo(leafletMap);
        }

        // ── Event listeners ────────────────────────────────────
        document.getElementById('filterKomoditas').addEventListener('click', e => {
            const target = e.target.closest('[data-komoditas-id]');
            if (!target) return;

            const komoditasId = target.dataset.komoditasId;
            const next = state.komoditas_ids.includes(komoditasId) ?
                state.komoditas_ids.filter(id => id !== komoditasId) : [...state.komoditas_ids, komoditasId];

            setSelectedKomoditas(next);
            loadAll();
        });

        document.getElementById('filterProvinsi').addEventListener('change', e => {
            state.provinsi_id = e.target.value || null;
            loadTrend();
        });

        document.querySelectorAll('[data-range]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-range]').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                state.range = parseInt(btn.dataset.range);
                updateChartSubtitle();
                loadTrend();
            });
        });

        function loadAll() {
            loadStats();
            loadTrend();
            loadMap();
        }

        document.getElementById('filterMapTanggal').addEventListener('change', () => {
            loadMap();
        });

        // ── Init ───────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('filterMapTanggal').value = new Date().toISOString().slice(0, 10);
            initMap();
            loadMaster();
        });
    </script>
@endpush
