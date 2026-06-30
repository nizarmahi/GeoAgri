@props([
    'panelId',
    'height' => 240,
    'komoditasIds' => [],
    'provinsiId' => '',
    'range' => 30,
    'tab' => '',
])

@php
    $base = config('grafana.url') . '/d-solo/' . config('grafana.dashboard_uid') . '/dashboard-geoagri';
    $rangeMap = [7 => 'now-7d', 30 => 'now-30d', 60 => 'now-60d', 90 => 'now-90d'];
    $from = $rangeMap[$range] ?? 'now-30d';
    $params = http_build_query([
        'orgId' => 1,
        'from' => $from,
        'to' => 'now',
        'timezone' => 'browser',
        'theme' => 'light',
        'kiosk' => '',
        'panelId' => $panelId,
    ]);
    if ($tab) {
        $params .= '&dtab=' . urlencode($tab);
    }
    // Multi-value komoditas
    if (is_array($komoditasIds)) {
        foreach ($komoditasIds as $id) {
            $params .= '&var-komoditas=' . urlencode($id);
        }
    } elseif ($komoditasIds) {
        $params .= '&var-komoditas=' . urlencode($komoditasIds);
    }
    if ($provinsiId) {
        $params .= '&var-provinsi=' . urlencode($provinsiId);
    }
@endphp

<iframe id="grafana-{{ $panelId }}"
    src="{{ $base }}?{{ $params }}"
    width="100%"
    height="{{ $height }}"
    frameborder="0"
    style="border-radius:var(--radius-sm);background:#fff;display:block;"
    allow="cross-origin-isolated"
></iframe>
