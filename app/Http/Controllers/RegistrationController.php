<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Registration;
use App\Support\Audit;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        $q = $request->query('q');
        $poli = $request->query('poli');

        $registrations = Registration::query()
            ->with(['patient', 'creator'])
            ->when($q, function ($query) use ($q) {
                // Cari berdasarkan Nomor Antrian atau Nama Pasien
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('nomor_antrian', 'like', "%{$q}%")
                        ->orWhereHas('patient', function ($patientQuery) use ($q) {
                            $patientQuery->where('nama', 'like', "%{$q}%");
                        });
                });
            })
            ->when($poli, function ($query) use ($poli) {
                // Filter berdasarkan Poli
                $query->where('poli', $poli);
            })
            ->when($tanggal, function ($query) use ($tanggal) {
                // Filter berdasarkan Tanggal Kunjungan
                $query->whereDate('tanggal_kunjungan', $tanggal);
            })
            ->orderBy('nomor_antrian')
            ->paginate(10)
            ->withQueryString();

        return view('registrations.index', compact('registrations', 'tanggal'));
    }

    public function create()
    {
        $patients = Patient::orderBy('nama')->get();
        return view('registrations.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'tanggal_kunjungan' => ['required', 'date'],
            'poli' => ['required', 'string', 'max:100'],
            'keluhan_utama' => ['nullable', 'string'],
        ]);

        // Nomor antrian otomatis per tanggal + poli
        $countTodayPoli = Registration::whereDate('tanggal_kunjungan', $validated['tanggal_kunjungan'])
            ->where('poli', $validated['poli'])
            ->count();

        $nextNumber = $countTodayPoli + 1;
        $validated['nomor_antrian'] = str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT); // 001, 002...
        $validated['status_antrian'] = 'menunggu';
        $validated['created_by'] = auth()->id();

        $registration = Registration::create($validated);
        $registration->load('patient');

        Audit::log(
            action: 'create',
            entityType: 'registration',
            entityId: $registration->id,
            description: 'Membuat pendaftaran #'.$registration->nomor_antrian.' untuk '.$registration->patient->nama.' ('.$registration->patient->no_rm.')',
            metadata: [
                'patient_id' => $registration->patient_id,
                'patient_name' => $registration->patient->nama,
                'patient_no_rm' => $registration->patient->no_rm,
                'nomor_antrian' => $registration->nomor_antrian,
                'tanggal_kunjungan' => $registration->tanggal_kunjungan?->format('Y-m-d'),
                'poli' => $registration->poli,
            ]
        );

        return redirect()->route('registrations.index', ['tanggal' => $validated['tanggal_kunjungan']])
            ->with('success', 'Pendaftaran berhasil dibuat. Nomor antrian: '.$validated['nomor_antrian']);
    }

    public function show(Registration $registration)
    {
        $registration->load(['patient', 'creator', 'medicalRecord']);
        return view('registrations.show', compact('registration'));
    }

    public function edit(Registration $registration)
    {
        $patients = Patient::orderBy('nama')->get();
        return view('registrations.edit', compact('registration', 'patients'));
    }

    public function update(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'tanggal_kunjungan' => ['required', 'date'],
            'poli' => ['required', 'string', 'max:100'],
            'keluhan_utama' => ['nullable', 'string'],
            'status_antrian' => ['required', 'in:menunggu,diperiksa,selesai'],
        ]);

        // Kalau tanggal/poli berubah, regenerate nomor antrian
        if (
            $registration->tanggal_kunjungan->format('Y-m-d') !== $validated['tanggal_kunjungan'] ||
            $registration->poli !== $validated['poli']
        ) {
            $countTodayPoli = Registration::whereDate('tanggal_kunjungan', $validated['tanggal_kunjungan'])
                ->where('poli', $validated['poli'])
                ->count();

            $validated['nomor_antrian'] = str_pad((string) ($countTodayPoli + 1), 3, '0', STR_PAD_LEFT);
        }

        $registration->update($validated);
        $registration->load('patient');

        Audit::log(
            action: 'update',
            entityType: 'registration',
            entityId: $registration->id,
            description: 'Mengubah pendaftaran #'.$registration->nomor_antrian.' untuk '.$registration->patient->nama.' ('.$registration->patient->no_rm.')',
            metadata: [
                'patient_id' => $registration->patient_id,
                'patient_name' => $registration->patient->nama,
                'patient_no_rm' => $registration->patient->no_rm,
                'nomor_antrian' => $registration->nomor_antrian,
                'tanggal_kunjungan' => $registration->tanggal_kunjungan?->format('Y-m-d'),
                'poli' => $registration->poli,
                'status_antrian' => $registration->status_antrian,
            ]
        );

        return redirect()->route('registrations.index', ['tanggal' => $validated['tanggal_kunjungan']])
            ->with('success', 'Data pendaftaran berhasil diupdate.');
    }

    public function destroy(Registration $registration)
    {
        $id = $registration->id;
        $noAntrian = $registration->nomor_antrian;
        $patient = $registration->patient;

        $registration->delete();

        Audit::log(
            action: 'delete',
            entityType: 'registration',
            entityId: $id,
            description: 'Menghapus pendaftaran #'.$noAntrian.' untuk '.$patient->nama.' ('.$patient->no_rm.')',
            metadata: [
                'patient_id' => $patient->id,
                'patient_name' => $patient->nama,
                'patient_no_rm' => $patient->no_rm,
                'nomor_antrian' => $noAntrian,
            ]
        );

        return redirect()->route('registrations.index')->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    public function exportDailyPdf(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());

        $registrations = Registration::with(['patient', 'creator'])
            ->whereDate('tanggal_kunjungan', $tanggal)
            ->orderBy('nomor_antrian')
            ->get();

        $summary = [
            'total' => $registrations->count(),
            'menunggu' => $registrations->where('status_antrian', 'menunggu')->count(),
            'diperiksa' => $registrations->where('status_antrian', 'diperiksa')->count(),
            'selesai' => $registrations->where('status_antrian', 'selesai')->count(),
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('registrations.export-daily-pdf', [
            'tanggal' => $tanggal,
            'registrations' => $registrations,
            'summary' => $summary,
            'printedAt' => now(),
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pendaftaran-harian-' . $tanggal . '.pdf');
    }
}