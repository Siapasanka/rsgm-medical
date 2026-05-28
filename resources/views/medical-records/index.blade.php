<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rekam Medis</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                    <form method="GET" class="flex gap-2 items-center">
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="border rounded px-3 py-2">
                        <button class="bg-gray-700 text-white px-4 py-2 rounded">Filter</button>
                    </form>

                    <div class="flex gap-2">
                        <a href="{{ route('medical-records.export.daily-pdf', ['tanggal' => $tanggal]) }}" class="bg-green-600 text-white px-4 py-2 rounded text-center">
                            Print PDF Harian
                        </a>
                        <a href="{{ route('medical-records.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-center">
                            + Tambah Rekam Medis
                        </a>
                    </div>
                </div>

                @if($registrationsWithoutRecord->count())
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="font-semibold text-yellow-800">Belum ada rekam medis untuk kunjungan hari ini:</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($registrationsWithoutRecord as $reg)
                                <a href="{{ route('medical-records.create', ['registration_id' => $reg->id]) }}"
                                   class="text-sm bg-white border px-2 py-1 rounded hover:bg-yellow-100">
                                    #{{ $reg->nomor_antrian }} - {{ $reg->patient->nama ?? '-' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Tanggal</th>
                                <th class="border px-3 py-2 text-left">No Antrian</th>
                                <th class="border px-3 py-2 text-left">Pasien</th>
                                <th class="border px-3 py-2 text-left">Dokter</th>
                                <th class="border px-3 py-2 text-left">Foto</th>
                                <th class="border px-3 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td class="border px-3 py-2">{{ $record->registration->tanggal_kunjungan?->format('d-m-Y') }}</td>
                                    <td class="border px-3 py-2">{{ $record->registration->nomor_antrian }}</td>
                                    <td class="border px-3 py-2">{{ $record->registration->patient->nama ?? '-' }}</td>
                                    <td class="border px-3 py-2">{{ $record->doctor->name ?? '-' }}</td>
                                    <td class="border px-3 py-2">{{ $record->photos->count() }}</td>
                                    <td class="border px-3 py-2">
                                        <a href="{{ route('medical-records.show', $record) }}" class="text-blue-600">Detail</a> |
                                        <a href="{{ route('medical-records.edit', $record) }}" class="text-yellow-600">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="border px-3 py-4 text-center">Belum ada data rekam medis.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $records->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
