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
            <option value="failed"  {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="running" {{ request('status') === 'running' ? 'selected' : '' }}>Running</option>
        </select>

        <select name="provinsi_id" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Provinsi</option>
            @foreach ($provinsiList as $prov)
                <option value="{{ $prov->id_provinsi }}" {{ request('provinsi_id') == $prov->id_provinsi ? 'selected' : '' }}>
                    {{ $prov->nama }}
                </option>
            @endforeach
        </select>

        <input type="date" name="tanggal" class="filter-select" value="{{ request('tanggal') }}" onchange="this.form.submit()" style="min-width:140px;appearance:auto;background-image:none;padding:7px 12px;cursor:text">

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
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->workflow_name }}</td>
                            <td>{{ $log->provinsi?->nama ?? '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = match ($log->status) {
                                        'success' => 'badge-success',
                                        'failed'  => 'badge-failed',
                                        'running' => 'badge-running',
                                        default   => '',
                                    };
                                @endphp
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
            <span>Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} dari {{ number_format($logs->total()) }}</span>
            {{ $logs->links() }}
        </div>
    </div>
@endsection
