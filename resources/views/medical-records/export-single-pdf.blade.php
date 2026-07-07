<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekam Medis - {{ $medicalRecord->registration->patient->nama ?? 'Pasien' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 13px;
        }
        .info-table, .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .content-table th, .content-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: top;
        }
        .content-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
            width: 28%;
            font-size: 11px;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .footer {
            margin-top: 45px;
            width: 100%;
            clear: both;
        }
        .ttd-box {
            float: right;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Rumah Sakit Gigi dan Mulut (RSGM)</h2>
        <p>Laporan Detail Rekam Medis Pasien</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>No. RM</strong></td>
            <td width="35%">: {{ $medicalRecord->registration->patient->no_rm ?? '-' }}</td>
            <td width="15%"><strong>Dokter</strong></td>
            <td width="35%">: {{ $medicalRecord->doctor->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Nama Pasien</strong></td>
            <td>: {{ $medicalRecord->registration->patient->nama ?? '-' }}</td>
            <td><strong>Poli Tujuan</strong></td>
            <td>: {{ $medicalRecord->registration->poli ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Lahir</strong></td>
            <td>: {{ $medicalRecord->registration->patient->tgl_lahir ? \Carbon\Carbon::parse($medicalRecord->registration->patient->tgl_lahir)->format('d-m-Y') : '-' }}</td>
            <td><strong>Tgl Kunjungan</strong></td>
            <td>: {{ $medicalRecord->created_at->format('d-m-Y H:i') }}</td>
        </tr>
    </table>

    <div class="section-title">Hasil Pemeriksaan Klinis</div>

    <table class="content-table">
        <tr>
            <th>Anamnesis</th>
            <td>{!! nl2br(e($medicalRecord->anamnesis ?? '-')) !!}</td>
        </tr>
        <tr>
            <th>Pemeriksaan Fisik</th>
            <td>{!! nl2br(e($medicalRecord->pemeriksaan_fisik ?? '-')) !!}</td>
        </tr>
        <tr>
            <th>Diagnosis</th>
            <td>{!! nl2br(e($medicalRecord->diagnosis ?? '-')) !!}</td>
        </tr>
        <tr>
            <th>Tindakan</th>
            <td>{!! nl2br(e($medicalRecord->tindakan ?? '-')) !!}</td>
        </tr>
        @if(!empty($medicalRecord->resep))
        <tr>
            <th>Resep Obat</th>
            <td>{!! nl2br(e($medicalRecord->resep)) !!}</td>
        </tr>
        @endif
        @if(!empty($medicalRecord->catatan))
        <tr>
            <th>Catatan Tambahan</th>
            <td>{!! nl2br(e($medicalRecord->catatan)) !!}</td>
        </tr>
        @endif
    </table>

    @if($medicalRecord->photos && $medicalRecord->photos->count() > 0)
        <table style="width: 100%; margin-top: 25px; border-collapse: collapse;">
            <tr>
                <td style="font-size: 14px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 5px;">
                    Lampiran Foto Klinis
                </td>
            </tr>
            @foreach($medicalRecord->photos as $photo)
                @php
                    $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $photo->file_path), '/');
                    
                    $p1 = public_path('storage/' . $cleanPath);
                    $p2 = storage_path('app/public/' . $cleanPath);
                    $p3 = public_path($cleanPath);

                    $targetFile = null;
                    if (file_exists($p1)) { $targetFile = $p1; }
                    elseif (file_exists($p2)) { $targetFile = $p2; }
                    elseif (file_exists($p3)) { $targetFile = $p3; }
                @endphp

                @if($targetFile)
                    @php
                        $ext = pathinfo($targetFile, PATHINFO_EXTENSION);
                        $data = file_get_contents($targetFile);
                        $base64 = 'data:image/' . $ext . ';base64,' . base64_encode($data);
                    @endphp
                    <tr>
                        <td style="padding-top: 20px; padding-bottom: 15px; text-align: center;">
                            <img src="{{ $base64 }}" style="max-width: 450px; max-height: 500px; border: 1px solid #444; padding: 4px; background-color: #fff;">
                            <div style="font-size: 11px; color: #555; margin-top: 6px; font-style: italic;">
                                {{ $photo->file_name }}
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>
    @endif

    <div class="footer">
        <div class="ttd-box">
            <p>Semarang, {{ now()->translatedFormat('d F Y') }}<br>Dokter Pemeriksa,</p>
            <br><br><br>
            <p><strong><u>{{ $medicalRecord->doctor->name ?? '.......................' }}</u></strong></p>
        </div>
    </div>

</body>
</html>