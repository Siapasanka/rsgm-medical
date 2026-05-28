<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Rekam Medis</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .header { background: #7c3aed; color: #fff; padding: 14px 16px; border-radius: 8px; }
        .title { font-size: 18px; margin: 0; font-weight: 700; }
        .subtitle { margin-top: 3px; font-size: 11px; }
        .info { margin-top: 12px; padding: 10px 12px; border: 1px solid #ddd6fe; background: #f5f3ff; border-radius: 8px; }
        .grid { width: 100%; }
        .grid td { padding: 3px 0; vertical-align: top; }
        .label { font-weight: bold; width: 150px; color: #4c1d95; }
        .section { margin-top: 10px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .section .head { background: #f3f4f6; padding: 8px 10px; font-weight: bold; color: #111827; }
        .section .body { padding: 10px; min-height: 34px; }
        .footer { margin-top: 16px; font-size: 10px; color: #64748b; }
        .photos { margin-top: 10px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .photos .head { background: #f3f4f6; padding: 8px 10px; font-weight: bold; color: #111827; }
        .photos .body { padding: 10px; }
        .photo-item { width: 100%; margin: 0 0 14px 0; page-break-inside: avoid; text-align: center; }
        .photo-item img { display: inline-block; max-width: 100%; width: auto; height: auto; max-height: 520px; margin: 0 auto; border: 1px solid #d1d5db; border-radius: 6px; }
        .photo-caption { font-size: 10px; color: #475569; margin-top: 4px; word-break: break-word; }
        .empty-photo { color: #6b7280; font-style: italic; }
        .sign { margin-top: 30px; width: 260px; float: right; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Ringkasan Rekam Medis</p>
        <div class="subtitle">RSGM UNDIP</div>
    </div>

    <div class="info">
        <table class="grid">
            <tr><td class="label">Dicetak</td><td>: {{ $printedAt->format('d-m-Y H:i:s') }}</td></tr>
            <tr><td class="label">Tanggal Kunjungan</td><td>: {{ $medicalRecord->registration->tanggal_kunjungan?->format('d-m-Y') }}</td></tr>
            <tr><td class="label">No Antrian</td><td>: {{ $medicalRecord->registration->nomor_antrian }}</td></tr>
            <tr><td class="label">Pasien</td><td>: {{ $medicalRecord->registration->patient->nama ?? '-' }}</td></tr>
            <tr><td class="label">Dokter</td><td>: {{ $medicalRecord->doctor->name ?? '-' }}</td></tr>
            <tr><td class="label">Jumlah Foto Klinis</td><td>: {{ $medicalRecord->photos->count() }}</td></tr>
        </table>
    </div>

    <div class="section"><div class="head">Anamnesis</div><div class="body">{{ $medicalRecord->anamnesis ?: '-' }}</div></div>
    <div class="section"><div class="head">Pemeriksaan Fisik</div><div class="body">{{ $medicalRecord->pemeriksaan_fisik ?: '-' }}</div></div>
    <div class="section"><div class="head">Diagnosis</div><div class="body">{{ $medicalRecord->diagnosis ?: '-' }}</div></div>
    <div class="section"><div class="head">Tindakan</div><div class="body">{{ $medicalRecord->tindakan ?: '-' }}</div></div>
    <div class="section"><div class="head">Resep</div><div class="body">{{ $medicalRecord->resep ?: '-' }}</div></div>
    <div class="section"><div class="head">Catatan</div><div class="body">{{ $medicalRecord->catatan ?: '-' }}</div></div>

    <div class="photos">
        <div class="head">Foto Klinis</div>
        <div class="body">
            @forelse($medicalRecord->photos as $photo)
                @php
                    $filePath = storage_path('app/public/' . $photo->file_path);
                    $imageData = null;

                    if (is_file($filePath)) {
                        $mime = $photo->mime_type ?: 'image/jpeg';
                        $imageData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($filePath));
                    }
                @endphp

                <div class="photo-item">
                    @if($imageData)
                        <img src="{{ $imageData }}" alt="{{ $photo->file_name }}">
                    @else
                        <div class="empty-photo">File foto tidak ditemukan: {{ $photo->file_name }}</div>
                    @endif
                    <div class="photo-caption">{{ $photo->file_name }}</div>
                </div>
            @empty
                <div class="empty-photo">Tidak ada foto klinis.</div>
            @endforelse
        </div>
    </div>

    <div class="sign">
        Semarang, {{ $printedAt->format('d-m-Y') }}<br>
        Dokter Pemeriksa,<br><br><br><br>
        <b>{{ $medicalRecord->doctor->name ?? '(............................)' }}</b>
    </div>

    <div style="clear: both"></div>
    <div class="footer">Dokumen sistem RSGM UNDIP</div>

    <script type="text/php">
        if (isset($pdf)) {
            $x = 450;
            $y = 810;
            $text = "Halaman {PAGE_NUM}/{PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $color = [0.39, 0.45, 0.54];
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>
