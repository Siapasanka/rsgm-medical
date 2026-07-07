<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Rekam Medis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-10">
                
                <form method="POST" action="{{ route('medical-records.store') }}" enctype="multipart/form-data" class="space-y-6 max-w-4xl mx-auto">
                    @csrf

                    <div x-data="registrationSearch({{ $registrations->toJson() ?? '[]' }}, '{{ old('registration_id', $registrationId ?? '') }}')" class="relative">
                        <div>
                            <x-input-label for="search_registration" :value="__('Pendaftaran')" />
                            
                            <div class="relative mt-1">
                                <x-text-input 
                                    id="search_registration" 
                                    type="text" 
                                    class="block w-full" 
                                    x-model="search"
                                    @focus="open = true"
                                    @click.away="open = false"
                                    @input="selectedRegistration = null; open = true"
                                    placeholder="Ketik Nama Pasien, No Antrian, atau No RM..."
                                    autocomplete="off"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('registration_id')" class="mt-2" />

                            <div x-show="open" style="display: none;" class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                <template x-if="filteredRegistrations.length > 0">
                                    <ul class="py-1">
                                        <template x-for="r in filteredRegistrations" :key="r.id">
                                            <li @click="selectRegistration(r)" class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm text-gray-800 border-b border-gray-50 last:border-0">
                                                <div class="font-medium">
                                                    <span x-text="r.nomor_antrian" class="text-blue-600 font-bold mr-1"></span> 
                                                    <span x-text="r.patient ? r.patient.nama : 'Pasien tidak diketahui'"></span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-0.5" x-text="r.poli + ' | RM: ' + (r.patient ? r.patient.no_rm : '-')"></div>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                                
                                <template x-if="filteredRegistrations.length === 0 && search !== ''">
                                    <div class="px-4 py-3 text-sm text-red-600">
                                        Keterangan: Data tidak ditemukan.
                                    </div>
                                </template>
                            </div>

                            <div x-show="selectedRegistration" style="display: none;" class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded border border-gray-200">
                                <strong>Terpilih:</strong> Antrian <span x-text="selectedRegistration?.nomor_antrian"></span> - 
                                <span x-text="selectedRegistration?.patient?.nama"></span> | 
                                RM: <span x-text="selectedRegistration?.patient?.no_rm"></span> | 
                                Poli: <span x-text="selectedRegistration?.poli"></span>
                            </div>
                        </div>

                        <input type="hidden" name="registration_id" :value="selectedRegistration ? selectedRegistration.id : ''">
                    </div>

                    <div>
                        <x-input-label for="anamnesis" :value="__('Anamnesis')" />
                        <textarea id="anamnesis" name="anamnesis" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('anamnesis') }}</textarea>
                        <x-input-error :messages="$errors->get('anamnesis')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="pemeriksaan_fisik" :value="__('Pemeriksaan Fisik')" />
                        <textarea id="pemeriksaan_fisik" name="pemeriksaan_fisik" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('pemeriksaan_fisik') }}</textarea>
                        <x-input-error :messages="$errors->get('pemeriksaan_fisik')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="diagnosis" :value="__('Diagnosis')" />
                            <textarea id="diagnosis" name="diagnosis" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('diagnosis') }}</textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tindakan" :value="__('Tindakan')" />
                            <textarea id="tindakan" name="tindakan" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('tindakan') }}</textarea>
                            <x-input-error :messages="$errors->get('tindakan')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="resep" :value="__('Resep (opsional)')" />
                            <textarea id="resep" name="resep" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('resep') }}</textarea>
                            <x-input-error :messages="$errors->get('resep')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="catatan" :value="__('Catatan (opsional)')" />
                            <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('catatan') }}</textarea>
                            <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                        </div>
                    </div>

                    <div x-data="photoPreview()">
                        <x-input-label for="photos" :value="__('Upload Foto (bisa pilih satu per satu atau sekaligus)')" />
                        
                        <input 
                            type="file" 
                            id="photos" 
                            name="photos[]" 
                            multiple 
                            accept=".jpg,.jpeg,.png,.webp" 
                            @change="handleFiles($event)"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer" 
                        />
                        <p class="mt-1 text-xs text-gray-500">Tips: Kamu bisa klik 'Choose Files' berkali-kali untuk menambahkan foto baru tanpa mereset foto sebelumnya.</p>
                        <x-input-error :messages="$errors->get('photos')" class="mt-2" />

                        <div x-show="previews.length > 0" class="mt-4 p-4 bg-gray-50/80 border border-gray-200 rounded-xl" style="display: none;">
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">
                                Foto Terkumpul (<span x-text="previews.length"></span>)
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(image, index) in previews" :key="index">
                                    <div class="relative bg-white border border-gray-200 rounded-lg p-1.5 shadow-sm group">
                                        <img :src="image.url" class="w-full h-24 object-cover rounded" :alt="image.name">
                                        <p class="text-[11px] text-gray-700 mt-1.5 truncate font-medium text-center" x-text="image.name"></p>
                                        
                                        <button 
                                            type="button" 
                                            @click="removePhoto(index)"
                                            class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow transition"
                                            title="Hapus foto ini"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded text-sm shadow-sm transition">
                            Simpan
                        </button>
                        <a href="{{ route('medical-records.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registrationSearch', (registrations, oldRegId) => ({
                registrations: registrations,
                search: '',
                open: false,
                selectedRegistration: null,

                init() {
                    if (oldRegId) {
                        this.selectedRegistration = this.registrations.find(r => r.id == oldRegId);
                        if (this.selectedRegistration) {
                            const patientName = this.selectedRegistration.patient ? this.selectedRegistration.patient.nama : '';
                            this.search = this.selectedRegistration.nomor_antrian + ' - ' + patientName;
                        }
                    }
                },

                get filteredRegistrations() {
                    if (this.search === '') {
                        return this.registrations.slice(0, 30);
                    }
                    const q = this.search.toLowerCase();
                    return this.registrations.filter(r => {
                        const patientName = r.patient && r.patient.nama ? r.patient.nama.toLowerCase() : '';
                        const noRm = r.patient && r.patient.no_rm ? r.patient.no_rm.toLowerCase() : '';
                        const noAntrian = r.nomor_antrian ? r.nomor_antrian.toLowerCase() : '';
                        
                        return patientName.includes(q) || noRm.includes(q) || noAntrian.includes(q);
                    }).slice(0, 20);
                },

                selectRegistration(r) {
                    this.selectedRegistration = r;
                    const patientName = r.patient ? r.patient.nama : '';
                    this.search = r.nomor_antrian + ' - ' + patientName;
                    this.open = false;
                }
            }));

            // LOGIKA BARU: AKUMULASI FOTO
            Alpine.data('photoPreview', () => ({
                previews: [],
                filesArray: [],
                
                handleFiles(event) {
                    const newlySelectedFiles = event.target.files;
                    if (!newlySelectedFiles || newlySelectedFiles.length === 0) return;

                    Array.from(newlySelectedFiles).forEach(newFile => {
                        // Cek apakah foto dengan nama & ukuran yang persis sama sudah ada di list
                        const isDuplicate = this.filesArray.some(f => f.name === newFile.name && f.size === newFile.size);
                        
                        if (!isDuplicate) {
                            this.filesArray.push(newFile); // PUSH (Menambahkan, bukan mengganti!)
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
                    
                    // Paksa update ingatan tag <input type="file"> dengan seluruh kumpulan foto
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