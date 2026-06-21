@extends('layouts.app')

@section('title', 'Aktivitas Scraping')

@push('styles')
    <style>
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

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
        }

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
            color: var(--text-muted);
        }

        .table-wrap {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-inner {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead {
            background: var(--bg-muted);
            border-bottom: 1px solid var(--border);
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            white-space: nowrap;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: var(--bg-muted);
        }

        .pagination-wrap {
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--text-muted);
        }

        .pagination-wrap nav {
            display: flex;
            gap: 4px;
        }

        .pagination-wrap nav a,
        .pagination-wrap nav span {
            padding: 5px 10px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            transition: all .15s;
        }

        .pagination-wrap nav a:hover {
            background: var(--primary-10);
            color: var(--primary);
            border-color: var(--primary-20);
        }

        .pagination-wrap nav span[aria-current="page"] {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            font-family: var(--mono);
            letter-spacing: .3px;
        }

        .badge-success {
            background: var(--green-10);
            color: var(--green);
        }

        .badge-failed {
            background: var(--red-10);
            color: var(--red);
        }

        .badge-running {
            background: var(--orange-10);
            color: var(--orange);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: opacity .15s, transform .1s;
        }

        .btn:active {
            transform: scale(.97);
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            opacity: .88;
        }

        .btn-outline {
            background: transparent;
            color: var(--text);
            border-color: var(--border);
        }

        .btn-outline:hover {
            background: var(--bg-muted);
        }

        .table-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-light);
            font-size: 13px;
        }

        .clickable-row {
            cursor: pointer;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: var(--bg-white);
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
            max-width: 640px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalIn .2s ease-out;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
        }

        .modal-header h2 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: var(--text-muted);
            border-radius: 6px;
            display: flex;
            align-items: center;
            transition: background .15s;
        }

        .modal-close:hover {
            background: var(--bg-muted);
        }

        .modal-body {
            padding: 18px 22px 22px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 24px;
        }

        .detail-grid .full-width {
            grid-column: 1 / -1;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .detail-item .label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .detail-item .value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            word-break: break-word;
        }

        .detail-item .value-mono {
            font-family: var(--mono);
            font-size: 13px;
        }

        .detail-section {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--text-light);
            margin: 16px 0 8px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        .detail-section:first-of-type {
            margin-top: 0;
            border-top: none;
            padding-top: 0;
        }

        .key-gold {
            color: #d4a017;
        }

        .key-silver {
            color: #9ca3af;
        }

        .key-icon {
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    {{-- Stat Cards --}}
    <div class="stats-grid">
        <div class="stat-card" style="--card-color:#2d3bde">
            <div class="stat-card-label">Total Scraping Hari Ini</div>
            <div class="stat-card-value">{{ number_format($totalHariIni) }}</div>
        </div>
        <div class="stat-card" style="--card-color:#16a34a">
            <div class="stat-card-label">Total Success</div>
            <div class="stat-card-value">{{ number_format($totalSuccess) }}</div>
        </div>
        <div class="stat-card" style="--card-color:#dc2626">
            <div class="stat-card-label">Total Failed</div>
            <div class="stat-card-value">{{ number_format($totalFailed) }}</div>
        </div>
        <div class="stat-card" style="--card-color:#7c3aed">
            <div class="stat-card-label">Last Scraping</div>
            <div class="stat-card-value" style="font-size:20px;letter-spacing:-.3px">
                {{ $lastScraping ? \Carbon\Carbon::parse($lastScraping)->format('H:i') : '—' }}
            </div>
            <div class="stat-card-sub">
                {{ $lastScraping ? \Carbon\Carbon::parse($lastScraping)->diffForHumans() : 'Belum ada data' }}
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form class="filter-bar" method="GET" action="{{ route('scraping-log') }}">
        <div class="filter-label-row">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            Filter:
        </div>

        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="running" {{ request('status') === 'running' ? 'selected' : '' }}>Running</option>
        </select>

        <select name="provinsi_id" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Provinsi</option>
            @foreach ($provinsiList as $prov)
                <option value="{{ $prov->id_provinsi }}"
                    {{ request('provinsi_id') == $prov->id_provinsi ? 'selected' : '' }}>
                    {{ $prov->nama }}
                </option>
            @endforeach
        </select>

        <input type="date" name="tanggal" class="filter-select" value="{{ request('tanggal') }}"
            onchange="this.form.submit()"
            style="min-width:140px;appearance:auto;background-image:none;padding:7px 12px;cursor:text">

        @if (request()->anyFilled(['status', 'provinsi_id', 'tanggal']))
            <a href="{{ route('scraping-log') }}" class="btn btn-outline" style="padding:6px 12px;font-size:12px">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="table-wrap">
        <div class="table-inner">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Workflow</th>
                        <th>Provinsi</th>
                        <th>Status</th>
                        <th>Total Data</th>
                        <th>Insert</th>
                        <th>Skip</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        @php
                            $logData = json_encode(
                                [
                                    'id' => $log->id,
                                    'workflow_name' => $log->workflow_name,
                                    'provinsi' => $log->provinsi?->nama,
                                    'status' => $log->status,
                                    'total_pasar' => $log->total_pasar,
                                    'total_data' => $log->total_data,
                                    'total_insert' => $log->total_insert,
                                    'total_skip' => $log->total_skip,
                                    'total_insert_pasar' => $log->total_insert_pasar,
                                    'total_gagal' => $log->total_gagal,
                                    'failed_markets' => $log->failed_markets,
                                    'error_message' => $log->error_message,
                                    'started_at' => $log->started_at
                                        ? \Carbon\Carbon::parse($log->started_at)->format('d/m/Y H:i:s')
                                        : null,
                                    'finished_at' => $log->finished_at
                                        ? \Carbon\Carbon::parse($log->finished_at)->format('d/m/Y H:i:s')
                                        : null,
                                    'duration_seconds' => $log->duration_seconds,
                                    'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                                    'updated_at' => $log->updated_at?->format('d/m/Y H:i:s'),
                                ],
                                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP,
                            );

                            $badgeClass = match ($log->status) {
                                'success' => 'badge-success',
                                'failed' => 'badge-failed',
                                'running' => 'badge-running',
                                default => '',
                            };
                        @endphp
                        <tr class="clickable-row" data-log='{!! $logData !!}'
                            onclick="openModal(JSON.parse(this.dataset.log))">
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->workflow_name }}</td>
                            <td>{{ $log->provinsi?->nama ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($log->status) }}</span>
                            </td>
                            <td>{{ number_format($log->total_data ?? 0) }}</td>
                            <td>{{ number_format($log->total_insert ?? 0) }}</td>
                            <td>{{ number_format($log->total_skip ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty">Belum ada data scraping</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            <span>Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} dari
                {{ number_format($logs->total()) }}</span>
            {{ $logs->links() }}
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal-overlay" id="detailModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                    Detail Scraping
                </h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                {{-- populated by JS --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function buildModalContent(log) {
            const badgeClass = {
                success: 'badge-success',
                failed: 'badge-failed',
                running: 'badge-running',
            } [log.status] || '';

            const keyGold = '<span class="key-icon key-gold">&#128273;</span>';
            const keySilver = '<span class="key-icon key-silver">&#128273;</span>';

            return `
                <div class="detail-section">Identitas</div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">${keyGold} ID</div>
                        <div class="value value-mono">${log.id}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Workflow Name</div>
                        <div class="value">${e(log.workflow_name)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">${keySilver} Provinsi</div>
                        <div class="value">${e(log.provinsi || '\u2014')}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Status</div>
                        <div class="value"><span class="badge ${badgeClass}">${log.status.charAt(0).toUpperCase() + log.status.slice(1)}</span></div>
                    </div>
                </div>

                <div class="detail-section">Data</div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">Total Pasar</div>
                        <div class="value value-mono">${n(log.total_pasar)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Total Data</div>
                        <div class="value value-mono">${n(log.total_data)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Total Insert</div>
                        <div class="value value-mono">${n(log.total_insert)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Total Skip</div>
                        <div class="value value-mono">${n(log.total_skip)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Total Insert Pasar</div>
                        <div class="value value-mono">${n(log.total_insert_pasar)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Total Gagal</div>
                        <div class="value value-mono">${n(log.total_gagal)}</div>
                    </div>
                    <div class="detail-item full-width">
                        <div class="label">Failed Markets</div>
                        <div class="value" style="font-weight:400;font-size:13px">${renderFailedMarkets(log.failed_markets)}</div>
                    </div>
                </div>

                ${log.error_message ? `
                    <div class="detail-section">Error</div>
                    <div class="detail-grid">
                        <div class="detail-item full-width">
                            <div class="label">Error Message</div>
                            <div class="value" style="font-weight:400;font-size:13px;color:var(--red);background:var(--red-10);padding:10px 14px;border-radius:var(--radius-sm);font-family:var(--mono);white-space:pre-wrap">${e(log.error_message)}</div>
                        </div>
                    </div>
                    ` : ''}

                <div class="detail-section">Waktu & Durasi</div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">Started At</div>
                        <div class="value value-mono">${log.started_at || '\u2014'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Finished At</div>
                        <div class="value value-mono">${log.finished_at || '\u2014'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Duration</div>
                        <div class="value value-mono">${log.duration_seconds != null ? log.duration_seconds + ' detik' : '\u2014'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Created At</div>
                        <div class="value value-mono">${log.created_at}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Updated At</div>
                        <div class="value value-mono">${log.updated_at || '\u2014'}</div>
                    </div>
                </div>
            `;
        }

        function e(str) {
            if (str == null) return '\u2014';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function n(val) {
            if (val == null) return '\u2014';
            return Number(val).toLocaleString('id-ID');
        }

        function renderFailedMarkets(markets) {
            if (!markets || (Array.isArray(markets) && markets.length === 0)) return '\u2014';
            const list = Array.isArray(markets) ? markets : JSON.parse(markets);
            if (!Array.isArray(list) || list.length === 0) return '\u2014';
            return '<ul style="margin:4px 0 0;padding-left:18px;line-height:1.6">' +
                list.map(function(m) {
                    if (typeof m === 'string') return '<li>' + e(m) + '</li>';
                    if (typeof m === 'object' && m !== null) {
                        const name = m.nama_pasar || m.name || m.nama || JSON.stringify(m);
                        const reason = m.alasan || m.reason || m.error || '';
                        return '<li>' + e(name) + (reason ? ' <span style="color:var(--text-light);font-size:12px">(' +
                            e(reason) + ')</span>' : '') + '</li>';
                    }
                    return '<li>' + e(String(m)) + '</li>';
                }).join('') + '</ul>';
        }

        function openModal(log) {
            document.getElementById('modalBody').innerHTML = buildModalContent(log);
            document.getElementById('detailModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('detailModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('detailModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        });
    </script>
@endpush
