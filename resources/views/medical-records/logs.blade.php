<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Log Rekam Medis</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-5">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat Rekam Medis</h3>
                        <p class="text-sm text-gray-500 mt-1">Catatan siapa yang menambah, mengubah, atau menghapus data.</p>
                    </div>

                    <a href="{{ route('medical-records.index') }}" class="inline-flex items-center justify-center bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">
                        Kembali ke Rekam Medis
                    </a>
                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                    @if($recordId)
                        <input type="hidden" name="record_id" value="{{ $recordId }}">
                    @endif

                    <!-- Cari Catatan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                        <input type="text" name="q" value="{{ $q }}" placeholder="Pasien, No RM, catatan..." class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <!-- Filter Aksi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aksi</label>
                        <select name="action" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="">Semua Aksi</option>
                            <option value="create" @selected($action === 'create')>Tambah</option>
                            <option value="update" @selected($action === 'update')>Ubah</option>
                            <option value="delete" @selected($action === 'delete')>Hapus</option>
                        </select>
                    </div>

                    <!-- Filter User (Sekarang jadi Input Pencarian) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <input type="text" name="user" value="{{ $user }}" placeholder="Nama User..." class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <!-- Filter Tanggal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div class="flex gap-2">
                        <button class="bg-gray-700 text-white px-4 py-2 rounded text-sm">Filter</button>
                        <a href="{{ route('medical-records.logs') }}" class="bg-gray-200 px-4 py-2 rounded text-sm">Reset</a>
                    </div>
                </form>

                @if($recordId)
                    <div class="rounded border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        Menampilkan log untuk satu rekam medis saja. Klik Reset untuk melihat semua log.
                    </div>
                @endif

                <div class="border rounded overflow-hidden">
                    @forelse($logs as $log)
                        @php
                            $record = $recordsById->get((int) $log->entity_id);
                            $metadata = $log->metadata ?? [];
                            $badgeClass = [
                                'create' => 'bg-green-100 text-green-800',
                                'update' => 'bg-yellow-100 text-yellow-800',
                                'delete' => 'bg-red-100 text-red-800'
                            ][$log->action] ?? 'bg-gray-100';
                            $actionLabel = ['create' => 'Tambah', 'update' => 'Ubah', 'delete' => 'Hapus'][$log->action] ?? 'Aksi';
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-[150px_1fr] gap-3 p-4 border-b last:border-b-0 hover:bg-gray-50">
                            <div class="text-sm text-gray-600">
                                <div class="font-medium text-gray-900">{{ $log->created_at?->format('d-m-Y') }}</div>
                                <div>{{ $log->created_at?->format('H:i:s') }}</div>
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $badgeClass }}">{{ $actionLabel }}</span>
                                    <span class="text-sm text-gray-500">oleh <strong>{{ $log->user->name ?? 'system' }}</strong></span>
                                </div>
                                <div class="text-sm text-gray-800 font-medium">{{ $log->description }}</div>
                                
                                <!-- Menampilkan detail perubahan jika ada -->
                                @if(isset($metadata['perubahan']))
                                    <div class="mt-2 text-xs text-gray-600 italic bg-gray-100 p-2 rounded">
                                        Data diubah: {{ implode(', ', array_keys($metadata['perubahan'])) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">Belum ada log rekam medis.</div>
                    @endforelse
                </div>

                <div>{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>