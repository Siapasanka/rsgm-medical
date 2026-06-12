<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pendaftaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('registrations.store') }}" class="space-y-6 max-w-2xl">
                    @csrf

                    <div x-data="patientSearch({{ $patients->toJson() }}, '{{ old('patient_id') }}')" class="relative">
                        <div>
                            <x-input-label for="search_pasien" :value="__('Pasien')" />
                            
                            <div class="relative mt-1">
                                <x-text-input 
                                    id="search_pasien" 
                                    type="text" 
                                    class="block w-full" 
                                    x-model="search"
                                    @focus="open = true"
                                    @click.away="open = false"
                                    @input="selectedPatient = null; open = true"
                                    placeholder="Ketik Nama atau No RM..."
                                    autocomplete="off"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('patient_id')" class="mt-2" />

                            <div x-show="open" style="display: none;" class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                <template x-if="filteredPatients.length > 0">
                                    <ul class="py-1">
                                        <template x-for="p in filteredPatients" :key="p.id">
                                            <li @click="selectPatient(p)" class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm text-gray-800">
                                                <span class="font-medium" x-text="p.nama"></span>
                                                <span class="text-gray-500" x-text="' (' + p.no_rm + ')'"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                                <template x-if="filteredPatients.length === 0 && search !== ''">
                                    <div class="px-4 py-3 text-sm text-red-600">
                                        Keterangan: Data tidak ditemukan.
                                    </div>
                                </template>
                            </div>

                            <div x-show="selectedPatient" style="display: none;" class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded border border-gray-200">
                                <strong>Detail:</strong> <span x-text="selectedPatient?.nama"></span> | 
                                NIK: <span x-text="selectedPatient?.nik"></span> | 
                                RM: <span x-text="selectedPatient?.no_rm"></span> | 
                                JK: <span x-text="selectedPatient?.jenis_kelamin"></span>
                            </div>
                        </div>

                        <input type="hidden" name="patient_id" :value="selectedPatient ? selectedPatient.id : ''">
                    </div>

                    <div>
                        <x-input-label for="poli" :value="__('Poli')" />
                        <select id="poli" name="poli" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="" disabled {{ old('poli') ? '' : 'selected' }}>Pilih Poli...</option>
                            <option value="Poli Gigi Umum" {{ old('poli') == 'Poli Gigi Umum' ? 'selected' : '' }}>Poli Gigi Umum</option>
                            <option value="Poli Bedah Mulut" {{ old('poli') == 'Poli Bedah Mulut' ? 'selected' : '' }}>Poli Bedah Mulut</option>
                            <option value="Poli Konservasi Gigi" {{ old('poli') == 'Poli Konservasi Gigi' ? 'selected' : '' }}>Poli Konservasi Gigi</option>
                            <option value="Poli Kedokteran Gigi Anak" {{ old('poli') == 'Poli Kedokteran Gigi Anak' ? 'selected' : '' }}>Poli Kedokteran Gigi Anak</option>
                            <option value="Poli Periodonsia" {{ old('poli') == 'Poli Periodonsia' ? 'selected' : '' }}>Poli Periodonsia</option>
                            <option value="Poli Orthodonsia" {{ old('poli') == 'Poli Orthodonsia' ? 'selected' : '' }}>Poli Orthodonsia</option>
                            <option value="Poli Prostodonsia" {{ old('poli') == 'Poli Prostodonsia' ? 'selected' : '' }}>Poli Prostodonsia</option>
                            <option value="Poli Penyakit Mulut" {{ old('poli') == 'Poli Penyakit Mulut' ? 'selected' : '' }}>Poli Penyakit Mulut</option>
                        </select>
                        <x-input-error :messages="$errors->get('poli')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="tanggal_kunjungan" :value="__('Tanggal Kunjungan')" />
                        <x-text-input id="tanggal_kunjungan" name="tanggal_kunjungan" type="date" class="mt-1 block w-full" value="{{ old('tanggal_kunjungan', date('Y-m-d')) }}" required />
                        <x-input-error :messages="$errors->get('tanggal_kunjungan')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="keluhan_utama" :value="__('Keluhan Utama (Opsional)')" />
                        <textarea id="keluhan_utama" name="keluhan_utama" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('keluhan_utama') }}</textarea>
                        <x-input-error :messages="$errors->get('keluhan_utama')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        <a href="{{ route('registrations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('patientSearch', (patients, oldPatientId) => ({
                patients: patients,
                search: '',
                open: false,
                selectedPatient: null,

                init() {
                    if (oldPatientId) {
                        this.selectedPatient = this.patients.find(p => p.id == oldPatientId);
                        if (this.selectedPatient) {
                            this.search = this.selectedPatient.nama + ' (' + this.selectedPatient.no_rm + ')';
                        }
                    }
                },

                get filteredPatients() {
                    if (this.search === '') {
                        return this.patients.slice(0, 30);
                    }
                    const q = this.search.toLowerCase();
                    return this.patients.filter(p => 
                        p.nama.toLowerCase().includes(q) || 
                        p.no_rm.toLowerCase().includes(q)
                    ).slice(0, 20);
                },

                selectPatient(p) {
                    this.selectedPatient = p;
                    this.search = p.nama + ' (' + p.no_rm + ')';
                    this.open = false;
                }
            }));
        });
    </script>
</x-app-layout>