<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $patients = Patient::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")
                    ->orWhere('no_rm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', compact('patients', 'q'));
    }

    public function create()
    {
        $generatedNoRm = $this->generateNextNoRm();

        return view('patients.create', compact('generatedNoRm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required', 'digits:16', 'unique:patients,nik'],
            'nama' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'alamat' => ['required', 'string'],
            'no_hp' => ['required', 'string', 'max:20'],
            'gol_darah' => ['required', 'string', 'max:3'],
            'alergi' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            // Retry sederhana jika terjadi bentrok no_rm karena request bersamaan
            for ($i = 0; $i < 5; $i++) {
                $noRm = $this->generateNextNoRm();

                if (! Patient::where('no_rm', $noRm)->exists()) {
                    $patient = Patient::create([
                        ...$validated,
                        'no_rm' => $noRm,
                    ]);

                    Audit::log(
                        action: 'create',
                        entityType: 'patient',
                        entityId: $patient->id,
                        description: 'Membuat pasien baru: '.$patient->nama.' ('.$patient->no_rm.')',
                        metadata: ['nik' => $patient->nik]
                    );
                    return;
                }
            }

            abort(500, 'Gagal membuat nomor RM otomatis. Silakan ulangi.');
        });

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil ditambahkan. Nomor RM dibuat otomatis.');
    }

    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nik' => ['required', 'digits:16', Rule::unique('patients', 'nik')->ignore($patient->id)],
            'nama' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'alamat' => ['required', 'string'],
            'no_hp' => ['required', 'string', 'max:20'],
            'gol_darah' => ['required', 'string', 'max:3'],
            'alergi' => ['nullable', 'string'],
        ]);

        $patient->update($validated);

        Audit::log(
            action: 'update',
            entityType: 'patient',
            entityId: $patient->id,
            description: 'Mengubah data pasien: '.$patient->nama.' ('.$patient->no_rm.')',
        );

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil diupdate.');
    }

    public function destroy(Patient $patient)
    {
        $patientName = $patient->nama;
        $patientNoRm = $patient->no_rm;
        $patientId = $patient->id;

        $patient->delete();

        Audit::log(
            action: 'delete',
            entityType: 'patient',
            entityId: $patientId,
            description: 'Menghapus data pasien: '.$patientName.' ('.$patientNoRm.')',
            metadata: [
                'patient_name' => $patientName,
                'patient_no_rm' => $patientNoRm,
            ]
        );

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil dihapus.');
    }

    private function generateNextNoRm(): string
    {
        $lastNoRm = Patient::query()
            ->where('no_rm', 'like', 'RM%')
            ->orderByDesc('id')
            ->value('no_rm');

        $lastNumber = 0;
        if ($lastNoRm && preg_match('/^RM(\d+)$/', $lastNoRm, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'RM' . ($lastNumber + 1);
    }
}
