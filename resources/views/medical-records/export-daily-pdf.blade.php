<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekam Medis Harian</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .header { background: #0f766e; color: #fff; padding: 14px 16px; border-radius: 8px; }
        .title { font-size: 18px; font-weight: 700; margin: 0; }
        .subtitle { font-size: 11px; margin-top: 3px; opacity: .95; }
        .meta { margin: 12px 0; padding: 10px 12px; background: #ecfeff; border: 1px solid #99f6e4; border-radius: 8px; }
        .cards { margin: 12px 0; }
        .card { display: inline-block; width: 32%; margin-right: 1%; padding: 8px; border-radius: 8px; color: #fff; text-align: center; }
        .card b { font-size: 16px; display: block; margin-top: 2px; }
        .card-total { background: #0ea5e9; }
        .card-foto { background: #f97316; }
        .card-note { background: #334155; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px; vertical-align: top; }
        th { background: #e2e8f0; color: #0f172a; text-align: left; }
        tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin-top: 10px; font-size: 10px; color: #64748b; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Laporan Rekam Medis Harian</p>
        <div class="subtitle">RSGM UNDIP</div>
    </div>

    <div class="meta">
        Tanggal Kunjungan: <b>{{ \Illuminate\Support\Carbon::parse($tanggal)->format('d-m-Y') }}</b><br>
        Dicetak pada: <b>{{ $printedAt->format('d-m-Y H:i:s') }}</b>
    </div>

    <div class="cards">
        <div class="card card-total">Total Rekam Medis<b>{{ $summary['total'] }}</b></div>
        <div class="card card-foto">Total Foto<b>{{ $summary['total_foto'] }}</b></div>
        <div class="card card-note">Laporan<b>Harian</b></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Antrian</th>
                <th>Pasien</th>
                <th>Dokter</th>
                <th>Diagnosis</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $idx => $r)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $r->registration->nomor_antrian }}</td>
                    <td>{{ $r->registration->patient->nama ?? '-' }}</td>
                    <td>{{ $r->doctor->name ?? '-' }}</td>
                    <td>{{ $r->diagnosis ?: '-' }}</td>
                    <td>{{ $r->photos->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada data rekam medis.</td>
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
