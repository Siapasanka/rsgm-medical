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

            <!-- Bagian Foto Sakti Anti-Forbidden -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Foto Klinis</h3>
                @if($medicalRecord->photos->count())
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($medicalRecord->photos as $photo)
                            @php
                                // 1. Bersihkan teks path dari awalan public/ atau storage/
                                $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $photo->file_path), '/');

                                // 2. Mesin pencari fisik: melacak 3 lokasi potensial di hard-disk laptop
                                $path1 = public_path('storage/' . $cleanPath);
                                $path2 = storage_path('app/public/' . $cleanPath);
                                $path3 = public_path($cleanPath);

                                $realDiskFile = null;
                                if (file_exists($path1)) { $realDiskFile = $path1; }
                                elseif (file_exists($path2)) { $realDiskFile = $path2; }
                                elseif (file_exists($path3)) { $realDiskFile = $path3; }

                                // 3. Jika fisik ketemu di hard-disk, ubah jadi Base64
                                if ($realDiskFile) {
                                    $ext = pathinfo($realDiskFile, PATHINFO_EXTENSION);
                                    $rawBytes = file_get_contents($realDiskFile);
                                    $base64Image = 'data:image/' . $ext . ';base64,' . base64_encode($rawBytes);
                                } else {
                                    $base64Image = null;
                                }
                            @endphp

                            <div class="block border rounded p-2 bg-gray-50 hover:bg-gray-100 transition shadow-sm">
                                @if($base64Image)
                                    <a href="{{ $base64Image }}" target="_blank" download="{{ $photo->file_name }}" title="Klik untuk mendownload gambar ini">
                                        <img src="{{ $base64Image }}" class="w-full h-28 object-cover rounded" alt="{{ $photo->file_name }}">
                                    </a>
                                @else
                                    <img src="https://placehold.co/300x200?text=File+Fisik+Hilang" class="w-full h-28 object-cover rounded opacity-40" alt="Missing">
                                @endif
                                <p class="text-xs mt-2 truncate text-gray-700 font-medium text-center" title="{{ $photo->file_name }}">{{ $photo->file_name }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Belum ada foto.</p>
                @endif
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('medical-records.export.single-pdf', $medicalRecord) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-medium text-sm transition">Print Ringkasan PDF</a>
                <a href="{{ route('medical-records.edit', $medicalRecord) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded font-medium text-sm transition">Edit</a>
                <form method="POST" action="{{ route('medical-records.destroy', $medicalRecord) }}" onsubmit="return confirm('Hapus rekam medis ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded font-medium text-sm transition">Hapus</button>
                </form>
                <a href="{{ route('medical-records.index') }}" class="text-gray-600 hover:underline text-sm ml-2">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>