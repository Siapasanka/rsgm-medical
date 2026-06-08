<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Audit Log</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; vertical-align: top; }
        th { background: #efefef; text-align: left; }
        .muted { color: #444; }
    </style>
</head>
<body>
    <h1>Laporan Audit Log</h1>
    <div class="meta muted">
        Dicetak: {{ $printedAt->format('d-m-Y H:i:s') }}<br>
        Filter: 
        q={{ $q ?: '-' }},
        user_id={{ $userId ?: '-' }},
        dari={{ $dateFrom ?: '-' }},
        sampai={{ $dateTo ?: '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%">Waktu</th>
                <th style="width: 15%">User</th>
                <th style="width: 10%">Aksi</th>
                <th style="width: 15%">Entity</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $log->user->name ?? 'system' }}</td>
                    <td>{{ strtoupper($log->action) }}</td>
                    <td>
                        {{ $log->entity_title }}
                        @if($log->entity_subtitle)
                            <br><span class="muted">{{ $log->entity_subtitle }}</span>
                        @endif
                    </td>
                    <td>{{ $log->readable_description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center">Tidak ada data audit log.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <script type="text/php">
        if (isset($pdf)) {
            $x = 760;
            $y = 565;
            $text = "Halaman {PAGE_NUM}/{PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $color = [0.39, 0.45, 0.54];
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>
