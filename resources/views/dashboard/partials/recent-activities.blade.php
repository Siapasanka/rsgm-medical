@php
    $compact = $compactActivities ?? false;
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
    <div class="{{ $compact ? 'px-4 py-3' : 'p-4' }} border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Aktivitas Terbaru</h3>
        <p class="text-xs text-gray-500">10 aktivitas sistem terakhir</p>
    </div>

    <div class="divide-y divide-gray-100 {{ $compact ? 'max-h-80 overflow-y-auto' : '' }}">
        @forelse($recentActivities as $activity)
            <div class="{{ $compact ? 'px-4 py-3' : 'p-4' }}">
                <p class="{{ $compact ? 'text-xs' : 'text-sm' }} text-gray-800 leading-5">{{ $activity->description }}</p>
                <div class="mt-1 text-xs text-gray-500">
                    {{ $activity->user?->name ?? 'System' }} | {{ $activity->created_at?->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="p-4 text-sm text-gray-500">Belum ada aktivitas.</div>
        @endforelse
    </div>
</div>
