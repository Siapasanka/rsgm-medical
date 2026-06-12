<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Harian Rekam Medis</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
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
            margin: 5px 0 0;
            font-size: 14px;
        }
        .summary {
            margin-bottom: 15px;
            font-size: 12px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #333;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Rekam Medis Harian</h2>
        <p>Tanggal Kunjungan: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="summary">
        <strong>Ringkasan:</strong> 
        Total Rekam Medis: {{ $summary['total_records'] ?? $summary['total'] ?? count($records) }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No Antrian</th>
                <th width="30%">Pasien</th>
                <th width="20%">Dokter</th>
                <th width="30%">Diagnosis</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">{{ $record->registration->nomor_antrian ?? '-' }}</td>
                    <td>
                        {{ $record->registration->patient->nama ?? '-' }}<br>
                        <small style="color: #555;">RM: {{ $record->registration->patient->no_rm ?? '-' }}</small>
                    </td>
                    <td>{{ $record->doctor->name ?? '-' }}</td>
                    <td>{{ $record->diagnosis ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">
                        Tidak ada data rekam medis pada tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh sistem pada: {{ \Carbon\Carbon::parse($printedAt ?? now())->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>