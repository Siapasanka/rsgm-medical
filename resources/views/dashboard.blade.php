<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            <p class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('dashboard.partials.cards')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8">
                    @if($role === 'dokter')
                        @include('dashboard.partials.doctor-pending')
                    @else
                        @include('dashboard.partials.queue-table')
                    @endif
                </div>

                <div class="lg:col-span-4">
                    @if(in_array($role, ['superadmin', 'admin']))
                        @include('dashboard.partials.recent-activities')
                    @elseif($role === 'petugas')
                        @include('dashboard.partials.quick-actions')
                    @elseif($role === 'dokter')
                        @include('dashboard.partials.doctor-recent-records')
                    @endif
                </div>
            </div>

            @if(in_array($role, ['superadmin', 'admin', 'petugas']))
                @include('dashboard.partials.trend-7days')
            @endif
        </div>
    </div>
</x-app-layout>
