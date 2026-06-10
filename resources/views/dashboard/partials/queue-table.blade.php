<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 {{ ($fillHeight ?? false) ? 'h-full flex flex-col' : '' }}">
    <div class="p-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Antrian Hari Ini</h3>
        <p class="text-xs text-gray-500">Urut berdasarkan nomor antrian</p>
    </div>

    <div class="overflow-x-auto {{ ($fillHeight ?? false) ? 'flex-1' : '' }}">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Pasien</th>
                    <th class="px-4 py-3 text-left">Poli</th>
                    <th class="px-4 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($queueToday as $row)
                    @php
                        $statusClass = match($row->status_antrian) {
                            'menunggu' => 'bg-amber-100 text-amber-700',
                            'diperiksa' => 'bg-blue-100 text-blue-700',
                            'selesai' => 'bg-emerald-100 text-emerald-700',
                            default => 'bg-gray-100 text-gray-700'
                        };
                    @endphp
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $row->nomor_antrian }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->patient?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->poli }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold uppercase {{ $statusClass }}">
                                {{ $row->status_antrian }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada antrian hari ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
