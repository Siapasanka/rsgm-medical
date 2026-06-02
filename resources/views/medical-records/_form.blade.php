@php
    $record = $medicalRecord ?? null;
    $existingPhotoCount = $record?->photos?->count() ?? 0;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <x-input-label for="registration_id" value="Pendaftaran" />

        @if($record)
            <input type="text" class="mt-1 block w-full border-gray-300 rounded-md bg-gray-100"
                value="{{ $record->registration->tanggal_kunjungan?->format('d-m-Y') }} | #{{ $record->registration->nomor_antrian }} | {{ $record->registration->patient->nama ?? '-' }}"
                disabled>
        @else
            <select id="registration_id" name="registration_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                <option value="">-- Pilih Pendaftaran --</option>
                @foreach($registrations as $reg)
                    <option value="{{ $reg->id }}" @selected(old('registration_id', $registrationId ?? null) == $reg->id)>
                        {{ $reg->tanggal_kunjungan?->format('d-m-Y') }} | #{{ $reg->nomor_antrian }} | {{ $reg->patient->nama ?? '-' }} | {{ $reg->poli }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('registration_id')" class="mt-2" />
        @endif
    </div>

    <div class="md:col-span-2">
        <x-input-label for="anamnesis" value="Anamnesis" />
        <textarea id="anamnesis" name="anamnesis" rows="3" class="mt-1 block w-full border-gray-300 rounded-md" required>{{ old('anamnesis', $record->anamnesis ?? '') }}</textarea >
        <x-input-error :messages="$errors->get('anamnesis')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="pemeriksaan_fisik" value="Pemeriksaan Fisik" />
        <textarea id="pemeriksaan_fisik" name="pemeriksaan_fisik" rows="3" class="mt-1 block w-full border-gray-300 rounded-md" required>{{ old('pemeriksaan_fisik', $record->pemeriksaan_fisik ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('pemeriksaan_fisik')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="diagnosis" value="Diagnosis" />
        <textarea id="diagnosis" name="diagnosis" rows="3" class="mt-1 block w-full border-gray-300 rounded-md" required>{{ old('diagnosis', $record->diagnosis ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="tindakan" value="Tindakan" />
        <textarea id="tindakan" name="tindakan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md" required>{{ old('tindakan', $record->tindakan ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('tindakan')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="resep" value="Resep (opsional)" />
        <textarea id="resep" name="resep" rows="3" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('resep', $record->resep ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('resep')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="catatan" value="Catatan (opsional)" />
        <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('catatan', $record->catatan ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="photos" value="Upload Foto (opsional, bisa lebih dari 1)" />
        <input type="file" id="photos" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp,image/*" class="mt-1 block w-full border-gray-300 rounded-md">
        <p class="text-xs text-gray-500 mt-1">
            Maks 5 file tiap upload, maks 5 MB per foto (JPG, JPEG, PNG, WEBP).
            @if($record)
                Foto tersimpan saat ini: {{ $existingPhotoCount }} (total maksimal 10 per rekam medis).
            @endif
        </p>
        <x-input-error :messages="$errors->get('photos')" class="mt-2" />
        <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />

        <div id="photo-preview-wrapper" class="mt-3 hidden">
            <p class="text-sm font-medium text-gray-700 mb-2">Preview foto yang akan diupload:</p>
            <div id="photo-preview-grid" class="grid grid-cols-2 md:grid-cols-5 gap-3"></div>
        </div>
    </div>
</div>

@once
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('photos');
        const wrapper = document.getElementById('photo-preview-wrapper');
        const grid = document.getElementById('photo-preview-grid');

        if (!input || !wrapper || !grid) return;

        input.addEventListener('change', function () {
            const files = Array.from(input.files || []);
            const maxFiles = 5;

            if (files.length > maxFiles) {
                alert('Maksimal 5 foto dalam sekali upload.');
                input.value = '';
                wrapper.classList.add('hidden');
                grid.innerHTML = '';
                return;
            }

            grid.innerHTML = '';

            if (!files.length) {
                wrapper.classList.add('hidden');
                return;
            }

            wrapper.classList.remove('hidden');

            files.forEach((file) => {
                if (!file.type.startsWith('image/')) return;

                const item = document.createElement('div');
                item.className = 'border rounded p-2';

                const img = document.createElement('img');
                img.className = 'w-full h-24 object-cover rounded';
                img.alt = file.name;

                const info = document.createElement('p');
                info.className = 'text-xs mt-2 break-all';
                info.textContent = `${file.name} (${Math.ceil(file.size / 1024)} KB)`;

                const reader = new FileReader();
                reader.onload = (e) => {
                    img.src = e.target?.result;
                };
                reader.readAsDataURL(file);

                item.appendChild(img);
                item.appendChild(info);
                grid.appendChild(item);
            });
        });
    });
</script>
@endonce
