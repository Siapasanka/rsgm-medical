<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Rekam Medis</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <p><b>Tanggal Kunjungan:</b> {{ $medicalRecord->registration->tanggal_kunjungan?->format('d-m-Y') }}</p>
                <p><b>No Antrian:</b> {{ $medicalRecord->registration->nomor_antrian }}</p>
                <p><b>Pasien:</b> {{ $medicalRecord->registration->patient->nama ?? '-' }}</p>
                <p><b>Dokter:</b> {{ $medicalRecord->doctor->name ?? '-' }}</p>
                <hr class="my-3">
                <p><b>Anamnesis:</b><br>{{ $medicalRecord->anamnesis ?: '-' }}</p>
                <p><b>Pemeriksaan Fisik:</b><br>{{ $medicalRecord->pemeriksaan_fisik ?: '-' }}</p>
                <p><b>Diagnosis:</b><br>{{ $medicalRecord->diagnosis ?: '-' }}</p>
                <p><b>Tindakan:</b><br>{{ $medicalRecord->tindakan ?: '-' }}</p>
                <p><b>Resep:</b><br>{{ $medicalRecord->resep ?: '-' }}</p>
                <p><b>Catatan:</b><br>{{ $medicalRecord->catatan ?: '-' }}</p>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Foto Klinis</h3>
                @if($medicalRecord->photos->count())
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($medicalRecord->photos as $photo)
                            <a href="{{ Storage::url($photo->file_path) }}" target="_blank" class="block border rounded p-2 hover:bg-gray-50">
                                <img src="{{ Storage::url($photo->file_path) }}" class="w-full h-28 object-cover rounded" alt="{{ $photo->file_name }}">
                                <p class="text-xs mt-2 truncate">{{ $photo->file_name }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Belum ada foto.</p>
                @endif
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('medical-records.export.single-pdf', $medicalRecord) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Print Ringkasan PDF</a>
                <a href="{{ route('medical-records.edit', $medicalRecord) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Edit</a>
                <form method="POST" action="{{ route('medical-records.destroy', $medicalRecord) }}" data-confirm="Hapus rekam medis ini?">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Hapus</button>
                </form>
                <a href="{{ route('medical-records.index') }}" class="text-gray-600">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
