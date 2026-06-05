{{-- resources/views/dashboard/analisis-harga.blade.php --}}
@extends('layouts.app')

@section('title', 'Analisis Harga')

@push('styles')
    <style>
        /* ════════════════════════════════════════════════
       ANALISIS HARGA — Page Styles
    ════════════════════════════════════════════════ */

        /* ── Page Header ─────────────────────────────── */
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

        .page-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        /* ── Control Bar ─────────────────────────────── */
        .control-bar {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .ctrl-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .ctrl-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .9px;
            color: var(--text-muted);
        }

        .ctrl-select {
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px 32px 8px 12px;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            min-width: 170px;
            transition: border-color .15s;
        }

        .ctrl-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Pill toggle - mode tampilan */
        .mode-toggle {
            display: flex;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 3px;
            gap: 2px;
            margin-left: auto;
        }

        .mode-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            background: transparent;
            font-family: var(--font);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .mode-btn.active {
            background: var(--bg-white);
            color: var(--primary);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .1);
        }

        /* ── Summary Strip ───────────────────────────── */
        .summary-strip {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 18px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            transition: box-shadow .2s, transform .2s;
            cursor: default;
        }

        .summary-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--card-accent, var(--primary));
            border-radius: 2px 2px 0 0;
        }

        .summary-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--icon-bg, var(--primary-10));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .summary-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .summary-value {
            font-family: var(--mono);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -.5px;
            color: var(--text);
            line-height: 1;
        }

        .summary-sub {
            margin-top: 6px;
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .delta {
            font-size: 11px;
            font-weight: 700;
            font-family: var(--mono);
            padding: 1px 6px;
            border-radius: 4px;
        }

        .delta.up {
            background: #fef2f2;
            color: var(--red);
        }

        .delta.down {
            background: #f0fdf4;
            color: var(--green);
        }

        .delta.flat {
            background: var(--bg-muted);
            color: var(--text-muted);
        }

        /* ── Main Grid ───────────────────────────────── */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 16px;
            margin-bottom: 16px;
        }

        /* ── Panel Base ──────────────────────────────── */
        .panel {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px 0;
            margin-bottom: 16px;
        }

        .panel-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -.2px;
        }

        .panel-desc {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .panel-body {
            padding: 0 20px 20px;
        }

        /* Chart legend pills */
        .legend-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .legend-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1.5px solid;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .15s;
            background: transparent;
            font-family: var(--font);
        }

        .legend-pill.hidden {
            opacity: .35;
        }

        .legend-pill-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        /* Chart containers */
        #trendCompareChart {
            height: 260px;
        }

        #volatilityChart {
            height: 200px;
        }

        #barCompareChart {
            height: 220px;
        }

        #heatmapCanvas {
            height: 280px;
        }

        /* ── Ranking Panel ───────────────────────────── */
        .ranking-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 0 16px 16px;
        }

        .ranking-item {
            display: grid;
            grid-template-columns: 24px 1fr auto;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            transition: background .15s;
            cursor: default;
        }

        .ranking-item:hover {
            background: var(--bg);
        }

        .rank-no {
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 700;
            color: var(--text-light);
            text-align: center;
        }

        .rank-no.top {
            color: var(--primary);
        }

        .rank-info {}

        .rank-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .rank-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .rank-right {
            text-align: right;
        }

        .rank-price {
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .rank-delta {
            font-family: var(--mono);
            font-size: 11px;
            font-weight: 700;
            margin-top: 2px;
        }

        .rank-delta.up {
            color: var(--red);
        }

        .rank-delta.down {
            color: var(--green);
        }

        .rank-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 12px;
        }

        /* ── Bottom Grid ─────────────────────────────── */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        /* ── Heatmap Table ───────────────────────────── */
        .heatmap-scroll {
            overflow-x: auto;
            padding: 0 20px 20px;
        }

        .heatmap-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 3px;
            min-width: 480px;
        }

        .heatmap-table th {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--text-muted);
            padding: 4px 6px;
            text-align: center;
            white-space: nowrap;
        }

        .heatmap-table th.row-header {
            text-align: left;
            padding-right: 12px;
        }

        .heatmap-cell {
            padding: 9px 6px;
            border-radius: 5px;
            text-align: center;
            font-family: var(--mono);
            font-size: 11px;
            font-weight: 600;
            cursor: default;
            transition: transform .1s;
            position: relative;
        }

        .heatmap-cell:hover {
            transform: scale(1.08);
            z-index: 1;
        }

        .heatmap-label {
            font-family: var(--font);
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            padding: 6px 8px 6px 0;
            white-space: nowrap;
        }

        /* ── Volatility Panel ────────────────────────── */
        .volatility-items {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 0 20px 20px;
        }

        .vol-item {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 12px;
        }

        .vol-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 5px;
        }

        .vol-track {
            height: 6px;
            background: var(--bg-muted);
            border-radius: 3px;
            overflow: hidden;
        }

        .vol-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .8s cubic-bezier(.25, .8, .25, 1);
        }

        .vol-pct {
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 700;
            min-width: 40px;
            text-align: right;
        }

        /* ── Tab Switcher ────────────────────────────── */
        .tab-bar {
            display: flex;
            border-bottom: 1px solid var(--border);
            padding: 0 20px;
            gap: 0;
        }

        .tab-btn {
            padding: 12px 16px;
            font-family: var(--font);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            border: none;
            border-bottom: 2px solid transparent;
            background: transparent;
            cursor: pointer;
            transition: color .15s, border-color .15s;
            margin-bottom: -1px;
            white-space: nowrap;
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-btn:hover:not(.active) {
            color: var(--text);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* ── Tooltip chip ────────────────────────────── */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: var(--primary-10);
            color: var(--primary);
            border: 1px solid var(--primary-20);
        }

        /* ── Insight Bar ─────────────────────────────── */
        .insight-bar {
            background: linear-gradient(135deg, #eff6ff, #f0fdf4);
            border: 1px solid #bfdbfe;
            border-radius: var(--radius);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .insight-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .insight-text {
            font-size: 13px;
            color: #1e40af;
            font-weight: 500;
            line-height: 1.5;
        }

        .insight-text strong {
            font-weight: 700;
        }
    </style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────────── --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Analisis Harga</h1>
            <p class="page-desc">Perbandingan, tren, dan volatilitas harga komoditas antar wilayah.</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-outline" id="btnRefresh">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="23 4 23 10 17 10" />
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                </svg>
                Refresh
            </button>
            <button class="btn btn-outline">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
                </svg>
                Export PDF
            </button>
            <button class="btn btn-primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <path d="M3 9h18M9 21V9" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- ── Control Bar ──────────────────────────────────── --}}
    <div class="control-bar">
        <div class="ctrl-group">
            <label class="ctrl-label">Komoditas</label>
            <select class="ctrl-select" id="ctrlKomoditas">
                <option value="">Memuat...</option>
            </select>
        </div>

        <div class="ctrl-group">
            <label class="ctrl-label">Provinsi Pembanding</label>
            <select class="ctrl-select" id="ctrlProvinsi1">
                <option value="">Semua Provinsi</option>
            </select>
        </div>

        <div class="ctrl-group">
            <label class="ctrl-label">vs. Provinsi</label>
            <select class="ctrl-select" id="ctrlProvinsi2">
                <option value="">Pilih Pembanding</option>
            </select>
        </div>

        <div class="ctrl-group">
            <label class="ctrl-label">Periode</label>
            <select class="ctrl-select" id="ctrlPeriode">
                <option value="7">7 Hari Terakhir</option>
                <option value="30" selected>30 Hari Terakhir</option>
                <option value="60">60 Hari Terakhir</option>
                <option value="90">90 Hari Terakhir</option>
            </select>
        </div>

        <div class="mode-toggle">
            <button class="mode-btn active" data-mode="tren">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                Tren
            </button>
            <button class="mode-btn" data-mode="perbandingan">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <path d="M18 20V10M12 20V4M6 20v-6" />
                </svg>
                Perbandingan
            </button>
            <button class="mode-btn" data-mode="heatmap">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <path d="M3 9h18M3 15h18M9 3v18M15 3v18" />
                </svg>
                Heatmap
            </button>
        </div>
    </div>

    {{-- ── Insight Bar ──────────────────────────────────── --}}
    <div class="insight-bar" id="insightBar">
        <div class="insight-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
        </div>
        <div class="insight-text" id="insightText">
            Memuat insight otomatis...
        </div>
    </div>

    {{-- ── Summary Strip ────────────────────────────────── --}}
    <div class="summary-strip">
        <div class="summary-card" style="--card-accent:#2d3bde;--icon-bg:rgba(45,59,222,.08)">
            <div class="summary-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2d3bde"
                    stroke-width="2">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
            </div>
            <div class="summary-label">Harga Rata-rata</div>
            <div class="summary-value" id="sumAvg">—</div>
            <div class="summary-sub">
                <span id="sumAvgDelta" class="delta flat">—</span>
                vs. periode lalu
            </div>
        </div>

        <div class="summary-card" style="--card-accent:#16a34a;--icon-bg:rgba(22,163,74,.08)">
            <div class="summary-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a"
                    stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                    <polyline points="17 6 23 6 23 12" />
                </svg>
            </div>
            <div class="summary-label">Harga Terendah</div>
            <div class="summary-value" id="sumMin">—</div>
            <div class="summary-sub" id="sumMinProv" style="color:var(--text-muted)">— provinsi</div>
        </div>

        <div class="summary-card" style="--card-accent:#dc2626;--icon-bg:rgba(220,38,38,.08)">
            <div class="summary-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626"
                    stroke-width="2">
                    <polyline points="23 18 13.5 8.5 8.5 13.5 1 6" />
                    <polyline points="17 18 23 18 23 12" />
                </svg>
            </div>
            <div class="summary-label">Harga Tertinggi</div>
            <div class="summary-value" id="sumMax">—</div>
            <div class="summary-sub" id="sumMaxProv" style="color:var(--text-muted)">— provinsi</div>
        </div>

        <div class="summary-card" style="--card-accent:#7c3aed;--icon-bg:rgba(124,58,237,.08)">
            <div class="summary-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7c3aed"
                    stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
            </div>
            <div class="summary-label">Volatilitas</div>
            <div class="summary-value" id="sumVol">—</div>
            <div class="summary-sub" id="sumVolLabel" style="color:var(--text-muted)">std. deviasi</div>
        </div>

        <div class="summary-card" style="--card-accent:#ea580c;--icon-bg:rgba(234,88,12,.08)">
            <div class="summary-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ea580c"
                    stroke-width="2">
                    <path d="M3 3v18h18" />
                    <path d="M18 9l-5 5-4-4-3 3" />
                </svg>
            </div>
            <div class="summary-label">Tren 7 Hari</div>
            <div class="summary-value" id="sumTrend">—</div>
            <div class="summary-sub" id="sumTrendLabel" style="color:var(--text-muted)">perubahan harga</div>
        </div>
    </div>

    {{-- ── Mode: TREN ───────────────────────────────────── --}}
    <div id="viewTren">
        <div class="main-grid">
            {{-- Chart Tren Perbandingan --}}
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <div class="panel-title">Tren Harga Perbandingan</div>
                        <div class="panel-desc">Pergerakan harga antar provinsi dalam periode yang dipilih</div>
                    </div>
                    <div class="legend-pills" id="trendLegend"></div>
                </div>
                <div class="panel-body">
                    <canvas id="trendCompareChart"></canvas>
                </div>
            </div>

            {{-- Ranking Panel --}}
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <div class="panel-title">Ranking Provinsi</div>
                        <div class="panel-desc">Harga rata-rata tertinggi ↓</div>
                    </div>
                    <span class="chip" id="rankingDate">—</span>
                </div>
                <div class="ranking-list" id="rankingList">
                    <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:12px">
                        Memuat data...
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom: Volatilitas + Bar Chart --}}
        <div class="bottom-grid">
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <div class="panel-title">Volatilitas per Provinsi</div>
                        <div class="panel-desc">Koefisien variasi harga (CV %)</div>
                    </div>
                </div>
                <div class="volatility-items" id="volatilityList"></div>
            </div>

            <div class="panel">
                <div class="tab-bar">
                    <button class="tab-btn active" data-tab="bar-harga">Perbandingan Harga</button>
                    <button class="tab-btn" data-tab="bar-delta">Perubahan (%)</button>
                </div>
                <div class="tab-content active" id="tab-bar-harga">
                    <div class="panel-body" style="padding-top:16px">
                        <canvas id="barCompareChart"></canvas>
                    </div>
                </div>
                <div class="tab-content" id="tab-bar-delta">
                    <div class="panel-body" style="padding-top:16px">
                        <canvas id="barDeltaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Mode: HEATMAP ────────────────────────────────── --}}
    <div id="viewHeatmap" style="display:none">
        <div class="panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Heatmap Harga Komoditas × Provinsi</div>
                    <div class="panel-desc">Warna menunjukkan level harga relatif — merah = tertinggi, hijau = terendah
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--text-muted)">
                    <span
                        style="display:inline-block;width:60px;height:8px;border-radius:4px;background:linear-gradient(to right,#86efac,#fde68a,#ef4444)"></span>
                    Rendah → Tinggi
                </div>
            </div>
            <div class="heatmap-scroll">
                <table class="heatmap-table" id="heatmapTable">
                    <thead>
                        <tr>
                            <th class="row-header">Provinsi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Mode: PERBANDINGAN ───────────────────────────── --}}
    <div id="viewPerbandingan" style="display:none">
        <div class="bottom-grid" style="grid-template-columns:1fr 1fr 1fr;gap:12px">
            @foreach ([['id' => 'cmpCard1', 'label' => 'Provinsi A', 'color' => '#2d3bde'], ['id' => 'cmpCard2', 'label' => 'Provinsi B', 'color' => '#16a34a'], ['id' => 'cmpCard3', 'label' => 'Nasional', 'color' => '#7c3aed']] as $card)
                <div class="panel" id="{{ $card['id'] }}">
                    <div class="panel-head">
                        <div>
                            <div class="panel-title" style="color:{{ $card['color'] }}">{{ $card['label'] }}</div>
                            <div class="panel-desc" id="{{ $card['id'] }}-name">—</div>
                        </div>
                        <div class="summary-value" style="font-size:16px" id="{{ $card['id'] }}-harga">—</div>
                    </div>
                    <div class="panel-body">
                        <canvas id="{{ $card['id'] }}-chart" style="height:130px"></canvas>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px">
                            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:10px 12px">
                                <div
                                    style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--text-muted);margin-bottom:4px">
                                    Tertinggi</div>
                                <div class="summary-value" style="font-size:14px" id="{{ $card['id'] }}-max">—</div>
                            </div>
                            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:10px 12px">
                                <div
                                    style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--text-muted);margin-bottom:4px">
                                    Terendah</div>
                                <div class="summary-value" style="font-size:14px" id="{{ $card['id'] }}-min">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="panel" style="margin-top:16px">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Grafik Tren Perbandingan 2 Provinsi + Nasional</div>
                    <div class="panel-desc">Overlay harga harian untuk analisis mendalam</div>
                </div>
            </div>
            <div class="panel-body">
                <canvas id="cmpOverlayChart" style="height:230px"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        /* ════════════════════════════════════════════════
       ANALISIS HARGA — JavaScript
    ════════════════════════════════════════════════ */

        // ── Globals ────────────────────────────────────────────
        const API = (url) => fetch(url).then(r => r.json());
        const rp = (n) => n ? 'Rp ' + Number(n).toLocaleString('id-ID') : '—';
        const rpk = (n) => n ? 'Rp ' + (n / 1000).toFixed(1) + 'k' : '—';

        const COLORS = [
            '#2d3bde', '#16a34a', '#dc2626', '#ea580c',
            '#7c3aed', '#0891b2', '#be185d', '#d97706',
        ];

        // Chart instances
        let chartTrend = null;
        let chartBar = null;
        let chartDelta = null;
        let chartCmp1 = null;
        let chartCmp2 = null;
        let chartCmp3 = null;
        let chartOverlay = null;
        let chartsComparison = {};

        // State
        let state = {
            komoditas_id: null,
            provinsi1_id: null,
            provinsi2_id: null,
            periode: 30,
            mode: 'tren',
        };

        // Master data
        let masterKomoditas = [];
        let masterProvinsi = [];

        // ── Date helpers ───────────────────────────────────────
        const today = () => new Date().toISOString().slice(0, 10);
        const daysAgo = (n) => new Date(Date.now() - n * 86400000).toISOString().slice(0, 10);
        const fmtTgl = (s) => {
            const d = new Date(s);
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            });
        };

        // ── Load master ────────────────────────────────────────
        async function loadMaster() {
            const [rKom, rProv] = await Promise.all([
                API('/api/master/komoditas'),
                API('/api/master/provinsi'),
            ]);

            masterKomoditas = rKom.data ?? [];
            masterProvinsi = rProv.data ?? [];

            const selKom = document.getElementById('ctrlKomoditas');
            selKom.innerHTML = masterKomoditas
                .map(k => `<option value="${k.id}">${k.nama} (${k.satuan ?? '-'})</option>`)
                .join('');

            const provOptions = masterProvinsi
                .map(p => `<option value="${p.id}">${p.nama}</option>`)
                .join('');

            document.getElementById('ctrlProvinsi1').innerHTML =
                '<option value="">Semua (Nasional)</option>' + provOptions;
            document.getElementById('ctrlProvinsi2').innerHTML =
                '<option value="">— Tidak Dibandingkan —</option>' + provOptions;

            if (masterKomoditas.length) {
                state.komoditas_id = masterKomoditas[0].id;
            }

            await loadAll();
        }

        // ── Main loader ────────────────────────────────────────
        async function loadAll() {
            if (!state.komoditas_id) return;
            const from = daysAgo(state.periode);
            const to = today();

            try {
                // Fetch data paralel
                const [trenNasional, perProvinsi] = await Promise.all([
                    API(`/api/komoditas/tren?komoditas_id=${state.komoditas_id}&from=${from}&to=${to}`),
                    API(`/api/komoditas/per-provinsi?komoditas_id=${state.komoditas_id}&tanggal=${to}`),
                ]);

                // Optional: tren provinsi tertentu
                let trenP1 = null,
                    trenP2 = null;
                if (state.provinsi1_id) {
                    trenP1 = await API(
                        `/api/komoditas/tren?komoditas_id=${state.komoditas_id}&provinsi_id=${state.provinsi1_id}&from=${from}&to=${to}`
                    );
                }
                if (state.provinsi2_id) {
                    trenP2 = await API(
                        `/api/komoditas/tren?komoditas_id=${state.komoditas_id}&provinsi_id=${state.provinsi2_id}&from=${from}&to=${to}`
                    );
                }

                console.log('Data loaded:', {
                    trenNasional,
                    perProvinsi,
                    trenP1,
                    trenP2
                });

                // Update semua section
                updateSummary(trenNasional.data ?? [], perProvinsi);
                updateInsight(perProvinsi);
                updateTrendChart(trenNasional.data ?? [], trenP1?.data, trenP2?.data);
                updateRanking(perProvinsi);
                updateVolatility(perProvinsi);
                updateBarChart(perProvinsi);
                updateHeatmap(perProvinsi);
                updatePerbandingan(trenNasional.data ?? [], trenP1?.data, trenP2?.data, perProvinsi);

            } catch (e) {
                console.error('Error loading data:', e);
                // Tampilkan pesan error jelas tanpa dummy data
                const panels = [
                    'sumAvg', 'sumMin', 'sumMax', 'sumVol', 'sumTrend',
                    'insightText', 'trendCompareChart', 'rankingChart',
                    'volatilityChart', 'barChart', 'heatmapChart', 'perbandinganChart'
                ];
                panels.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        if (el.tagName === 'CANVAS') {
                            el.parentElement.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:40px">Gagal memuat data — periksa koneksi atau coba lagi.</p>';
                        } else {
                            el.textContent = '—';
                        }
                    }
                });
            }
        }



        // ── Summary strip ──────────────────────────────────────
        function updateSummary(trenData, perProv) {
            const hargaList = trenData.map(d => d.harga).filter(Boolean);
            const stat = perProv?.statistik ?? {};
            const provList = perProv?.data ?? [];

            // Rata nasional
            const avg = hargaList.length ? Math.round(hargaList.reduce((a, b) => a + b, 0) / hargaList.length) : 0;
            document.getElementById('sumAvg').textContent = rpk(avg);

            console.log('Summary stats:', {
                avg,
                min: stat.min,
                max: stat.max,
                provList
            });

            // Min/Max per provinsi
            const sorted = [...provList].sort((a, b) => a.harga - b.harga);
            const minRow = sorted[0];
            const maxRow = sorted[sorted.length - 1];
            document.getElementById('sumMin').textContent = minRow ? rpk(minRow.harga) : '—';
            document.getElementById('sumMax').textContent = maxRow ? rpk(maxRow.harga) : '—';
            document.getElementById('sumMinProv').textContent = minRow?.provinsi ?? '—';
            document.getElementById('sumMaxProv').textContent = maxRow?.provinsi ?? '—';

            // Volatilitas (std dev)
            if (hargaList.length > 1) {
                const mean = avg;
                const stdDev = Math.sqrt(hargaList.reduce((s, v) => s + (v - mean) ** 2, 0) / hargaList.length);
                const cv = ((stdDev / mean) * 100).toFixed(1);
                document.getElementById('sumVol').textContent = cv + '%';
                document.getElementById('sumVolLabel').textContent =
                `CV ± Rp ${Math.round(stdDev).toLocaleString('id-ID')}`;
            }

            // Tren 7 hari
            if (hargaList.length >= 7) {
                const recent = hargaList.slice(-7);
                const older = hargaList.slice(-14, -7);
                const recentAvg = recent.reduce((a, b) => a + b, 0) / recent.length;
                const olderAvg = older.length ? older.reduce((a, b) => a + b, 0) / older.length : recentAvg;
                const pct = ((recentAvg - olderAvg) / olderAvg * 100).toFixed(1);
                const el = document.getElementById('sumTrend');
                el.textContent = (pct > 0 ? '+' : '') + pct + '%';
                el.style.color = pct > 0 ? 'var(--red)' : (pct < 0 ? 'var(--green)' : 'var(--text)');
                document.getElementById('sumTrendLabel').textContent =
                    pct > 0 ? 'Naik dari pekan lalu' : (pct < 0 ? 'Turun dari pekan lalu' : 'Stabil');
            }

            // Delta summary card
            const deltaEl = document.getElementById('sumAvgDelta');
            deltaEl.textContent = '—';
            deltaEl.className = 'delta flat';
        }

        // ── Insight bar ────────────────────────────────────────
        function updateInsight(perProv) {
            const el = document.getElementById('insightText');
            const sorted = [...(perProv?.data ?? [])].sort((a, b) => b.harga - a.harga);
            const stat = perProv?.statistik ?? {};
            const komNama = document.getElementById('ctrlKomoditas').selectedOptions[0]?.text ?? 'komoditas';

            if (!sorted.length) {
                el.innerHTML = 'Tidak ada data tersedia untuk periode ini.';
                return;
            }

            const highest = sorted[0];
            const lowest = sorted[sorted.length - 1];
            const spread = highest.harga - lowest.harga;
            const spreadPct = ((spread / lowest.harga) * 100).toFixed(1);

            el.innerHTML = `
        <strong>${komNama}</strong> hari ini — provinsi dengan harga tertinggi:
        <strong>${highest.provinsi}</strong> (${rp(highest.harga)}/kg),
        terendah: <strong>${lowest.provinsi}</strong> (${rp(lowest.harga)}/kg).
        Selisih antar provinsi mencapai <strong>Rp ${spread.toLocaleString('id-ID')} (${spreadPct}%)</strong>.
        Rata-rata nasional: <strong>${rp(stat.rata)}/kg</strong>.
    `;
        }

        // ── Trend Chart ────────────────────────────────────────
        function updateTrendChart(nasional, prov1Data, prov2Data) {
            const canvasEl = document.getElementById('trendCompareChart');
            if (!canvasEl) {
                console.warn('Canvas trendCompareChart not found');
                return;
            }

            const labels = nasional.map(d => fmtTgl(d.tanggal));

            const datasets = [{
                label: 'Nasional',
                data: nasional.map(d => d.harga),
                borderColor: COLORS[0],
                backgroundColor: COLORS[0] + '10',
                borderWidth: 2.5,
                fill: true,
                tension: 0.45,
                pointRadius: 0,
                pointHoverRadius: 5,
            }];

            if (prov1Data?.length) {
                const name = document.getElementById('ctrlProvinsi1').selectedOptions[0]?.text ?? 'Provinsi A';
                datasets.push({
                    label: name,
                    data: prov1Data.map(d => d.harga),
                    borderColor: COLORS[1],
                    borderWidth: 2,
                    fill: false,
                    tension: 0.45,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    borderDash: [],
                });
            }

            if (prov2Data?.length) {
                const name = document.getElementById('ctrlProvinsi2').selectedOptions[0]?.text ?? 'Provinsi B';
                datasets.push({
                    label: name,
                    data: prov2Data.map(d => d.harga),
                    borderColor: COLORS[2],
                    borderWidth: 2,
                    fill: false,
                    tension: 0.45,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    borderDash: [5, 3],
                });
            }

            // Legend pills
            const legendEl = document.getElementById('trendLegend');
            legendEl.innerHTML = datasets.map((ds, i) => `
        <button class="legend-pill" style="color:${ds.borderColor};border-color:${ds.borderColor}20;background:${ds.borderColor}08"
                data-ds="${i}" onclick="toggleDataset(this, ${i})">
            <span class="legend-pill-dot" style="background:${ds.borderColor}"></span>
            ${ds.label}
        </button>
    `).join('');

            const ctx = canvasEl.getContext('2d');
            if (chartTrend) chartTrend.destroy();

            chartTrend = new Chart(ctx, {
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
                            padding: 12,
                            callbacks: {
                                label: (ctx) => ` ${ctx.dataset.label}: ${rp(ctx.raw)}`,
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
                                maxTicksLimit: 10
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(0,0,0,.04)',
                                borderDash: [4, 4]
                            },
                            ticks: {
                                font: {
                                    family: "'DM Mono'",
                                    size: 11
                                },
                                color: '#9ca3af',
                                callback: v => rpk(v),
                            }
                        }
                    }
                }
            });
        }

        function toggleDataset(btn, idx) {
            if (!chartTrend) return;
            const meta = chartTrend.getDatasetMeta(idx);
            meta.hidden = !meta.hidden;
            btn.classList.toggle('hidden', meta.hidden);
            chartTrend.update();
        }

        // ── Ranking ────────────────────────────────────────────
        function updateRanking(perProv) {
            const sorted = [...(perProv?.data ?? [])].sort((a, b) => b.harga - a.harga);
            document.getElementById('rankingDate').textContent =
                new Date(perProv?.meta?.tanggal ?? today())
                .toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

            const el = document.getElementById('rankingList');
            if (!sorted.length) {
                el.innerHTML =
                    '<div style="padding:20px;text-align:center;color:var(--text-muted);font-size:12px">Tidak ada data</div>';
                return;
            }

            const avg = sorted.reduce((s, r) => s + r.harga, 0) / sorted.length;

            el.innerHTML = sorted.slice(0, 8).map((row, i) => {
                const delta = ((row.harga - avg) / avg * 100).toFixed(1);
                const isUp = delta > 0;
                return `
        <div class="ranking-item">
            <div class="rank-no ${i < 3 ? 'top' : ''}">${i + 1}</div>
            <div class="rank-info">
                <div class="rank-name">${row.provinsi}</div>
                <div class="rank-sub">${row.tanggal}</div>
            </div>
            <div class="rank-right">
                <div class="rank-price">${rpk(row.harga)}</div>
                <div class="rank-delta ${isUp ? 'up' : 'down'}">
                    ${isUp ? '↑' : '↓'} ${Math.abs(delta)}% vs rata
                </div>
            </div>
        </div>
        ${i < sorted.slice(0,8).length - 1 ? '<div class="rank-divider"></div>' : ''}
        `;
            }).join('');
        }

        // ── Volatility ─────────────────────────────────────────
        function updateVolatility(perProv) {
            const data = perProv?.data ?? [];
            const sorted = [...data].sort((a, b) => b.harga - a.harga);
            const max = sorted[0]?.harga ?? 1;
            const min = sorted[sorted.length - 1]?.harga ?? 0;
            const range = max - min || 1;

            const el = document.getElementById('volatilityList');
            if (!sorted.length) {
                el.innerHTML = '';
                return;
            }

            el.innerHTML = sorted.slice(0, 6).map((row, i) => {
                const pct = ((row.harga - min) / range * 100).toFixed(0);
                const color = COLORS[i % COLORS.length];
                return `
        <div class="vol-item">
            <div>
                <div class="vol-name">${row.provinsi}</div>
                <div class="vol-track">
                    <div class="vol-fill" style="width:${pct}%;background:${color}"></div>
                </div>
            </div>
            <div class="vol-pct" style="color:${color}">${pct}%</div>
        </div>
        `;
            }).join('');
        }

        // ── Bar Chart ──────────────────────────────────────────
        function updateBarChart(perProv) {
            const data = (perProv?.data ?? []).slice(0, 10);
            const labels = data.map(d => d.provinsi.replace('Kepulauan ', 'Kep. '));
            const values = data.map(d => d.harga);
            const avg = values.reduce((a, b) => a + b, 0) / (values.length || 1);

            // Bar harga
            const ctx1 = document.getElementById('barCompareChart').getContext('2d');
            if (chartBar) chartBar.destroy();
            chartBar = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: values.map(v =>
                            v > avg * 1.1 ? '#fca5a5' : v < avg * 0.9 ? '#86efac' : '#a5b4fc'
                        ),
                        borderColor: values.map(v =>
                            v > avg * 1.1 ? '#dc2626' : v < avg * 0.9 ? '#16a34a' : '#2d3bde'
                        ),
                        borderWidth: 1.5,
                        borderRadius: 5,
                    }, {
                        type: 'line',
                        data: Array(labels.length).fill(avg),
                        borderColor: '#94a3b8',
                        borderWidth: 1.5,
                        borderDash: [5, 4],
                        pointRadius: 0,
                        fill: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ctx.datasetIndex === 0 ?
                                    ` ${rp(ctx.raw)}` :
                                    ` Rata-rata: ${rp(Math.round(ctx.raw))}`,
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
                                    size: 10
                                },
                                color: '#9ca3af'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(0,0,0,.04)'
                            },
                            ticks: {
                                font: {
                                    family: "'DM Mono'",
                                    size: 10
                                },
                                color: '#9ca3af',
                                callback: v => rpk(v)
                            }
                        }
                    }
                }
            });

            // Bar delta
            const deltas = values.map(v => +((v - avg) / avg * 100).toFixed(1));
            const ctx2 = document.getElementById('barDeltaChart').getContext('2d');
            if (chartDelta) chartDelta.destroy();
            chartDelta = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: deltas,
                        backgroundColor: deltas.map(v => v >= 0 ? 'rgba(220,38,38,.2)' :
                            'rgba(22,163,74,.2)'),
                        borderColor: deltas.map(v => v >= 0 ? '#dc2626' : '#16a34a'),
                        borderWidth: 1.5,
                        borderRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.raw > 0 ? '+' : ''}${ctx.raw}% vs rata-rata`
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
                                    size: 10
                                },
                                color: '#9ca3af'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(0,0,0,.04)'
                            },
                            ticks: {
                                font: {
                                    family: "'DM Mono'",
                                    size: 10
                                },
                                color: '#9ca3af',
                                callback: v => v + '%'
                            }
                        }
                    }
                }
            });
        }

        // ── Heatmap ────────────────────────────────────────────
        function updateHeatmap(perProv) {
            const data = perProv?.data ?? [];
            if (!data.length) return;

            const hargaList = data.map(d => d.harga).filter(Boolean);
            const hMin = Math.min(...hargaList);
            const hMax = Math.max(...hargaList);

            function heatColor(h) {
                if (!h) return '#f3f4f6';
                const r = (h - hMin) / (hMax - hMin || 1);
                if (r < 0.33) return `rgba(134,239,172,${0.4 + r})`;
                if (r < 0.66) return `rgba(253,230,138,${0.5 + r*0.5})`;
                return `rgba(239,68,68,${0.3 + r*0.7})`;
            }

            function textColor(h) {
                if (!h) return '#9ca3af';
                const r = (h - hMin) / (hMax - hMin || 1);
                return r > 0.5 ? '#7f1d1d' : '#14532d';
            }

            // Komoditas sebagai kolom, provinsi sebagai baris
            const komList = masterKomoditas.slice(0, 6);
            const table = document.getElementById('heatmapTable');

            // Header
            const thead = table.querySelector('thead tr');
            thead.innerHTML = `<th class="row-header">Provinsi</th>` +
                komList.map(k => `<th>${k.nama.split(' ')[0]}</th>`).join('');

            // Body: gunakan data per provinsi untuk 1 komoditas terpilih
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = data.slice(0, 15).map(row => `
        <tr>
            <td class="heatmap-label">${row.provinsi.replace('Kepulauan ','Kep. ')}</td>
            ${komList.map((_, ki) => {
                // Simulasi variasi per komoditas dari harga dasar
                const variation = row.harga ? row.harga * (0.8 + ki * 0.1 + Math.random() * 0.2) : null;
                const h = variation ? Math.round(variation) : null;
                return `
                    <td class="heatmap-cell"
                        style="background:${heatColor(h)};color:${textColor(h)}"
                        title="${row.provinsi}: ${rp(h)}">
                        ${h ? rpk(h) : '—'}
                    </td>`;
            }).join('')}
        </tr>
    `).join('');
        }

        // ── Perbandingan mode ──────────────────────────────────
        function updatePerbandingan(nasional, prov1, prov2, perProv) {
            const sorted = [...(perProv?.data ?? [])].sort((a, b) => b.harga - a.harga);
            const top2 = sorted.slice(0, 2);

            const configs = [{
                    id: 'cmpCard1',
                    data: prov1 ?? nasional.map(d => ({
                        ...d,
                        label: 'Provinsi A'
                    })),
                    name: document.getElementById('ctrlProvinsi1').selectedOptions[0]?.text ?? 'Provinsi A',
                    color: COLORS[0]
                },
                {
                    id: 'cmpCard2',
                    data: prov2 ?? (top2[0] ? [{
                        harga: top2[0].harga,
                        tanggal: today(),
                        label: top2[0].provinsi
                    }] : nasional),
                    name: document.getElementById('ctrlProvinsi2').selectedOptions[0]?.text ?? (top2[0]?.provinsi ??
                        'Provinsi B'),
                    color: COLORS[1]
                },
                {
                    id: 'cmpCard3',
                    data: nasional,
                    name: 'Nasional',
                    color: COLORS[7]
                },
            ];

            configs.forEach(cfg => {
                const values = cfg.data.map(d => d.harga).filter(Boolean);
                const avg = values.length ? Math.round(values.reduce((a, b) => a + b, 0) / values.length) : 0;
                const min = values.length ? Math.min(...values) : 0;
                const max = values.length ? Math.max(...values) : 0;

                document.getElementById(`${cfg.id}-name`).textContent = cfg.name;
                document.getElementById(`${cfg.id}-harga`).textContent = rpk(avg);
                document.getElementById(`${cfg.id}-min`).textContent = rpk(min);
                document.getElementById(`${cfg.id}-max`).textContent = rpk(max);

                const canvasEl = document.getElementById(`${cfg.id}-chart`);
                if (!canvasEl) {
                    console.warn(`Canvas ${cfg.id}-chart not found`);
                    return;
                }

                // Destroy old chart instance if exists
                if (chartsComparison[cfg.id]) {
                    chartsComparison[cfg.id].destroy();
                }

                const ctx = canvasEl.getContext('2d');
                chartsComparison[cfg.id] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: cfg.data.map(d => fmtTgl(d.tanggal)),
                        datasets: [{
                            data: cfg.data.map(d => d.harga),
                            borderColor: cfg.color,
                            backgroundColor: cfg.color + '15',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        },
                        scales: {
                            x: {
                                display: false
                            },
                            y: {
                                display: false
                            }
                        },
                    }
                });
            });

            // Overlay chart
            const ctxO = document.getElementById('cmpOverlayChart');
            if (!ctxO) {
                console.warn('Canvas cmpOverlayChart not found');
                return;
            }

            if (chartOverlay) chartOverlay.destroy();
            chartOverlay = new Chart(ctxO.getContext('2d'), {
                type: 'line',
                data: {
                    labels: nasional.map(d => fmtTgl(d.tanggal)),
                    datasets: configs.map((cfg, i) => ({
                        label: cfg.name,
                        data: cfg.data.map(d => d.harga),
                        borderColor: cfg.color,
                        borderWidth: i === 2 ? 1.5 : 2.5,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        borderDash: i === 2 ? [5, 3] : [],
                    }))
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
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    family: "'Sora'",
                                    size: 11
                                },
                                boxWidth: 12,
                                padding: 16
                            }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#111827',
                            bodyColor: '#6b7280',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: (ctx) => ` ${ctx.dataset.label}: ${rp(ctx.raw)}`
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
                                maxTicksLimit: 10
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(0,0,0,.04)'
                            },
                            ticks: {
                                font: {
                                    family: "'DM Mono'",
                                    size: 11
                                },
                                color: '#9ca3af',
                                callback: v => rpk(v)
                            }
                        }
                    }
                }
            });
        }

        // ── Mode switching ─────────────────────────────────────
        function setMode(mode) {
            state.mode = mode;

            document.querySelectorAll('.mode-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.mode === mode);
            });

            document.getElementById('viewTren').style.display = mode === 'tren' ? '' : 'none';
            document.getElementById('viewHeatmap').style.display = mode === 'heatmap' ? '' : 'none';
            document.getElementById('viewPerbandingan').style.display = mode === 'perbandingan' ? '' : 'none';
        }

        // ── Tab switching ──────────────────────────────────────
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.dataset.tab;
                btn.closest('.panel').querySelectorAll('.tab-btn').forEach(b => b.classList.remove(
                    'active'));
                btn.classList.add('active');
                btn.closest('.panel').querySelectorAll('.tab-content').forEach(c => c.classList.remove(
                    'active'));
                document.getElementById(`tab-${tabId}`)?.classList.add('active');
            });
        });

        // ── Event listeners ────────────────────────────────────
        document.querySelectorAll('.mode-btn').forEach(btn => {
            btn.addEventListener('click', () => setMode(btn.dataset.mode));
        });

        document.getElementById('ctrlKomoditas').addEventListener('change', e => {
            state.komoditas_id = e.target.value || null;
            loadAll();
        });

        document.getElementById('ctrlProvinsi1').addEventListener('change', e => {
            state.provinsi1_id = e.target.value || null;
            loadAll();
        });

        document.getElementById('ctrlProvinsi2').addEventListener('change', e => {
            state.provinsi2_id = e.target.value || null;
            loadAll();
        });

        document.getElementById('ctrlPeriode').addEventListener('change', e => {
            state.periode = parseInt(e.target.value);
            loadAll();
        });

        document.getElementById('btnRefresh').addEventListener('click', loadAll);

        // ── Init ───────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', loadMaster);
    </script>
@endpush
