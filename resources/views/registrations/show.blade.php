<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pendaftaran</h2></x-slot>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <p><b>No Antrian:</b> {{ $registration->nomor_antrian }}</p>
                <p><b>Tanggal:</b> {{ $registration->tanggal_kunjungan?->format('d-m-Y') }}</p>
                <p><b>Pasien:</b> {{ $registration->patient->nama ?? '-' }}</p>
                <p><b>Poli:</b> {{ $registration->poli }}</p>
                <p><b>Status:</b> {{ $registration->status_antrian }}</p>
                <p><b>Keluhan:</b> {{ $registration->keluhan_utama }}</p>

                <div class="pt-3 flex items-center gap-3">
                    @if($registration->medicalRecord)
                        <a href="{{ route('medical-records.show', $registration->medicalRecord) }}" class="text-blue-600">Lihat Rekam Medis</a>
                    @else
                        <a href="{{ route('medical-records.create', ['registration_id' => $registration->id]) }}" class="text-green-600">Buat Rekam Medis</a>
                    @endif

                    <a href="{{ route('registrations.index') }}" class="text-gray-600">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
