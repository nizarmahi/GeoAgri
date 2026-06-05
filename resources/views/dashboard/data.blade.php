{{-- resources/views/dashboard/data.blade.php --}}
@extends('layouts.app')

@section('title', 'Katalog Data')

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
        }

        .page-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .page-actions {
            display: flex;
            gap: 8px;
        }

        /* ─── Search & Filter Bar ─────────────────────── */
        .search-bar {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
        }

        .search-input-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 8px 12px;
            transition: border-color .15s;
        }

        .search-input-wrap:focus-within {
            border-color: var(--primary);
        }

        .search-input {
            border: none;
            background: transparent;
            font-family: var(--font);
            font-size: 13px;
            color: var(--text);
            width: 100%;
            outline: none;
        }

        .search-input::placeholder {
            color: var(--text-light);
        }

        .sort-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            flex-shrink: 0;
        }

        .sort-select {
            border: none;
            background: transparent;
            font-family: var(--font);
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
            cursor: pointer;
            outline: none;
        }

        /* ─── Data Table ──────────────────────────────── */
        .table-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            background: var(--bg);
            border-bottom: 1px solid var(--border);
        }

        .data-table th {
            padding: 12px 16px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--text-muted);
            text-align: left;
            white-space: nowrap;
        }

        .th-sortable {
            cursor: pointer;
            user-select: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .th-sortable:hover {
            color: var(--text);
        }

        .data-table td {
            padding: 14px 16px;
            font-size: 13px;
            color: var(--text);
            border-bottom: 1px solid var(--bg-muted);
            vertical-align: middle;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background: var(--bg);
        }

        /* Tanggal */
        .td-date {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .td-date-main {
            font-weight: 600;
            color: var(--text);
        }

        /* Komoditas cell */
        .komoditas-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .komoditas-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            background: var(--icon-bg, #f0f1f6);
        }

        .komoditas-name {
            font-weight: 700;
            color: var(--primary);
            font-size: 13px;
        }

        /* Harga */
        .td-harga {
            font-family: var(--mono);
            font-weight: 700;
            font-size: 14px;
            letter-spacing: -.3px;
        }

        /* Satuan */
        .td-satuan {
            color: var(--text-muted);
            font-size: 12px;
        }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family: var(--mono);
            letter-spacing: .5px;
        }

        .status-valid {
            background: #dcfce7;
            color: #15803d;
        }

        .status-null {
            background: #fef3c7;
            color: #b45309;
        }

        /* Action btn */
        .action-btn {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all .15s;
        }

        .action-btn:hover {
            background: var(--bg-muted);
            color: var(--text);
        }

        /* ─── Pagination ──────────────────────────────── */
        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-top: 1px solid var(--border);
            background: var(--bg);
        }

        .table-info {
            font-size: 12px;
            color: var(--text-muted);
        }

        .table-info strong {
            color: var(--text);
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .page-btn {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--bg-white);
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
        }

        .page-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .page-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .page-btn.ellipsis {
            border: none;
            background: none;
            cursor: default;
            color: var(--text-light);
        }

        /* ─── Status Bar ──────────────────────────────── */
        .status-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .status-live-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse 1.8s ease-in-out infinite;
        }
    </style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────── --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Katalog Data Komoditas</h1>
            <p class="page-desc">Manajemen dan kurasi data harga pangan nasional secara real-time.</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-outline">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
                </svg>
                Export CSV
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

    {{-- ── Search & Filter ─────────────────────────── --}}
    <div class="search-bar">
        <div class="search-input-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input class="search-input" id="searchInput" placeholder="Cari komoditas, provinsi, atau pasar..."
                type="text">
        </div>

        <button class="btn btn-outline">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            Filter Lanjutan
        </button>

        <div class="sort-wrap">
            URUTKAN:
            <select class="sort-select" id="sortSelect">
                <option value="terbaru">Terbaru</option>
                <option value="terlama">Terlama</option>
                <option value="harga_asc">Harga ↑</option>
                <option value="harga_desc">Harga ↓</option>
            </select>
        </div>
    </div>

    {{-- ── Table ────────────────────────────────────── --}}
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th><span class="th-sortable">TANGGAL ↕</span></th>
                    <th>PROVINSI</th>
                    <th>PASAR</th>
                    <th>KOMODITAS</th>
                    <th>HARGA (RP)</th>
                    <th>SATUAN</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                {{-- Diisi via JS --}}
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" style="animation:spin 1s linear infinite">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                            </svg>
                            Memuat data...
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="table-footer">
            <div class="table-info">
                Menampilkan <strong id="showingFrom">1</strong>–<strong id="showingTo">10</strong>
                dari <strong id="showingTotal">—</strong> data
            </div>
            <div class="pagination" id="pagination">
                {{-- Diisi via JS --}}
            </div>
        </div>

        <div class="status-bar">
            <div class="status-live-dot"></div>
            SYSTEM LIVE: SINKRONISASI TERAKHIR <span id="syncTime">— MENIT LALU</span>
        </div>
    </div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

@endsection

@push('scripts')
    <script>
        // ── Icon map ───────────────────────────────────────────
        const icons = {
            'Beras': {
                icon: '🍚',
                bg: '#fef9c3'
            },
            'Cabai': {
                icon: '🌶️',
                bg: '#fef2f2'
            },
            'Bawang': {
                icon: '🧅',
                bg: '#fff7ed'
            },
            'Telur': {
                icon: '🥚',
                bg: '#fffbeb'
            },
            'Daging': {
                icon: '🥩',
                bg: '#fdf2f8'
            },
            'Minyak': {
                icon: '🫙',
                bg: '#f0fdf4'
            },
            'Jagung': {
                icon: '🌽',
                bg: '#fefce8'
            },
            'Gula': {
                icon: '🍬',
                bg: '#fff1f2'
            },
            'default': {
                icon: '📦',
                bg: '#f0f1f6'
            },
        };

        function getIcon(nama) {
            for (const [key, val] of Object.entries(icons)) {
                if (nama.toLowerCase().includes(key.toLowerCase())) return val;
            }
            return icons.default;
        }

        function rupiah(n) {
            return n ? Number(n).toLocaleString('id-ID') : '—';
        }

        // ── State ──────────────────────────────────────────────
        let currentPage = 1;
        const perPage = 10;
        let allData = [];
        let filtered = [];

        async function fetchJson(url) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            return response.json();
        }

        // ── Fetch data ─────────────────────────────────────────
        async function loadData() {
            try {
                const to = new Date().toISOString().slice(0, 10);
                const from = new Date(Date.now() - 30 * 86400000).toISOString().slice(0, 10);
                const json = await fetchJson(`/api/komoditas/data?from=${from}&to=${to}&limit=1000`);

                allData = json.data ?? [];
                filtered = [...allData];
                render();

            } catch (e) {
                const tbody = document.getElementById('tableBody');
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
            Gagal memuat data asli dari API.</td></tr>`;
                document.getElementById('showingFrom').textContent = '0';
                document.getElementById('showingTo').textContent = '0';
                document.getElementById('showingTotal').textContent = '0';
                document.getElementById('pagination').innerHTML = '';
                document.getElementById('syncTime').textContent = 'GAGAL';
            }
        }

        // ── Render table ───────────────────────────────────────
        function render() {
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const page = filtered.slice(start, end);

            document.getElementById('showingFrom').textContent = filtered.length ? start + 1 : 0;
            document.getElementById('showingTo').textContent = Math.min(end, filtered.length);
            document.getElementById('showingTotal').textContent = filtered.length.toLocaleString('id-ID');

            const tbody = document.getElementById('tableBody');
            if (!page.length) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
            Tidak ada data ditemukan.</td></tr>`;
                return;
            }

            tbody.innerHTML = page.map(row => {
                const {
                    icon,
                    bg
                } = getIcon(row.komoditas);
                const tgl = new Date(row.tanggal);
                const tglF = tgl.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

                return `
        <tr>
            <td>
                <div class="td-date">
                    <div class="td-date-main">${tglF}</div>
                </div>
            </td>
            <td>${row.provinsi}</td>
            <td>${row.pasar}</td>
            <td>
                <div class="komoditas-cell">
                    <div class="komoditas-icon" style="background:${bg}">${icon}</div>
                    <span class="komoditas-name">${row.komoditas}</span>
                </div>
            </td>
            <td class="td-harga">${rupiah(row.harga)}</td>
            <td class="td-satuan">${row.satuan}</td>
            <td>
                <span class="status-badge ${row.status === 'VALID' ? 'status-valid' : 'status-null'}">
                    ${row.status}
                </span>
            </td>
            <td>
                <button class="action-btn" title="Opsi">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/>
                        <circle cx="12" cy="19" r="1.5"/>
                    </svg>
                </button>
            </td>
        </tr>`;
            }).join('');

            renderPagination();
            document.getElementById('syncTime').textContent = '2 MENIT LALU';
        }

        // ── Render pagination ──────────────────────────────────
        function renderPagination() {
            const total = Math.ceil(filtered.length / perPage);
            const pg = document.getElementById('pagination');

            let html = `
        <button class="page-btn" onclick="goPage(${currentPage-1})" ${currentPage===1?'disabled':''}>‹</button>
    `;

            const pages = [];
            for (let i = 1; i <= total; i++) {
                if (i === 1 || i === total || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    pages.push(i);
                } else if (pages[pages.length - 1] !== '…') {
                    pages.push('…');
                }
            }

            pages.forEach(p => {
                if (p === '…') {
                    html += `<button class="page-btn ellipsis">…</button>`;
                } else {
                    html +=
                        `<button class="page-btn ${p===currentPage?'active':''}" onclick="goPage(${p})">${p}</button>`;
                }
            });

            html +=
                `<button class="page-btn" onclick="goPage(${currentPage+1})" ${currentPage===total?'disabled':''}>›</button>`;
            pg.innerHTML = html;
        }

        function goPage(n) {
            const total = Math.ceil(filtered.length / perPage);
            if (n < 1 || n > total) return;
            currentPage = n;
            render();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // ── Search ─────────────────────────────────────────────
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase();
            filtered = allData.filter(r =>
                r.komoditas.toLowerCase().includes(q) ||
                r.provinsi.toLowerCase().includes(q) ||
                r.pasar.toLowerCase().includes(q)
            );
            currentPage = 1;
            render();
        });

        // ── Sort ───────────────────────────────────────────────
        document.getElementById('sortSelect').addEventListener('change', (e) => {
            const val = e.target.value;
            filtered.sort((a, b) => {
                if (val === 'terbaru') return new Date(b.tanggal) - new Date(a.tanggal);
                if (val === 'terlama') return new Date(a.tanggal) - new Date(b.tanggal);
                if (val === 'harga_asc') return (a.harga || 0) - (b.harga || 0);
                return (b.harga || 0) - (a.harga || 0);
            });
            currentPage = 1;
            render();
        });

        // ── Init ───────────────────────────────────────────────
        loadData();
    </script>
@endpush
