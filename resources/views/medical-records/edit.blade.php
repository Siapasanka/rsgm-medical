<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Rekam Medis</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('medical-records.update', $medicalRecord) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('medical-records._form', ['medicalRecord' => $medicalRecord])

                    <div class="flex items-center gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
                        <a href="{{ route('medical-records.show', $medicalRecord) }}" class="text-gray-600">Batal</a>
                    </div>
                </form>
            </div>

            @if($medicalRecord->photos->count())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-3">Foto Saat Ini</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($medicalRecord->photos as $photo)
                            <div class="border rounded p-2">
                                <a href="{{ Storage::url($photo->file_path) }}" target="_blank">
                                    <img src="{{ Storage::url($photo->file_path) }}" class="w-full h-28 object-cover rounded" alt="{{ $photo->file_name }}">
                                </a>
                                <form method="POST" action="{{ route('medical-records.photos.destroy', [$medicalRecord, $photo]) }}" class="mt-2" data-confirm="Hapus foto ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 text-sm">Hapus Foto</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
