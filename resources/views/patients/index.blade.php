<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Pasien</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                    <form method="GET" action="{{ route('patients.index') }}" class="flex gap-2">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Cari nama / no RM / NIK"
                            class="border rounded px-3 py-2 w-full sm:w-72"
                        >
                        <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">
                            Cari
                        </button>
                    </form>

                    <a href="{{ route('patients.create') }}"
                       class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded">
                        + Tambah Pasien
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">No RM</th>
                                <th class="border px-3 py-2 text-left">NIK</th>
                                <th class="border px-3 py-2 text-left">Nama</th>
                                <th class="border px-3 py-2 text-left">JK</th>
                                <th class="border px-3 py-2 text-left">No HP</th>
                                <th class="border px-3 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patients as $patient)
                                <tr>
                                    <td class="border px-3 py-2">{{ $patient->no_rm }}</td>
                                    <td class="border px-3 py-2">{{ $patient->nik }}</td>
                                    <td class="border px-3 py-2">{{ $patient->nama }}</td>
                                    <td class="border px-3 py-2">{{ $patient->jenis_kelamin }}</td>
                                    <td class="border px-3 py-2">{{ $patient->no_hp }}</td>
                                    <td class="border px-3 py-2">
                                        <a href="{{ route('patients.show', $patient) }}" class="text-blue-600">Detail</a> |
                                        <a href="{{ route('patients.edit', $patient) }}" class="text-yellow-600">Edit</a> |
                                        <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline" data-confirm="Yakin hapus data?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border px-3 py-4 text-center">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $patients->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>