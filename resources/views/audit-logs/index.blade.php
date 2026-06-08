<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Log</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-5">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat Aktivitas Sistem</h3>
                        <p class="text-sm text-gray-500 mt-1">Aktivitas pengguna ditampilkan dengan nama data yang mudah dikenali.</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('audit-logs.export.csv', request()->query()) }}" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Export CSV
                        </a>
                        <a href="{{ route('audit-logs.export.pdf', request()->query()) }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Export PDF
                        </a>
                    </div>
                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                        <input type="text" name="q" value="{{ $q }}" placeholder="Aksi, deskripsi, pasien" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <select name="user_id" class="w-full border rounded px-3 py-2">
                            <option value="">Semua User</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected((string) $userId === (string) $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="flex gap-2">
                        <button class="bg-gray-700 text-white px-4 py-2 rounded">Filter</button>
                        <a href="{{ route('audit-logs.index') }}" class="bg-gray-200 px-4 py-2 rounded">Reset</a>
                    </div>
                </form>

                <div class="border rounded overflow-hidden">
                    @forelse($logs as $log)
                        @php
                            $badgeClass = match($log->action) {
                                'create' => 'bg-green-100 text-green-800',
                                'update' => 'bg-yellow-100 text-yellow-800',
                                'delete' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };

                            $actionLabel = match($log->action) {
                                'create' => 'Tambah',
                                'update' => 'Ubah',
                                'delete' => 'Hapus',
                                default => ucfirst($log->action),
                            };

                            $entityBadgeClass = match($log->entity_type) {
                                'patient' => 'bg-sky-100 text-sky-800',
                                'registration' => 'bg-violet-100 text-violet-800',
                                'medical_record' => 'bg-emerald-100 text-emerald-800',
                                'medical_record_photo' => 'bg-pink-100 text-pink-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-[150px_1fr] gap-3 p-4 border-b last:border-b-0 hover:bg-gray-50">
                            <div class="text-sm text-gray-600">
                                <div class="font-medium text-gray-900">{{ $log->created_at?->format('d-m-Y') }}</div>
                                <div>{{ $log->created_at?->format('H:i:s') }}</div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-semibold px-2 py-1 rounded {{ $badgeClass }}">{{ $actionLabel }}</span>
                                    <span class="text-xs font-semibold px-2 py-1 rounded {{ $entityBadgeClass }}">{{ $log->entity_title }}</span>
                                    <span class="text-sm text-gray-500">oleh {{ $log->user->name ?? 'system' }}</span>
                                </div>

                                <div class="font-medium text-gray-900">{{ $log->readable_description }}</div>

                                @if($log->entity_subtitle)
                                    <div class="text-sm text-gray-600">{{ $log->entity_subtitle }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">Belum ada audit log.</div>
                    @endforelse
                </div>

                <div>{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
