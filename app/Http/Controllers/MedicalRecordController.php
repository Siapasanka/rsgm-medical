<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordPhoto;
use App\Models\Registration;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());

        $records = MedicalRecord::with(['registration.patient', 'doctor'])
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

    public function logs(Request $request)
    {
        $filters = [
            'q' => $request->query('q'),
            'action' => $request->query('action'),
            'user' => $request->query('user'), // <-- DIUBAH: Menangkap ketikan teks pencarian user
            'dateFrom' => $request->query('date_from'),
            'dateTo' => $request->query('date_to'),
            'recordId' => $request->query('record_id'),
        ];

        $logs = AuditLog::with('user')
            ->where('entity_type', 'medical_record')
            // FIX 1: Pembatasan whereIn('action') dihapus agar log Hapus Foto & Hapus RM ikut muncul!
            ->when($filters['recordId'], fn ($query) => $query->where('entity_id', $filters['recordId']))
            ->when($filters['q'], function ($query) use ($filters) {
                $q = $filters['q'];
                $query->where(function ($sub) use ($q) {
                    $sub->where('description', 'like', "%{$q}%")
                        ->orWhere('metadata', 'like', "%{$q}%");

                    if (is_numeric($q)) {
                        $sub->orWhere('entity_id', $q);
                    }
                });
            })
            ->when($filters['action'], fn ($query) => $query->where('action', $filters['action']))
            ->when($filters['user'], function ($query, $userName) {
                // FIX 2: Filter pencarian berdasarkan ketikan nama User/Dokter
                $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$userName}%"));
            })
            ->when($filters['dateFrom'], fn ($query) => $query->whereDate('created_at', '>=', $filters['dateFrom']))
            ->when($filters['dateTo'], fn ($query) => $query->whereDate('created_at', '<=', $filters['dateTo']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $recordIds = $logs->getCollection()
            ->pluck('entity_id')
            ->filter()
            ->unique();

        $recordsById = MedicalRecord::with(['registration.patient', 'doctor'])
            ->whereIn('id', $recordIds)
            ->get()
            ->keyBy('id');

        return view('medical-records.logs', [
            ...$filters,
            'logs' => $logs,
            'recordsById' => $recordsById,
        ]);
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
                    $path = Storage::disk('public')->putFile('medical-records', $photo);

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
        
        $record->load('registration.patient');

        Audit::log(
            action: 'create',
            entityType: 'medical_record',
            entityId: $record->id,
            description: 'Membuat rekam medis untuk '.$record->registration->patient->nama.' ('.$record->registration->patient->no_rm.') - pendaftaran #'.$record->registration->nomor_antrian,
            metadata: [
                'registration_id' => $record->registration_id,
                'patient_id' => $record->registration->patient_id,
                'patient_name' => $record->registration->patient->nama,
                'patient_no_rm' => $record->registration->patient->no_rm,
                'nomor_antrian' => $record->registration->nomor_antrian,
                'jumlah_foto' => $record->photos()->count(),
            ]
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
        ]);

        $recordData = collect($validated)->except(['photos'])->toArray();
        $changes = collect($recordData)
            ->filter(fn ($value, $field) => $medicalRecord->getOriginal($field) !== $value)
            ->map(fn ($value, $field) => [
                'before' => $medicalRecord->getOriginal($field),
                'after' => $value,
            ])
            ->all();
        $oldPhotoCount = $medicalRecord->photos()->count();
        $addedPhotoCount = count($request->file('photos', []));

        DB::transaction(function () use ($request, $recordData, $medicalRecord) {
            $medicalRecord->update($recordData);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = Storage::disk('public')->putFile('medical-records', $photo);

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
        
        $medicalRecord->load('registration.patient');

        Audit::log(
            action: 'update',
            entityType: 'medical_record',
            entityId: $medicalRecord->id,
            description: 'Mengubah rekam medis untuk '.$medicalRecord->registration->patient->nama.' ('.$medicalRecord->registration->patient->no_rm.') - pendaftaran #'.$medicalRecord->registration->nomor_antrian,
            metadata: [
                'registration_id' => $medicalRecord->registration_id,
                'patient_id' => $medicalRecord->registration->patient_id,
                'patient_name' => $medicalRecord->registration->patient->nama,
                'patient_no_rm' => $medicalRecord->registration->patient->no_rm,
                'nomor_antrian' => $medicalRecord->registration->nomor_antrian,
                'perubahan' => $changes,
                'jumlah_foto_sebelum' => $oldPhotoCount,
                'jumlah_foto_ditambah' => $addedPhotoCount,
                'jumlah_foto' => $medicalRecord->photos()->count(),
            ]
        );

        return redirect()->route('medical-records.show', $medicalRecord)->with('success', 'Rekam medis berhasil diupdate.');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load('registration.patient');
        $id = $medicalRecord->id;
        $photoCount = $medicalRecord->photos()->count();
        $patient = $medicalRecord->registration->patient;
        $nomorAntrian = $medicalRecord->registration->nomor_antrian;
        $registrationId = $medicalRecord->registration_id;

        foreach ($medicalRecord->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $medicalRecord->delete();

        Audit::log(
            action: 'delete',
            entityType: 'medical_record',
            entityId: $id,
            description: 'Menghapus rekam medis untuk '.$patient->nama.' ('.$patient->no_rm.') - pendaftaran #'.$nomorAntrian,
            metadata: [
                'registration_id' => $registrationId,
                'patient_id' => $patient->id,
                'patient_name' => $patient->nama,
                'patient_no_rm' => $patient->no_rm,
                'nomor_antrian' => $nomorAntrian,
                'jumlah_foto_terhapus' => $photoCount,
            ]
        );

        return redirect()->route('medical-records.index')->with('success', 'Rekam medis berhasil dihapus.');
    }

    public function destroyPhoto(MedicalRecord $medicalRecord, MedicalRecordPhoto $photo)
    {
        abort_unless($photo->medical_record_id === $medicalRecord->id, 404);
        $medicalRecord->load('registration.patient');

        $photoId = $photo->id;
        $fileName = $photo->file_name;
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        // FIX 3: Diikat langsung ke entity 'medical_record' dengan aksi 'update'
        Audit::log(
            action: 'update',
            entityType: 'medical_record',
            entityId: $medicalRecord->id,
            description: 'Menghapus foto klinis ('.$fileName.') dari rekam medis pasien '.$medicalRecord->registration->patient->nama,
            metadata: [
                'event' => 'hapus_foto',
                'file_name' => $fileName,
                'patient_name' => $medicalRecord->registration->patient->nama,
                'patient_no_rm' => $medicalRecord->registration->patient->no_rm,
            ]
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