<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pasien</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p><b>No RM:</b> {{ $patient->no_rm }}</p>
                <p><b>NIK:</b> {{ $patient->nik }}</p>
                <p><b>Nama:</b> {{ $patient->nama }}</p>
                <p><b>Tanggal Lahir:</b> {{ $patient->tgl_lahir?->format('d-m-Y') }}</p>
                <p><b>Jenis Kelamin:</b> {{ $patient->jenis_kelamin }}</p>
                <p><b>No HP:</b> {{ $patient->no_hp }}</p>
                <p><b>Golongan Darah:</b> {{ $patient->gol_darah }}</p>
                <p><b>Alamat:</b> {{ $patient->alamat }}</p>
                <p><b>Alergi:</b> {{ $patient->alergi }}</p>

                <a href="{{ route('patients.index') }}" class="inline-block mt-4 text-blue-600">← Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>