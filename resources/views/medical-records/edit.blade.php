<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Rekam Medis') }}
        </h2>
    </x-slot>

    <!-- Panggil Library SweetAlert2 via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-10">
                
                <form method="POST" action="{{ route('medical-records.update', $medicalRecord) }}" enctype="multipart/form-data" class="space-y-6 max-w-4xl mx-auto">
                    @csrf
                    @method('PUT')

                    <!-- Info Pendaftaran (Read-Only saat Edit) -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <x-input-label :value="__('Pasien / Pendaftaran')" />
                        <div class="mt-1 font-medium text-gray-800">
                            {{ $medicalRecord->registration->nomor_antrian ?? '-' }} - 
                            {{ $medicalRecord->registration->patient->nama ?? '-' }} 
                            (RM: {{ $medicalRecord->registration->patient->no_rm ?? '-' }})
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            Poli: {{ $medicalRecord->registration->poli ?? '-' }} | 
                            Tanggal: {{ $medicalRecord->registration->tanggal_kunjungan?->format('d-m-Y') }}
                        </div>
                    </div>

                    <!-- 1. Anamnesis -->
                    <div>
                        <x-input-label for="anamnesis" :value="__('Anamnesis')" />
                        <textarea id="anamnesis" name="anamnesis" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('anamnesis', $medicalRecord->anamnesis) }}</textarea>
                        <x-input-error :messages="$errors->get('anamnesis')" class="mt-2" />
                    </div>

                    <!-- 2. Pemeriksaan Fisik -->
                    <div>
                        <x-input-label for="pemeriksaan_fisik" :value="__('Pemeriksaan Fisik')" />
                        <textarea id="pemeriksaan_fisik" name="pemeriksaan_fisik" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('pemeriksaan_fisik', $medicalRecord->pemeriksaan_fisik) }}</textarea>
                        <x-input-error :messages="$errors->get('pemeriksaan_fisik')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 3. Diagnosis -->
                        <div>
                            <x-input-label for="diagnosis" :value="__('Diagnosis')" />
                            <textarea id="diagnosis" name="diagnosis" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>

                        <!-- 4. Tindakan -->
                        <div>
                            <x-input-label for="tindakan" :value="__('Tindakan')" />
                            <textarea id="tindakan" name="tindakan" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('tindakan', $medicalRecord->tindakan) }}</textarea>
                            <x-input-error :messages="$errors->get('tindakan')" class="mt-2" />
                        </div>

                        <!-- 5. Resep -->
                        <div>
                            <x-input-label for="resep" :value="__('Resep (opsional)')" />
                            <textarea id="resep" name="resep" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('resep', $medicalRecord->resep) }}</textarea>
                            <x-input-error :messages="$errors->get('resep')" class="mt-2" />
                        </div>

                        <!-- 6. Catatan -->
                        <div>
                            <x-input-label for="catatan" :value="__('Catatan (opsional)')" />
                            <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('catatan', $medicalRecord->catatan) }}</textarea>
                            <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                        </div>
                    </div>

                    <!-- 7. Tambah Foto Baru (Akumulatif) -->
                    <div x-data="photoPreview()">
                        <x-input-label for="photos" :value="__('Tambah Foto Baru (opsional, jika ingin menambahkan foto lagi)')" />
                        <input 
                            type="file" 
                            id="photos" 
                            name="photos[]" 
                            multiple 
                            accept=".jpg,.jpeg,.png,.webp" 
                            @change="handleFiles($event)"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer" 
                        />
                        <p class="mt-1 text-xs text-gray-500">Format foto: JPG, JPEG, PNG, WEBP. Foto tersimpan saat ini: {{ $medicalRecord->photos->count() }}</p>
                        <x-input-error :messages="$errors->get('photos')" class="mt-2" />

                        <!-- Grid Preview Foto BARU -->
                        <div x-show="previews.length > 0" class="mt-4 p-4 bg-blue-50/50 border border-blue-200 rounded-xl" style="display: none;">
                            <p class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-3">
                                Foto Baru yang Akan Ditambahkan (<span x-text="previews.length"></span>)
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(image, index) in previews" :key="index">
                                    <div class="relative bg-white border border-gray-200 rounded-lg p-1.5 shadow-sm group">
                                        <img :src="image.url" class="w-full h-24 object-cover rounded" :alt="image.name">
                                        <p class="text-[11px] text-gray-700 mt-1.5 truncate font-medium text-center" x-text="image.name"></p>
                                        <button type="button" @click="removePhoto(index)" class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow">✕</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Utama Form -->
                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded text-sm shadow-sm transition">
                            Update
                        </button>
                        <a href="{{ route('medical-records.show', $medicalRecord) }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                    </div>
                </form>

                <hr class="my-8 border-gray-200">

                <!-- 8. FOTO SAAT INI (Anti-Forbidden Base64) -->
                <div>
                    <h3 class="font-semibold text-gray-800 text-base mb-4">Foto Saat Ini</h3>
                    @if($medicalRecord->photos->count())
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($medicalRecord->photos as $photo)
                                @php
                                    $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $photo->file_path), '/');
                                    
                                    $p1 = public_path('storage/' . $cleanPath);
                                    $p2 = storage_path('app/public/' . $cleanPath);
                                    $p3 = public_path($cleanPath);

                                    $targetFile = null;
                                    if (file_exists($p1)) { $targetFile = $p1; }
                                    elseif (file_exists($p2)) { $targetFile = $p2; }
                                    elseif (file_exists($p3)) { $targetFile = $p3; }

                                    if ($targetFile) {
                                        $ext = pathinfo($targetFile, PATHINFO_EXTENSION);
                                        $data = file_get_contents($targetFile);
                                        $base64 = 'data:image/' . $ext . ';base64,' . base64_encode($data);
                                    } else {
                                        $base64 = null;
                                    }
                                @endphp

                                <div class="border rounded-lg p-2 bg-gray-50 flex flex-col justify-between shadow-sm">
                                    <div>
                                        @if($base64)
                                            <a href="{{ $base64 }}" target="_blank" download="{{ $photo->file_name }}">
                                                <img src="{{ $base64 }}" class="w-full h-28 object-cover rounded hover:opacity-90 transition" alt="{{ $photo->file_name }}">
                                            </a>
                                        @else
                                            <img src="https://placehold.co/300x200?text=File+Fisik+Hilang" class="w-full h-28 object-cover rounded opacity-40">
                                        @endif
                                        <p class="text-xs mt-2 truncate text-gray-700 font-medium text-center">{{ $photo->file_name }}</p>
                                    </div>

                                    <!-- Perbaikan: onsubmit dihapus, diganti panggil SweetAlert -->
                                    <form id="delete-form-{{ $photo->id }}" method="POST" action="{{ route('medical-records.photos.destroy', [$medicalRecord, $photo]) }}" class="mt-3 text-center">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="button" 
                                            onclick="confirmDelete('{{ $photo->id }}', '{{ $photo->file_name }}')"
                                            class="text-xs text-red-600 hover:text-red-800 font-semibold hover:underline"
                                        >
                                            Hapus Foto
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Belum ada foto yang terupload pada rekam medis ini.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Script Preview Akumulatif & SweetAlert Trigger -->
    <script>
        // Fungsi Trigger Modal Hapus SweetAlert2
        function confirmDelete(photoId, fileName) {
            Swal.fire({
                title: 'Hapus Foto Ini?',
                text: `File "${fileName}" akan dihapus permanen dari rekam medis ini.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6e7881',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tembak form hapus sesungguhnya secara otomatis
                    document.getElementById(`delete-form-${photoId}`).submit();
                }
            });
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('photoPreview', () => ({
                previews: [],
                filesArray: [],
                
                handleFiles(event) {
                    const newlySelectedFiles = event.target.files;
                    if (!newlySelectedFiles || newlySelectedFiles.length === 0) return;

                    Array.from(newlySelectedFiles).forEach(newFile => {
                        const isDuplicate = this.filesArray.some(f => f.name === newFile.name && f.size === newFile.size);
                        if (!isDuplicate) {
                            this.filesArray.push(newFile);
                        }
                    });

                    this.syncDOM();
                },

                removePhoto(index) {
                    this.filesArray.splice(index, 1);
                    this.syncDOM();
                },

                syncDOM() {
                    const dt = new DataTransfer();
                    this.filesArray.forEach(file => dt.items.add(file));
                    document.getElementById('photos').files = dt.files;

                    this.previews = this.filesArray.map(file => ({
                        url: URL.createObjectURL(file),
                        name: file.name
                    }));
                }
            }));
        });
    </script>
</x-app-layout>