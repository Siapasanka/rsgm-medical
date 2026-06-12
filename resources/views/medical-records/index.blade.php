<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Rekam Medis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success') || session('status'))
                <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-lg border border-emerald-200">
                    {{ session('success') ?? session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6 md:p-8">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <h3 class="text-lg font-extrabold text-gray-900 uppercase tracking-widest">Data Rekam Medis</h3>
                    
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('medical-records.export-daily-pdf') }}" target="_blank" class="inline-flex items-center justify-center h-10 px-4 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-md text-sm font-bold shadow-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print PDF Harian
                        </a>
                        
                        <a href="{{ route('medical-records.create') }}" class="inline-flex items-center justify-center h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold shadow-sm transition-colors">
                            + Tambah Data
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('medical-records.index') }}" class="mb-6 flex flex-wrap items-end gap-3 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                    <div class="flex-1 min-w-[200px]">
                        <x-input-label for="q" :value="__('Cari Pasien / No RM')" class="mb-1" />
                        <x-text-input id="q" name="q" type="text" class="block w-full bg-white" value="{{ request('q') }}" placeholder="Ketik pencarian..." />
                    </div>
                    <div class="w-40">
                        <x-input-label for="date" :value="__('Tanggal')" class="mb-1" />
                        <x-text-input id="date" name="date" type="date" class="block w-full bg-white" value="{{ request('date') }}" />
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center justify-center h-10 px-5 border border-blue-700 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-semibold shadow-sm transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('medical-records.index') }}" class="inline-flex items-center justify-center h-10 px-5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md text-sm font-semibold shadow-sm transition-colors">
                            Reset
                        </a>
                    </div>
                </form>

                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-widest">No RM</th>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-widest">Nama Pasien</th>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-widest">Dokter</th>
                                <th class="px-5 py-4 text-center text-[11px] font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($medicalRecords as $record)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-4 text-sm text-gray-900 font-bold">{{ $record->patient->no_rm ?? '-' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-900">{{ $record->patient->nama ?? '-' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600">{{ $record->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600">{{ $record->user->name ?? '-' }}</td>
                                    
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                            
                                            <a href="{{ route('medical-records.show', $record) }}" class="inline-flex items-center h-7 px-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded text-xs font-bold transition-colors shadow-sm">
                                                Lihat
                                            </a>
                                            
                                            <a href="{{ route('medical-records.export-single-pdf', $record) }}" target="_blank" class="inline-flex items-center h-7 px-3 bg-purple-500 hover:bg-purple-600 text-white rounded text-xs font-bold transition-colors shadow-sm">
                                                Print PDF
                                            </a>

                                            <a href="{{ route('medical-records.edit', $record) }}" class="inline-flex items-center h-7 px-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs font-bold transition-colors shadow-sm">
                                                Edit
                                            </a>

                                            <form class="inline-block m-0 p-0" method="POST" action="{{ route('medical-records.destroy', $record) }}" onsubmit="return confirm('Yakin ingin menghapus rekam medis ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center h-7 px-3 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-bold transition-colors shadow-sm">
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-5 py-8 text-center text-sm text-gray-500" colspan="5">Belum ada data rekam medis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $medicalRecords->links() }}
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>