<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 flex-1 flex flex-col">
    <div class="p-4 border-b border-gray-100 bg-white">
        <h3 class="font-semibold text-gray-800">Rekam Medis Saya Hari Ini</h3>
        <p class="text-xs text-gray-500">10 data terakhir yang kamu kerjakan</p>
    </div>

    <div class="divide-y divide-gray-100 flex-1 flex flex-col">
        @forelse($myRecordsToday as $record)
            <div class="p-4">
                <p class="text-sm text-gray-800 font-medium">{{ $record->registration?->patient?->nama ?? 'Pasien' }}</p>
                <div class="mt-1 text-xs text-gray-500">
                    RM #{{ $record->id }} • Antrian {{ $record->registration?->nomor_antrian ?? '-' }} • {{ $record->created_at?->diffForHumans() }}
                </div>
                <a href="{{ route('medical-records.show', $record) }}" class="inline-block mt-2 text-xs text-blue-600 hover:text-blue-700">Lihat detail</a>
            </div>
        @empty
            <div class="p-4 text-sm text-gray-500 flex-1 flex items-center justify-center text-center min-h-[150px]">
                Belum ada rekam medis yang dibuat hari ini.
            </div>
        @endforelse
    </div>
</div>