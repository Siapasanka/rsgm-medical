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

            @if(in_array($role, ['superadmin', 'admin']))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                    <div class="md:col-span-2">
                        @include('dashboard.partials.queue-table', ['fillHeight' => true])
                    </div>

                    <div class="flex flex-col gap-6">
                        @include('dashboard.partials.recent-activities', ['compactActivities' => true])
                        @include('dashboard.partials.trend-7days', ['compactTrend' => true])
                    </div>
                </div>
            @elseif($role === 'dokter')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-8">
                        @include('dashboard.partials.doctor-pending')
                    </div>

                    <div class="lg:col-span-4">
                        @include('dashboard.partials.doctor-recent-records')
                    </div>
                </div>

                @include('dashboard.partials.trend-7days')
            @endif
        </div>
    </div>
</x-app-layout>
