@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label>Pasien</label>
        <select name="patient_id" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Pasien --</option>
            @foreach($patients as $p)
                <option value="{{ $p->id }}" @selected(old('patient_id', $registration->patient_id ?? '') == $p->id)>
                    {{ $p->no_rm }} - {{ $p->nama }}
                </option>
            @endforeach
        </select>
        @error('patient_id') <small class="text-red-600">{{ $message }}</small> @enderror
    </div>

    <div>
        <label>Tanggal Kunjungan</label>
        <input type="date" name="tanggal_kunjungan"
               value="{{ old('tanggal_kunjungan', isset($registration) ? $registration->tanggal_kunjungan?->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="w-full border rounded px-3 py-2" required>
        @error('tanggal_kunjungan') <small class="text-red-600">{{ $message }}</small> @enderror
    </div>

    <div>
        <label>Poli</label>
        <input type="text" name="poli" value="{{ old('poli', $registration->poli ?? '') }}"
               class="w-full border rounded px-3 py-2" placeholder="Contoh: Konservasi Gigi" required>
        @error('poli') <small class="text-red-600">{{ $message }}</small> @enderror
    </div>

    @isset($registration)
    <div>
        <label>Status Antrian</label>
        <select name="status_antrian" class="w-full border rounded px-3 py-2">
            @foreach(['menunggu','diperiksa','selesai'] as $status)
                <option value="{{ $status }}" @selected(old('status_antrian', $registration->status_antrian) == $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
    </div>
    @endisset

    <div class="md:col-span-2">
        <label>Keluhan Utama (opsional)</label>
        <textarea name="keluhan_utama" class="w-full border rounded px-3 py-2">{{ old('keluhan_utama', $registration->keluhan_utama ?? '') }}</textarea>
    </div>
</div>

<div class="mt-4">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('registrations.index') }}" class="ml-2 text-gray-600">Kembali</a>
</div>