@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label>No RM</label>
        <input type="text" value="{{ old('no_rm', $patient->no_rm ?? ($generatedNoRm ?? 'Auto')) }}" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
        <small class="text-gray-500">Nomor RM dibuat otomatis oleh sistem.</small>
    </div>

    <div>
        <label>NIK</label>
        <input type="text" name="nik" value="{{ old('nik', $patient->nik ?? '') }}" class="w-full border rounded px-3 py-2">
        @error('nik') <small class="text-red-600">{{ $message }}</small> @enderror
    </div>

    <div>
        <label>Nama</label>
        <input type="text" name="nama" value="{{ old('nama', $patient->nama ?? '') }}" class="w-full border rounded px-3 py-2">
        @error('nama') <small class="text-red-600">{{ $message }}</small> @enderror
    </div>

    <div>
        <label>Tanggal Lahir</label>
        <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', isset($patient) ? $patient->tgl_lahir?->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2">
        @error('tgl_lahir') <small class="text-red-600">{{ $message }}</small> @enderror
    </div>

    <div>
        <label>Jenis Kelamin</label>
        <select name="jenis_kelamin" class="w-full border rounded px-3 py-2">
            <option value="">-- Pilih --</option>
            <option value="L" @selected(old('jenis_kelamin', $patient->jenis_kelamin ?? '')=='L')>Laki-laki</option>
            <option value="P" @selected(old('jenis_kelamin', $patient->jenis_kelamin ?? '')=='P')>Perempuan</option>
        </select>
        @error('jenis_kelamin') <small class="text-red-600">{{ $message }}</small> @enderror
    </div>

    <div>
        <label>No HP</label>
        <input type="text" name="no_hp" value="{{ old('no_hp', $patient->no_hp ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label>Golongan Darah</label>
        <input type="text" name="gol_darah" value="{{ old('gol_darah', $patient->gol_darah ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div class="md:col-span-2">
        <label>Alamat</label>
        <textarea name="alamat" class="w-full border rounded px-3 py-2">{{ old('alamat', $patient->alamat ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label>Alergi</label>
        <textarea name="alergi" class="w-full border rounded px-3 py-2">{{ old('alergi', $patient->alergi ?? '') }}</textarea>
    </div>
</div>

<div style="margin-top:16px; display:flex; align-items:center; gap:12px;">
    <button type="submit" style="background:#2563eb; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">
        {{ isset($patient) ? 'Update' : 'Tambah' }}
    </button>
    <a href="{{ route('patients.index') }}" style="color:#4b5563; text-decoration:none;">Kembali</a>
</div>