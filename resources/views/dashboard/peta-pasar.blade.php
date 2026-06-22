{{-- resources/views/dashboard/peta-pasar.blade.php --}}
@extends('layouts.app')

@section('title', 'Peta Pasar')

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
            background: #1d2bb3;
        }

        .map-panel {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 24px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 16px;
        }

        .map-panel-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .layer-switch {
            display: flex;
            gap: 4px;
            background: var(--bg);
            padding: 3px;
            border-radius: var(--radius-sm);
        }

        .layer-btn {
            padding: 5px 14px;
            border: none;
            border-radius: 6px;
            font-family: var(--font);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            background: transparent;
            color: var(--text-muted);
            transition: all .15s;
        }

        .layer-btn.active {
            background: var(--bg-white);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .layer-btn:hover:not(.active) {
            color: var(--text);
        }

        #petaPasarMap {
            height: 480px;
            border-radius: var(--radius-sm);
            z-index: 0;
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

        .map-legend-bar {
            width: 16px;
            height: 12px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .stat-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-mini {
            background: var(--bg);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            text-align: center;
        }

        .stat-mini-value {
            font-size: 20px;
            font-weight: 700;
            font-family: var(--mono);
            color: var(--text);
            line-height: 1.2;
        }

        .stat-mini-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .map-popup {
            font-family: var(--font), sans-serif;
            font-size: 13px;
            line-height: 1.5;
            min-width: 160px;
        }

        .map-popup-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .map-popup-sub {
            color: var(--text-muted);
            font-size: 12px;
        }

        .map-popup-divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 6px 0;
        }

        .map-popup-price {
            font-weight: 700;
            font-family: var(--mono);
            color: var(--primary);
        }

        .map-wrap {
            position: relative;
        }

        #mapLoading {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,.65);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            backdrop-filter: blur(1px);
        }

        #mapLoading.active {
            display: flex;
        }

        .map-loading-spinner {
            width: 38px;
            height: 38px;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: mapSpin .7s linear infinite;
        }

        @keyframes mapSpin {
            to { transform: rotate(360deg); }
        }

        .filter-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Peta Pasar &amp; Kabupaten/Kota</h1>
            <p class="page-desc">Visualisasi sebaran harga komoditas per pasar dan per kabupaten/kota.</p>
        </div>
    </div>

    <div class="filter-bar">
        <span class="filter-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            Filter:
        </span>

        <select id="filterKomoditas" class="filter-select">
            @foreach ($komoditasList as $k)
                <option value="{{ $k->id_master_komoditas }}">{{ $k->nama }}</option>
            @endforeach
        </select>

        <select id="filterProvinsi" class="filter-select">
            <option value="">Semua Provinsi</option>
            @foreach ($provinsiList as $p)
                <option value="{{ $p->id_provinsi }}">{{ $p->nama }}</option>
            @endforeach
        </select>

        <input type="date" id="filterTanggal" class="filter-date-input">

        <button id="btnTerapkan" class="filter-btn">Terapkan</button>
    </div>

    <div class="stat-row" id="statRow">
        <div class="stat-mini">
            <div class="stat-mini-value" id="statPasar">—</div>
            <div class="stat-mini-label">Pasar</div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-value" id="statKabupaten">—</div>
            <div class="stat-mini-label">Kabupaten/Kota</div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-value" id="statProvinsi">—</div>
            <div class="stat-mini-label">Provinsi</div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-value" id="statRataHarga">—</div>
            <div class="stat-mini-label">Rata-rata Harga</div>
        </div>
    </div>

    <div class="map-panel">
        <div class="map-panel-title">
            <span>Peta Sebaran Harga</span>
            <div class="layer-switch">
                <button class="layer-btn active" data-layer="kabupaten">Kabupaten/Kota</button>
                <button class="layer-btn" data-layer="pasar">Pasar</button>
                <span style="width:1px;height:18px;background:var(--border);margin:0 4px;flex-shrink:0"></span>
                <button class="layer-btn" data-layer="heatmap">Heatmap</button>
            </div>
        </div>
        <div class="map-wrap">
            <div id="petaPasarMap"></div>
            <div id="mapLoading"><div class="map-loading-spinner"></div></div>
        </div>
        <div class="map-legend" id="mapLegend">
            <span class="map-legend-item" id="legendKab">
                <span class="map-legend-bar" style="background:#86efac"></span> Rendah
                <span class="map-legend-bar" style="background:#fde68a"></span> Sedang
                <span class="map-legend-bar" style="background:#fca5a5"></span> Tinggi
                <span class="map-legend-bar" style="background:#ef4444"></span> Sangat Tinggi
                <span class="map-legend-bar" style="background:#e5e7eb"></span> Tidak Ada Data
            </span>
            <span class="map-legend-item" id="legendPasar" style="display:none">
                <span class="map-legend-dot" style="background:#2d3bde"></span> Pasar dengan data
                <span class="map-legend-dot" style="background:#e5e7eb"></span> Tidak ada data
            </span>
            <span class="map-legend-item" id="legendHeatmap" style="display:none">
                <span class="map-legend-bar" style="background:#86efac"></span> Rendah
                <span class="map-legend-bar" style="background:#fde68a"></span> Sedang
                <span class="map-legend-bar" style="background:#fca5a5"></span> Tinggi
                <span class="map-legend-bar" style="background:#ef4444"></span> Sangat Tinggi
            </span>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script>
        const api = (url) => fetch(url).then(r => r.json());
        const fmt = (n) => n ? 'Rp ' + Number(n).toLocaleString('id-ID') : '—';

        let leafletMap = null;
        let kabLayer = null;
        let kabOutlineLayer = null;
        let pasarLayer = null;
        let heatLayer = null;
        let currentLayer = 'kabupaten';

        function initMap() {
            leafletMap = L.map('petaPasarMap', {
                zoomControl: true
            }).setView([-2.5, 118], 5);

            // 1. Layer Peta Dasar (Abu-abu polos, tanpa teks) ditaruh paling bawah
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap, &copy; CartoDB',
                subdomains: 'abcd',
                maxZoom: 19,
            }).addTo(leafletMap);

            // 2. Buat "Pane" (lapisan) khusus untuk teks agar selalu berada di atas choropleth
            leafletMap.createPane('labels');
            leafletMap.getPane('labels').style.zIndex = 450;
            // Pointer events di-set 'none' agar interaksi (hover/klik popup) tetap tembus ke layer data di bawahnya
            leafletMap.getPane('labels').style.pointerEvents = 'none';

            // 3. Tambahkan Layer khusus Teks (Label Nama Kota/Kabupaten) ke dalam pane tersebut
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
                pane: 'labels',
                subdomains: 'abcd',
                maxZoom: 19,
            }).addTo(leafletMap);
        }

        function getColor(harga, min, max) {
            if (!harga) return '#e5e7eb';
            const ratio = (harga - min) / (max - min || 1);
            if (ratio < 0.25) return '#86efac';
            if (ratio < 0.50) return '#fde68a';
            if (ratio < 0.75) return '#fca5a5';
            return '#ef4444';
        }

        async function loadData() {
            const btn = document.getElementById('btnTerapkan');
            const loading = document.getElementById('mapLoading');
            btn.disabled = true;
            btn.textContent = 'Memuat...';
            loading.classList.add('active');

            try {
                const komoditasId = document.getElementById('filterKomoditas').value;
                const provinsiId = document.getElementById('filterProvinsi').value;
                const tanggal = document.getElementById('filterTanggal').value;
                const komoditasNama = document.getElementById('filterKomoditas').selectedOptions[0].text;

                const [kabRes, pasarRes] = await Promise.all([
                    api(`/api/komoditas/map?komoditas_id=${komoditasId}&tanggal=${tanggal}&level=kabupaten`),
                    api(
                        `/api/komoditas/pasar-map?komoditas_id=${komoditasId}&tanggal=${tanggal}${provinsiId ? '&provinsi_id=' + provinsiId : ''}`
                    ),
                    loadHeatmapData(komoditasNama)
                ]);

                updateStats(kabRes, pasarRes);
                renderKabLayer(kabRes);
                renderKabOutlineLayer(kabRes);
                renderPasarLayer(pasarRes);

                switchLayer(currentLayer);
            } finally {
                btn.disabled = false;
                btn.textContent = 'Terapkan';
                loading.classList.remove('active');
            }
        }

        function updateStats(kabRes, pasarRes) {
            const kabFeatures = kabRes.features || [];
            const pasarFeatures = pasarRes.features || [];

            const provinsiSet = new Set();
            const hargaList = [];

            kabFeatures.forEach(f => {
                const p = f.properties;
                if (p.harga) hargaList.push(p.harga);
            });

            pasarFeatures.forEach(f => {
                const p = f.properties;
                if (p.provinsi_id) provinsiSet.add(p.provinsi_id);
            });

            document.getElementById('statPasar').textContent = pasarFeatures.length.toLocaleString();
            document.getElementById('statKabupaten').textContent = kabFeatures.length.toLocaleString();
            document.getElementById('statProvinsi').textContent = provinsiSet.size.toLocaleString();

            const avg = hargaList.length ?
                Math.round(hargaList.reduce((a, b) => a + b, 0) / hargaList.length) :
                0;
            document.getElementById('statRataHarga').textContent = avg ? fmt(avg) : '—';
        }

        function renderKabLayer(res) {
            if (kabLayer) {
                leafletMap.removeLayer(kabLayer);
            }

            const hargaList = (res.features || [])
                .map(f => f.properties.harga)
                .filter(Boolean);
            const min = hargaList.length ? Math.min(...hargaList) : 0;
            const max = hargaList.length ? Math.max(...hargaList) : 0;

            kabLayer = L.geoJSON(res, {
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
                            <div class="map-popup-name">${p.nama}</div>
                            <div class="map-popup-sub">Kabupaten/Kota</div>
                            <hr class="map-popup-divider">
                            <div class="map-popup-price">${fmt(p.harga)} /kg</div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                                ${p.jumlah_pasar} pasar
                            </div>
                        </div>
                    `);
                    layer.on('mouseover', () => layer.setStyle({
                        fillOpacity: 0.9,
                        weight: 2
                    }));
                    layer.on('mouseout', () => kabLayer.resetStyle(layer));
                }
            });
        }

        function renderPasarLayer(res) {
            if (pasarLayer) {
                leafletMap.removeLayer(pasarLayer);
            }

            pasarLayer = L.geoJSON(res, {
                pointToLayer: (feature, latlng) => {
                    const p = feature.properties;
                    const color = p.harga ? '#2d3bde' : '#e5e7eb';
                    return L.circleMarker(latlng, {
                        radius: 7,
                        fillColor: color,
                        fillOpacity: 0.85,
                        color: '#fff',
                        weight: 1.5,
                    });
                },
                onEachFeature: (feature, layer) => {
                    const p = feature.properties;
                    layer.bindPopup(`
                        <div class="map-popup">
                            <div class="map-popup-name">${p.nama}</div>
                            <div class="map-popup-sub">${p.kabupaten}, ${p.provinsi}</div>
                            <hr class="map-popup-divider">
                            <div class="map-popup-price">${fmt(p.harga)} /kg</div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                                ${p.total_records} record
                            </div>
                        </div>
                    `);
                }
            });
        }

        function renderKabOutlineLayer(res) {
            if (kabOutlineLayer) {
                leafletMap.removeLayer(kabOutlineLayer);
            }
            kabOutlineLayer = L.geoJSON(res, {
                style: {
                    fillColor: 'transparent',
                    fillOpacity: 0,
                    color: '#6b7280',
                    weight: 0.8,
                    opacity: 0.5,
                },
                interactive: false,
            });
            leafletMap.addLayer(kabOutlineLayer);
        }

        function loadHeatmapData(komoditasNama) {
            if (!komoditasNama) {
                komoditasNama = document.getElementById('filterKomoditas').selectedOptions[0].text;
            }
            return api(`/api/komoditas/heatmap?komoditas=${encodeURIComponent(komoditasNama)}`)
                .then(res => {
                    renderHeatmapLayer(res);
                    return res;
                })
                .catch(err => console.error('Gagal memuat heatmap:', err));
        }

        function renderHeatmapLayer(res) {
            if (heatLayer) {
                leafletMap.removeLayer(heatLayer);
                heatLayer = null;
            }

            const features = res.features || [];
            const points = [];

            features.forEach(f => {
                if (f.geometry?.type !== 'Point') return;
                const [lng, lat] = f.geometry.coordinates;
                const val = f.properties?.value ?? f.properties?.harga ?? f.properties?.intensity ?? 1;
                const intensity = val > 0 ? Math.min(val / 50000, 1) : 0.5;
                points.push([lat, lng, intensity]);
            });

            if (points.length) {
                heatLayer = L.heatLayer(points, {
                    radius: 30,
                    blur: 20,
                    maxZoom: 10,
                    gradient: {
                        0.0: '#86efac',
                        0.4: '#fde68a',
                        0.7: '#fca5a5',
                        1.0: '#ef4444'
                    }
                });
            }
        }

        function switchLayer(layer) {
            currentLayer = layer;

            if (layer === 'heatmap') {
                if (kabLayer) leafletMap.removeLayer(kabLayer);
                if (pasarLayer) leafletMap.removeLayer(pasarLayer);
                if (heatLayer) leafletMap.addLayer(heatLayer);
            } else {
                if (heatLayer) leafletMap.removeLayer(heatLayer);

                if (kabLayer) {
                    if (layer === 'kabupaten') {
                        leafletMap.addLayer(kabLayer);
                    } else {
                        leafletMap.removeLayer(kabLayer);
                    }
                }

                if (pasarLayer) {
                    if (layer === 'pasar') {
                        leafletMap.addLayer(pasarLayer);
                    } else {
                        leafletMap.removeLayer(pasarLayer);
                    }
                }
            }

            document.getElementById('legendKab').style.display = layer === 'kabupaten' ? '' : 'none';
            document.getElementById('legendPasar').style.display = layer === 'pasar' ? '' : 'none';
            document.getElementById('legendHeatmap').style.display = layer === 'heatmap' ? '' : 'none';

            document.querySelectorAll('.layer-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.layer === layer);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('filterTanggal').value = new Date().toISOString().slice(0, 10);
            initMap();

            document.querySelectorAll('.layer-btn').forEach(btn => {
                btn.addEventListener('click', () => switchLayer(btn.dataset.layer));
            });

            document.getElementById('btnTerapkan').addEventListener('click', loadData);

            loadData();
        });
    </script>
@endpush
