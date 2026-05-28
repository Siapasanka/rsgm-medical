<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendaftaran Harian</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .header { background: #1d4ed8; color: #fff; padding: 14px 16px; border-radius: 8px; }
        .title { font-size: 18px; font-weight: 700; margin: 0; }
        .subtitle { font-size: 11px; margin-top: 3px; opacity: .95; }
        .meta { margin: 12px 0; padding: 10px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; }
        .cards { margin: 12px 0; }
        .card { display: inline-block; width: 23%; margin-right: 1%; padding: 8px; border-radius: 8px; color: #fff; text-align: center; }
        .card b { font-size: 16px; display: block; margin-top: 2px; }
        .card-total { background: #2563eb; }
        .card-menunggu { background: #f59e0b; }
        .card-diperiksa { background: #7c3aed; }
        .card-selesai { background: #059669; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px; vertical-align: top; }
        th { background: #e2e8f0; color: #0f172a; text-align: left; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { padding: 3px 6px; border-radius: 6px; font-size: 10px; color: #fff; }
        .b-menunggu { background: #f59e0b; }
        .b-diperiksa { background: #7c3aed; }
        .b-selesai { background: #059669; }
        .footer { margin-top: 10px; font-size: 10px; color: #64748b; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Laporan Pendaftaran Harian</p>
        <div class="subtitle">RSGM UNDIP</div>
    </div>

    <div class="meta">
        Tanggal Kunjungan: <b>{{ \Illuminate\Support\Carbon::parse($tanggal)->format('d-m-Y') }}</b><br>
        Dicetak pada: <b>{{ $printedAt->format('d-m-Y H:i:s') }}</b>
    </div>

    <div class="cards">
        <div class="card card-total">Total<b>{{ $summary['total'] }}</b></div>
        <div class="card card-menunggu">Menunggu<b>{{ $summary['menunggu'] }}</b></div>
        <div class="card card-diperiksa">Diperiksa<b>{{ $summary['diperiksa'] }}</b></div>
        <div class="card card-selesai">Selesai<b>{{ $summary['selesai'] }}</b></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No Antrian</th>
                <th>Pasien</th>
                <th>Poli</th>
                <th>Status</th>
                <th>Keluhan</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registrations as $r)
                <tr>
                    <td>{{ $r->nomor_antrian }}</td>
                    <td>{{ $r->patient->nama ?? '-' }}</td>
                    <td>{{ $r->poli }}</td>
                    <td>
                        @php
                            $badge = match($r->status_antrian) {
                                'menunggu' => 'b-menunggu',
                                'diperiksa' => 'b-diperiksa',
                                'selesai' => 'b-selesai',
                                default => 'b-menunggu',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ strtoupper($r->status_antrian) }}</span>
                    </td>
                    <td>{{ $r->keluhan_utama ?: '-' }}</td>
                    <td>{{ $r->creator->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada data pendaftaran.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dokumen sistem RSGM UNDIP</div>

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
