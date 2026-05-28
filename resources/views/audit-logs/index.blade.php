<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Log</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari action/deskripsi/entity" class="border rounded px-3 py-2">

                    <select name="user_id" class="border rounded px-3 py-2">
                        <option value="">Semua User</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @selected((string)$userId === (string)$u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="border rounded px-3 py-2" title="Dari tanggal">
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="border rounded px-3 py-2" title="Sampai tanggal">

                    <div class="flex gap-2">
                        <button class="bg-gray-700 text-white px-4 py-2 rounded">Filter</button>
                        <a href="{{ route('audit-logs.index') }}" class="bg-gray-200 px-4 py-2 rounded">Reset</a>
                    </div>
                </form>

                <div class="mb-4 flex gap-2">
                    <a href="{{ route('audit-logs.export.csv', request()->query()) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Export CSV
                    </a>
                    <a href="{{ route('audit-logs.export.pdf', request()->query()) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Export PDF
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Waktu</th>
                                <th class="border px-3 py-2 text-left">User</th>
                                <th class="border px-3 py-2 text-left">Action</th>
                                <th class="border px-3 py-2 text-left">Entity</th>
                                <th class="border px-3 py-2 text-left">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                @php
                                    $badgeClass = match($log->action) {
                                        'create' => 'bg-green-100 text-green-800',
                                        'update' => 'bg-yellow-100 text-yellow-800',
                                        'delete' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };

                                    $entityLabel = match($log->entity_type) {
                                        'patient' => 'Pasien',
                                        'registration' => 'Pendaftaran',
                                        'medical_record' => 'Rekam Medis',
                                        'medical_record_photo' => 'Foto Rekam Medis',
                                        default => ucwords(str_replace('_', ' ', (string) $log->entity_type)),
                                    };

                                    $entityBadgeClass = match($log->entity_type) {
                                        'patient' => 'bg-sky-100 text-sky-800',
                                        'registration' => 'bg-violet-100 text-violet-800',
                                        'medical_record' => 'bg-emerald-100 text-emerald-800',
                                        'medical_record_photo' => 'bg-pink-100 text-pink-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <tr>
                                    <td class="border px-3 py-2">{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                                    <td class="border px-3 py-2">{{ $log->user->name ?? 'system' }}</td>
                                    <td class="border px-3 py-2">
                                        <span class="text-xs font-semibold px-2 py-1 rounded {{ $badgeClass }}">{{ strtoupper($log->action) }}</span>
                                    </td>
                                    <td class="border px-3 py-2">
                                        <span class="text-xs font-semibold px-2 py-1 rounded {{ $entityBadgeClass }}">{{ $entityLabel }}</span>
                                        <span class="text-gray-500 ml-1">ID: {{ $log->entity_id }}</span>
                                    </td>
                                    <td class="border px-3 py-2">{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border px-3 py-4 text-center">Belum ada audit log.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
