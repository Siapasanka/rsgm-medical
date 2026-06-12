<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pasien') }}
            </h2>
            <a href="{{ route('patients.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">

                <div class="px-6 py-5 border-b border-gray-200 bg-white">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900">{{ $patient->nama }}</h3>
                        <p class="text-sm font-medium text-gray-500 mt-0.5">Nomor Rekam Medis: <span class="text-blue-600 font-bold">{{ $patient->no_rm }}</span></p>
                    </div>
                </div>

                <div class="p-6 bg-gray-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">NIK</p>
                            <p class="text-sm font-bold text-gray-900">{{ $patient->nik }}</p>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Lahir</p>
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($patient->tgl_lahir)->translatedFormat('d F Y') }}</p>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Jenis Kelamin</p>
                            <p class="text-sm font-bold text-gray-900">{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">No. Handphone</p>
                            <p class="text-sm font-bold text-gray-900">{{ $patient->no_hp }}</p>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Golongan Darah</p>
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded border border-red-200 text-xs font-extrabold bg-red-50 text-red-700">
                                {{ $patient->gol_darah }}
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm md:col-span-2 lg:col-span-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Alamat Lengkap</p>
                            <p class="text-sm font-bold text-gray-900 leading-relaxed">{{ $patient->alamat }}</p>
                        </div>

                        <div class="bg-red-50 p-4 rounded-lg border border-red-200 shadow-sm md:col-span-2 lg:col-span-3 flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <p class="text-xs font-extrabold text-red-800 uppercase tracking-wider mb-1">Riwayat Alergi Obat / Makanan</p>
                                <p class="text-sm font-bold text-red-900">{{ $patient->alergi ?: 'Tidak ada riwayat alergi yang tercatat.' }}</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>