<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Rekam Medis</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                    
                    <form method="GET" action="{{ route('medical-records.index') }}" class="flex flex-wrap gap-2 items-center">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Pasien / No RM..." class="border border-gray-300 rounded px-3 py-2 text-sm w-48 focus:border-indigo-500 focus:ring-indigo-500">
                        
                        <input type="date" name="tanggal" value="{{ $tanggal ?? request('tanggal', now()->toDateString()) }}" class="border border-gray-300 rounded px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        
                        <div class="flex gap-1">
                            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-2 rounded text-sm transition">Filter</button>
                            <a href="{{ route('medical-records.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded text-sm transition">Reset</a>
                        </div>
                    </form>

                    <div class="flex gap-2 shrink-0">
                        <a href="{{ route('medical-records.export.daily-pdf', ['tanggal' => $tanggal ?? request('tanggal', now()->toDateString())]) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium transition whitespace-nowrap shadow-sm">
                            Print PDF Harian
                        </a>
                        <a href="{{ route('medical-records.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition whitespace-nowrap shadow-sm">
                            + Tambah Data
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">No RM</th>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">Nama Pasien</th>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">Tanggal</th>
                                <th class="border px-3 py-2 text-left font-semibold text-gray-700 text-sm">Dokter</th>
                                <th class="border px-3 py-2 text-center font-semibold text-gray-700 text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($records as $r)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ $r->registration->patient->no_rm ?? '-' }}</td>
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ $r->registration->patient->nama ?? '-' }}</td>
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ $r->created_at->format('d-m-Y') }}</td>
                                <td class="border px-3 py-2 text-sm text-gray-800">{{ $r->doctor->name ?? '-' }}</td>
                                <td class="border px-3 py-2 text-center text-sm whitespace-nowrap">
                                    <a href="{{ route('medical-records.show', $r) }}" class="text-blue-600 hover:underline">Lihat</a> |
                                    <a href="{{ route('medical-records.export.single-pdf', $r) }}" target="_blank" class="text-gray-700 hover:underline">Print PDF</a> |
                                    <a href="{{ route('medical-records.edit', $r) }}" class="text-yellow-600 hover:underline">Edit</a> |
                                    
                                    <a href="{{ route('medical-records.logs', ['record_id' => $r->id]) }}" class="text-purple-600 font-semibold hover:underline" title="Lihat riwayat aktivitas rekam medis ini">Log</a> |
                                    
                                    <form action="{{ route('medical-records.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus rekam medis ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border px-3 py-6 text-center text-gray-500 text-sm">Belum ada data rekam medis pada tanggal ini.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $records->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>