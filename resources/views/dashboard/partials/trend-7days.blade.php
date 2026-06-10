@php
    $maxTotal = max(1, $trend7Days->max('total'));
    $compact = $compactTrend ?? false;
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
    <div class="{{ $compact ? 'px-4 py-3' : 'p-4' }} border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Tren Kunjungan 7 Hari</h3>
        <p class="text-xs text-gray-500">{{ $compact ? 'Jumlah pendaftaran per hari' : 'Ringkasan jumlah pendaftaran per hari' }}</p>
    </div>

    <div class="{{ $compact ? 'px-4 py-3 space-y-2' : 'p-4 space-y-3' }}">
        @forelse($trend7Days as $point)
            @php $width = ($point['total'] / $maxTotal) * 100; @endphp
            <div>
                <div class="flex justify-between text-xs text-gray-600 {{ $compact ? 'mb-0.5' : 'mb-1' }}">
                    <span>{{ $point['label'] }}</span>
                    <span>{{ $point['total'] }}</span>
                </div>
                <div class="{{ $compact ? 'h-1.5' : 'h-2' }} w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500" style="width: {{ $width }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">Belum ada data trend.</p>
        @endforelse
    </div>
</div>
