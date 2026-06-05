{{-- resources/views/dashboard/analisis-null.blade.php --}}
@extends('layouts.app')

@section('title', 'Analisis NULL')

@push('styles')
    <style>
        /* ─── Page Header ─────────────────────────────── */
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
            gap: 10px;
        }

        /* ─── KPI Row ─────────────────────────────────── */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .kpi-item {
            padding: 22px 24px;
            border-right: 1px solid var(--border);
            position: relative;
        }

        .kpi-item:last-child {
            border-right: none;
        }

        .kpi-accent {
            width: 3px;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background: var(--kpi-color, var(--primary));
        }

        .kpi-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .kpi-value {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -1px;
            color: var(--text);
            font-family: var(--mono);
            line-height: 1;
        }

        .kpi-sub {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .kpi-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .kpi-badge.up {
            background: var(--green-10);
            color: var(--green);
        }

        .kpi-badge.warn {
            background: var(--orange-10);
            color: var(--orange);
        }

        .kpi-badge.ok {
            background: var(--green-10);
            color: var(--green);
        }

        /* ─── Two Column Grid ─────────────────────────── */
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
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-subtitle {
            font-size: 11px;
            font-weight: 400;
            color: var(--text-muted);
            margin-left: 6px;
        }

        /* Timeline axis */
        .timeline-axis {
            display: flex;
            justify-content: space-between;
            padding: 0 4px;
            margin-top: 12px;
        }

        .timeline-label {
            font-size: 11px;
            font-family: var(--mono);
            color: var(--text-light);
        }

        .timeline-label.today {
            font-weight: 700;
            color: var(--primary);
        }

        .timeline-chart {
            position: relative;
            height: 180px;
        }

        .timeline-chart canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
        }

        /* Donut chart wrapper */
        .donut-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #nullDonutChart {
            height: 200px;
            width: 200px;
        }

        .donut-legend {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
            margin-top: 16px;
            width: 100%;
        }

        .donut-legend-item {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .donut-dot {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* ─── Bottom Grid ─────────────────────────────── */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Progress bars - NULL per Pasar */
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

        /* ─── Missing Batches Table ────────────────────── */
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

        .null-count {
            font-weight: 700;
            font-family: var(--mono);
            color: var(--red);
        }

        .null-record {
            font-size: 11px;
            color: var(--text-muted);
        }

        .btn-rescrape {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            background: var(--primary-10);
            border: none;
            padding: 4px 10px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background .15s;
        }

        .btn-rescrape:hover {
            background: var(--primary-20);
        }

        /* ─── Pagination ─────────────────────────────── */
        .pagination-wrap {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }

        .pager-summary {
            font-size: 12px;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 10px;
        }

        .pager-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
        }

        .pager-pages {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .pager-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: var(--mono);
            color: var(--text-muted);
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all .15s;
        }

        .pager-link:hover {
            background: var(--bg-muted);
            color: var(--text);
            border-color: var(--text-light);
        }

        .pager-link.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .pager-link.disabled {
            opacity: .35;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────── --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Analisis Integritas Data</h1>
            <p class="page-desc">Audit kualitas data hasil scraping harian dari berbagai pasar komoditas.</p>
        </div>
        <div class="page-actions">
            {{-- <button class="btn btn-outline">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
                </svg>
                Eksport Laporan
            </button>
            <button class="btn btn-primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="23 4 23 10 17 10" />
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                </svg>
                Jalankan Scraping Ulang
            </button> --}}
        </div>
    </div>

    {{-- ── KPI Row ──────────────────────────────────── --}}
    <div class="kpi-row">
        <div class="kpi-item">
            <div class="kpi-accent" style="--kpi-color:#2d3bde"></div>
            <div class="kpi-label">Overall Completion</div>
            <div class="kpi-value">{{ number_format($completionRate, 1) }}%</div>
            <div class="kpi-sub">
                <span class="kpi-badge up">↑ {{ $completionRate >= 80 ? 'Baik' : 'Perlu perhatian' }}</span>
            </div>
        </div>

        <div class="kpi-item">
            <div class="kpi-accent" style="--kpi-color:#dc2626"></div>
            <div class="kpi-label">Total Missing Values</div>
            <div class="kpi-value">{{ number_format($totalNull) }}</div>
            <div class="kpi-sub">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="#ea580c" stroke="none">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                </svg>
                <span style="color:var(--orange)"> dari {{ number_format($totalRecords) }} total records</span>
            </div>
        </div>

        <div class="kpi-item">
            <div class="kpi-accent" style="--kpi-color:#16a34a"></div>
            <div class="kpi-label">Scraping Stability</div>
            <div class="kpi-value">{{ $stabilityLabel }}</div>
            <div class="kpi-sub">
                <span class="kpi-badge ok">✓ {{ $stabilityDesc }}</span>
            </div>
        </div>

        <div class="kpi-item">
            <div class="kpi-accent" style="--kpi-color:#6b7280"></div>
            <div class="kpi-label">Waktu Terakhir Scan</div>
            <div class="kpi-value" id="lastScanTime">{{ $lastScanTime }}</div>
            <div class="kpi-sub">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                <span>Data terakhir tersedia</span>
            </div>
        </div>
    </div>

    {{-- ── Timeline + Donut ─────────────────────────── --}}
    <div class="two-col">
        <div class="panel">
            <div class="panel-title">
                Persentase NULL per Tanggal
                <div class="legend-item"
                    style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted)">
                    <span style="width:24px;height:3px;background:#2d3bde;display:inline-block;border-radius:2px"></span>
                    Stability Index
                </div>
            </div>
            <div class="timeline-chart">
                <canvas id="nullTimelineChart"></canvas>
            </div>
            <div class="timeline-axis">
                @foreach ($recentDays as $day)
                    <span class="timeline-label {{ $loop->last ? 'today' : '' }}">
                        {{ Carbon\Carbon::parse($day->tanggal)->format('d/m') }}
                    </span>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Persentase NULL per Komoditas</div>
            <div class="donut-wrapper">
                <canvas id="nullDonutChart"></canvas>
                <div class="donut-legend">
                    @forelse ($donutLabels as $i => $label)
                        <div class="donut-legend-item">
                            <span class="donut-dot" style="background:{{ $donutColors[$i] ?? '#d1d5db' }}"></span>
                            {{ $label }}
                        </div>
                    @empty
                        <div class="donut-legend-item">
                            <span class="donut-dot" style="background:#d1d5db"></span>
                            Tidak ada data
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bottom: Pasar + Batches ──────────────────── --}}
    <div class="bottom-grid">
        <div class="panel">
            <div class="panel-title">Persentase NULL per Pasar</div>
            <div class="progress-list">
                @forelse ($pasarNull as $item)
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-name">{{ $item['nama'] }}</span>
                            <span class="progress-pct" style="--pct-color:{{ $item['color'] }}">
                                {{ rtrim(rtrim(number_format((float) $item['pct'], 1, '.', ''), '0'), '.') }}%
                            </span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill"
                                style="width:{{ min(100, max(0, (float) $item['pct'])) }}%;--fill-color:{{ $item['color'] }}">
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
            <div class="pagination-wrap">
                @if ($pasarNull->hasPages())
                    <div class="pager-summary">
                        Menampilkan {{ $pasarNull->firstItem() }} sampai {{ $pasarNull->lastItem() }} dari
                        {{ $pasarNull->total() }} pasar
                    </div>
                    <div class="pager-controls">
                        <a href="{{ $pasarNull->previousPageUrl() ?? '#' }}"
                            class="pager-link {{ $pasarNull->onFirstPage() ? 'disabled' : '' }}">Prev</a>

                        <div class="pager-pages">
                            @for ($page = 1; $page <= $pasarNull->lastPage(); $page++)
                                @if ($page == 1 || $page == $pasarNull->lastPage() || abs($page - $pasarNull->currentPage()) <= 1)
                                    <a href="{{ $pasarNull->url($page) }}"
                                        class="pager-link {{ $page === $pasarNull->currentPage() ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @elseif ($page == 2 || $page == $pasarNull->lastPage() - 1)
                                    <span class="pager-link disabled">...</span>
                                @endif
                            @endfor
                        </div>

                        <a href="{{ $pasarNull->nextPageUrl() ?? '#' }}"
                            class="pager-link {{ $pasarNull->hasMorePages() ? '' : 'disabled' }}">Next</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Missing Data Batches</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Sumber</th>
                        <th>Tanggal Batch</th>
                        <th>Jumlah NULL</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($batches as $batch)
                        <tr>
                            <td><strong>{{ $batch['nama'] }}</strong></td>
                            <td style="color:var(--text-muted)">{{ $batch['tgl'] }}</td>
                            <td>
                                <span class="null-count">{{ number_format($batch['null']) }}</span>
                                <span class="null-record"> Records</span>
                            </td>
                            <td>
                                <button class="btn-rescrape">Re-Scrape</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="color:var(--text-muted);text-align:center;padding:24px 0">
                                Tidak ada data NULL
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ── Data dari Server ──────────────────────────────────
        const timelineLabels = @json($timelineLabels);
        const timelineNullPct = @json($timelineNullPct);
        const timelineStability = @json($timelineStability);
        const donutLabels = @json($donutLabels);
        const donutData = @json($donutData);
        const donutColors = @json($donutColors);
        const donutTopLabel = @json($donutTopLabel);
        const donutTopValue = @json($donutTopValue);

        // ── Timeline Chart ─────────────────────────────────────
        const timelineCtx = document.getElementById('nullTimelineChart').getContext('2d');
        new Chart(timelineCtx, {
            type: 'bar',
            data: {
                labels: timelineLabels,
                datasets: [{
                        type: 'bar',
                        label: 'NULL %',
                        data: timelineNullPct,
                        backgroundColor: (ctx) => {
                            const v = ctx.raw;
                            if (v > 8) return 'rgba(220,38,38,.3)';
                            if (v > 6) return 'rgba(234,88,12,.3)';
                            return 'rgba(45,59,222,.2)';
                        },
                        borderColor: (ctx) => {
                            const v = ctx.raw;
                            if (v > 8) return '#dc2626';
                            if (v > 6) return '#ea580c';
                            return '#2d3bde';
                        },
                        borderWidth: 1.5,
                        borderRadius: 4,
                        order: 2,
                    },
                    {
                        type: 'line',
                        label: 'Stability Index',
                        data: timelineStability,
                        borderColor: '#2d3bde',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#2d3bde',
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1',
                        order: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0,0,0,.04)'
                        },
                        ticks: {
                            callback: v => v + '%',
                            font: {
                                family: "'DM Mono'",
                                size: 11
                            },
                            color: '#9ca3af'
                        },
                    },
                    y1: {
                        position: 'right',
                        display: false,
                        min: 80,
                        max: 100
                    }
                }
            }
        });

        // ── Donut Chart ────────────────────────────────────────
        const donutCtx = document.getElementById('nullDonutChart').getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutData,
                    backgroundColor: donutColors,
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.raw}%`
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw(chart) {
                    const {
                        ctx,
                        chartArea: {
                            width,
                            height
                        }
                    } = chart;
                    ctx.save();
                    ctx.font = "700 28px 'DM Mono'";
                    ctx.fillStyle = '#111827';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(donutTopValue + '%', width / 2, height / 2 - 10);
                    ctx.font = "500 11px 'Sora'";
                    ctx.fillStyle = '#9ca3af';
                    ctx.fillText(donutTopLabel.toUpperCase(), width / 2, height / 2 + 16);
                    ctx.restore();
                }
            }]
        });
    </script>
@endpush
