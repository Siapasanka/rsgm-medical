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
                
                <div class="flex flex-col xl:flex-row xl:justify-between xl:items-center gap-4 mb-4">
                    
                    <form method="GET" action="{{ route('registrations.index') }}" class="flex flex-wrap gap-2 items-center">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Antrian / Nama..." class="border border-gray-300 rounded px-3 py-2 text-sm w-48 focus:border-indigo-500 focus:ring-indigo-500">
                        
                        <select name="poli" class="border border-gray-300 rounded px-3 py-2 text-sm w-48 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Poli</option>
                            <option value="Poli Gigi Umum" {{ request('poli') == 'Poli Gigi Umum' ? 'selected' : '' }}>Poli Gigi Umum</option>
                            <option value="Poli Bedah Mulut" {{ request('poli') == 'Poli Bedah Mulut' ? 'selected' : '' }}>Poli Bedah Mulut</option>
                            <option value="Poli Konservasi Gigi" {{ request('poli') == 'Poli Konservasi Gigi' ? 'selected' : '' }}>Poli Konservasi Gigi</option>
                            <option value="Poli Kedokteran Gigi Anak" {{ request('poli') == 'Poli Kedokteran Gigi Anak' ? 'selected' : '' }}>Poli Kedokteran Gigi Anak</option>
                            <option value="Poli Periodonsia" {{ request('poli') == 'Poli Periodonsia' ? 'selected' : '' }}>Poli Periodonsia</option>
                            <option value="Poli Orthodonsia" {{ request('poli') == 'Poli Orthodonsia' ? 'selected' : '' }}>Poli Orthodonsia</option>
                            <option value="Poli Prostodonsia" {{ request('poli') == 'Poli Prostodonsia' ? 'selected' : '' }}>Poli Prostodonsia</option>
                            <option value="Poli Penyakit Mulut" {{ request('poli') == 'Poli Penyakit Mulut' ? 'selected' : '' }}>Poli Penyakit Mulut</option>
                        </select>

                        <input type="date" name="tanggal" value="{{ $tanggal ?? request('tanggal') }}" class="border border-gray-300 rounded px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        
                        <div class="flex gap-1">
                            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded text-sm transition">Filter</button>
                            <a href="{{ route('registrations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm transition">Reset</a>
                        </div>
                    </form>

                    <div class="flex gap-2 shrink-0">
                        <a href="{{ route('registrations.export.daily-pdf', ['tanggal' => $tanggal ?? request('tanggal')]) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition">
                            Print PDF Harian
                        </a>
                        <a href="{{ route('registrations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition">
                            + Tambah Pendaftaran
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">No Antrian</th>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">Tanggal</th>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">Pasien</th>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">Poli</th>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">Status</th>
                                <th class="border px-3 py-2 text-center font-semibold text-gray-700 text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($registrations as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ $r->nomor_antrian }}</td>
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ $r->tanggal_kunjungan?->format('d-m-Y') }}</td>
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ $r->patient->nama ?? '-' }}</td>
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ $r->poli }}</td>
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ ucfirst($r->status_antrian) }}</td>
                                <td class="border px-3 py-2 text-center text-sm">
                                    <a href="{{ route('registrations.show', $r) }}" class="text-blue-600 hover:underline">Detail</a> |
                                    <a href="{{ route('registrations.edit', $r) }}" class="text-yellow-600 hover:underline">Edit</a> |
                                    <form action="{{ route('registrations.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="border px-3 py-4 text-center text-gray-500 text-sm">Belum ada data pendaftaran.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $registrations->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>