<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pendaftaran Pasien</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                    <form method="GET" class="flex gap-2 items-center">
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="border rounded px-3 py-2">
                        <button class="bg-gray-700 text-white px-4 py-2 rounded">Filter</button>
                    </form>

                    <div class="flex gap-2">
                        <a href="{{ route('registrations.export.daily-pdf', ['tanggal' => $tanggal]) }}" class="bg-green-600 text-white px-4 py-2 rounded">
                            Print PDF Harian
                        </a>
                        <a href="{{ route('registrations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                            + Tambah Pendaftaran
                        </a>
                    </div>
                </div>

                <table class="min-w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">No Antrian</th>
                            <th class="border px-3 py-2">Tanggal</th>
                            <th class="border px-3 py-2">Pasien</th>
                            <th class="border px-3 py-2">Poli</th>
                            <th class="border px-3 py-2">Status</th>
                            <th class="border px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($registrations as $r)
                        <tr>
                            <td class="border px-3 py-2">{{ $r->nomor_antrian }}</td>
                            <td class="border px-3 py-2">{{ $r->tanggal_kunjungan?->format('d-m-Y') }}</td>
                            <td class="border px-3 py-2">{{ $r->patient->nama ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $r->poli }}</td>
                            <td class="border px-3 py-2">{{ $r->status_antrian }}</td>
                            <td class="border px-3 py-2">
                                <a href="{{ route('registrations.show', $r) }}" class="text-blue-600">Detail</a> |
                                <a href="{{ route('registrations.edit', $r) }}" class="text-yellow-600">Edit</a> |
                                <form action="{{ route('registrations.destroy', $r) }}" method="POST" class="inline" data-confirm="Yakin hapus?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="border px-3 py-4 text-center">Belum ada data.</td></tr>
                    @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $registrations->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>