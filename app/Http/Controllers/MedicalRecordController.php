<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\MedicalRecordPhoto;
use App\Models\Registration;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());

        $records = MedicalRecord::with(['registration.patient', 'doctor', 'photos'])
            ->whereHas('registration', fn ($q) => $q->whereDate('tanggal_kunjungan', $tanggal))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $registrationsWithoutRecord = Registration::with('patient')
            ->whereDate('tanggal_kunjungan', $tanggal)
            ->whereDoesntHave('medicalRecord')
            ->orderBy('nomor_antrian')
            ->get();

        return view('medical-records.index', compact('records', 'tanggal', 'registrationsWithoutRecord'));
    }

    public function create(Request $request)
    {
        $registrationId = $request->query('registration_id');

        $registrations = Registration::with('patient')
            ->whereDoesntHave('medicalRecord')
            ->orderByDesc('tanggal_kunjungan')
            ->orderBy('nomor_antrian')
            ->get();

        return view('medical-records.create', compact('registrations', 'registrationId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => ['required', 'exists:registrations,id', 'unique:medical_records,registration_id'],
            'anamnesis' => ['nullable', 'string'],
            'pemeriksaan_fisik' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'tindakan' => ['nullable', 'string'],
            'resep' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'photos' => ['required', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'photos.required' => 'Foto wajib diupload.',
            'photos.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'photos.*.image' => 'File yang diupload harus berupa gambar.',
        ]);

        $record = DB::transaction(function () use ($request, $validated) {
            $record = MedicalRecord::create([
                ...collect($validated)->except(['photos'])->toArray(),
                'dokter_id' => auth()->id(),
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('medical-records', 'public');

                    $record->photos()->create([
                        'file_path' => $path,
                        'file_name' => $photo->getClientOriginalName(),
                        'mime_type' => $photo->getClientMimeType(),
                        'file_size' => $photo->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            $record->registration()->update(['status_antrian' => 'selesai']);

            return $record;
        });

        Audit::log(
            action: 'create',
            entityType: 'medical_record',
            entityId: $record->id,
            description: 'Membuat rekam medis untuk pendaftaran ID '.$record->registration_id,
            metadata: ['jumlah_foto' => $record->photos()->count()]
        );

        return redirect()->route('medical-records.index')->with('success', 'Rekam medis berhasil dibuat.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['registration.patient', 'doctor', 'photos.uploader']);

        return view('medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['registration.patient', 'photos']);

        return view('medical-records.edit', compact('medicalRecord'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'anamnesis' => ['nullable', 'string'],
            'pemeriksaan_fisik' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'tindakan' => ['nullable', 'string'],
            'resep' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'photos.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'photos.*.image' => 'File yang diupload harus berupa gambar.',
        ]);

        DB::transaction(function () use ($request, $validated, $medicalRecord) {
            $medicalRecord->update(collect($validated)->except(['photos'])->toArray());

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('medical-records', 'public');

                    $medicalRecord->photos()->create([
                        'file_path' => $path,
                        'file_name' => $photo->getClientOriginalName(),
                        'mime_type' => $photo->getClientMimeType(),
                        'file_size' => $photo->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }
        });

        Audit::log(
            action: 'update',
            entityType: 'medical_record',
            entityId: $medicalRecord->id,
            description: 'Mengubah rekam medis ID '.$medicalRecord->id,
            metadata: ['jumlah_foto' => $medicalRecord->photos()->count()]
        );

        return redirect()->route('medical-records.show', $medicalRecord)->with('success', 'Rekam medis berhasil diupdate.');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $id = $medicalRecord->id;
        $photoCount = $medicalRecord->photos()->count();

        foreach ($medicalRecord->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $medicalRecord->delete();

        Audit::log(
            action: 'delete',
            entityType: 'medical_record',
            entityId: $id,
            description: 'Menghapus rekam medis ID '.$id,
            metadata: ['jumlah_foto_terhapus' => $photoCount]
        );

        return redirect()->route('medical-records.index')->with('success', 'Rekam medis berhasil dihapus.');
    }

    public function destroyPhoto(MedicalRecord $medicalRecord, MedicalRecordPhoto $photo)
    {
        abort_unless($photo->medical_record_id === $medicalRecord->id, 404);

        $photoId = $photo->id;
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        Audit::log(
            action: 'delete',
            entityType: 'medical_record_photo',
            entityId: $photoId,
            description: 'Menghapus foto rekam medis ID '.$medicalRecord->id,
        );

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function exportDailyPdf(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());

        $records = MedicalRecord::with(['registration.patient', 'doctor', 'photos'])
            ->whereHas('registration', fn ($q) => $q->whereDate('tanggal_kunjungan', $tanggal))
            ->orderByDesc('id')
            ->get();

        $summary = [
            'total' => $records->count(),
            'total_foto' => $records->sum(fn ($row) => $row->photos->count()),
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('medical-records.export-daily-pdf', [
            'tanggal' => $tanggal,
            'records' => $records,
            'summary' => $summary,
            'printedAt' => now(),
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-rekam-medis-harian-' . $tanggal . '.pdf');
    }

    public function exportSinglePdf(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['registration.patient', 'doctor', 'photos']);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('medical-records.export-single-pdf', [
            'medicalRecord' => $medicalRecord,
            'printedAt' => now(),
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('rekam-medis-' . $medicalRecord->id . '.pdf');
    }
}
