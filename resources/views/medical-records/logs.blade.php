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
                        <p class="text-sm text-gray-500 mt-1">Catatan siapa yang menambah atau mengubah rekam medis.</p>
                    </div>

                    <a href="{{ route('medical-records.index') }}" class="inline-flex items-center justify-center bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">
                        Kembali ke Rekam Medis
                    </a>
                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                    @if($recordId)
                        <input type="hidden" name="record_id" value="{{ $recordId }}">
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                        <input type="text" name="q" value="{{ $q }}" placeholder="Pasien, No RM, catatan" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aksi</label>
                        <select name="action" class="w-full border rounded px-3 py-2">
                            <option value="">Semua Aksi</option>
                            <option value="create" @selected($action === 'create')>Tambah</option>
                            <option value="update" @selected($action === 'update')>Ubah</option>
                        </select>
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
                        <a href="{{ route('medical-records.logs') }}" class="bg-gray-200 px-4 py-2 rounded">Reset</a>
                    </div>
                </form>

                @if($recordId)
                    <div class="rounded border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        Menampilkan log untuk satu rekam medis. Klik Reset untuk melihat semua log.
                    </div>
                @endif

                <div class="border rounded overflow-hidden">
                    @forelse($logs as $log)
                        @php
                            $record = $recordsById->get((int) $log->entity_id);
                            $metadata = $log->metadata ?? [];
                            $changes = is_array($metadata['perubahan'] ?? null) ? $metadata['perubahan'] : [];
                            $hasStoredChanges = array_key_exists('perubahan', $metadata);
                            $addedPhotoCount = (int) ($metadata['jumlah_foto_ditambah'] ?? 0);
                            $patientName = $record?->registration?->patient?->nama ?? ($metadata['patient_name'] ?? null);
                            $patientNoRm = $record?->registration?->patient?->no_rm ?? ($metadata['patient_no_rm'] ?? null);
                            $nomorAntrian = $record?->registration?->nomor_antrian ?? ($metadata['nomor_antrian'] ?? null);
                            $tanggalKunjungan = $record?->registration?->tanggal_kunjungan?->format('d-m-Y');
                            $doctorName = $record?->doctor?->name ?? '-';
                            $badgeClass = $log->action === 'create'
                                ? 'bg-green-100 text-green-800'
                                : 'bg-yellow-100 text-yellow-800';
                            $actionLabel = $log->action === 'create' ? 'Tambah' : 'Ubah';
                            $fieldLabels = [
                                'anamnesis' => 'Anamnesis',
                                'pemeriksaan_fisik' => 'Pemeriksaan Fisik',
                                'diagnosis' => 'Diagnosis',
                                'tindakan' => 'Tindakan',
                                'resep' => 'Resep',
                                'catatan' => 'Catatan',
                            ];
                            $patientLine = $patientName
                                ? $patientName . ($patientNoRm ? ' (' . $patientNoRm . ')' : '')
                                : 'Data pasien tidak ditemukan';
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-[150px_1fr] gap-3 p-4 border-b last:border-b-0 hover:bg-gray-50">
                            <div class="text-sm text-gray-600">
                                <div class="font-medium text-gray-900">{{ $log->created_at?->format('d-m-Y') }}</div>
                                <div>{{ $log->created_at?->format('H:i:s') }}</div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-semibold px-2 py-1 rounded {{ $badgeClass }}">{{ $actionLabel }}</span>
                                    <span class="text-sm text-gray-500">oleh {{ $log->user->name ?? 'system' }}</span>
                                    <span class="text-sm text-gray-500">Dokter: {{ $doctorName }}</span>
                                </div>

                                <div>
                                    <div class="font-medium text-gray-900">{{ $patientLine }}</div>
                                    <div class="text-sm text-gray-600">
                                        Antrian #{{ $nomorAntrian ?? '-' }}
                                        @if($tanggalKunjungan)
                                            | {{ $tanggalKunjungan }}
                                        @endif
                                    </div>
                                </div>

                                @if($log->action === 'update')
                                    @if(count($changes))
                                        <div class="space-y-2">
                                            @foreach($changes as $field => $change)
                                                @php
                                                    $before = filled($change['before'] ?? null) ? (string) $change['before'] : '-';
                                                    $after = filled($change['after'] ?? null) ? (string) $change['after'] : '-';
                                                @endphp
                                                <div class="border-l-4 border-yellow-300 bg-yellow-50 px-3 py-2">
                                                    <div class="font-medium text-gray-900">{{ $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field)) }}</div>
                                                    <div class="text-sm text-gray-600">
                                                        <span class="text-gray-500">{{ \Illuminate\Support\Str::limit($before, 90) }}</span>
                                                        <span class="text-gray-400">menjadi</span>
                                                        <span class="text-gray-900">{{ \Illuminate\Support\Str::limit($after, 90) }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($hasStoredChanges)
                                        <div class="text-sm text-gray-600">Tidak ada perubahan teks rekam medis.</div>
                                    @else
                                        <div class="text-sm text-gray-600">Detail perubahan belum tersedia untuk log lama.</div>
                                    @endif

                                    @if($addedPhotoCount > 0)
                                        <div class="text-sm text-gray-600">Menambah {{ $addedPhotoCount }} foto.</div>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-700">
                                        Membuat rekam medis untuk {{ $patientLine }}.
                                        @if(isset($metadata['jumlah_foto']))
                                            Jumlah foto: {{ $metadata['jumlah_foto'] }}.
                                        @endif
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
