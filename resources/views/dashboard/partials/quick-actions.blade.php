<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
    <div class="p-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Aksi Cepat</h3>
        <p class="text-xs text-gray-500">Shortcut operasional harian</p>
    </div>

    <div class="p-4 space-y-2">
        <a href="{{ route('patients.create') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            + Tambah Pasien
        </a>
        <a href="{{ route('registrations.create') }}" class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">
            + Daftarkan Kunjungan
        </a>
        <a href="{{ route('patients.index') }}" class="block w-full text-center bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">
            Lihat Data Pasien
        </a>
    </div>
</div>
