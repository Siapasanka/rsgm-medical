<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between gap-2">
        <div>
            <h3 class="font-semibold text-gray-800">Pasien Belum Ada Rekam Medis</h3>
            <p class="text-xs text-gray-500">Prioritas dokter hari ini</p>
        </div>
        <a href="{{ route('medical-records.create') }}" class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded">
            + Buat RM
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Pasien</th>
                    <th class="px-4 py-3 text-left">Poli</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctorPending as $row)
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $row->nomor_antrian }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->patient?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->poli }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('medical-records.create', ['registration_id' => $row->id]) }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                                Buat Rekam Medis
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">Semua pasien hari ini sudah memiliki rekam medis.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
