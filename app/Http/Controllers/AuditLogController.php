<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordPhoto;
use App\Models\Patient;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        $logs = $this->queryWithFilters($filters)
            ->latest()
            ->paginate(20)
            ->withQueryString();
        $this->decorateLogs($logs->getCollection());

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('audit-logs.index', [
            ...$filters,
            'logs' => $logs,
            'users' => $users,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);

        $rows = $this->queryWithFilters($filters)
            ->latest()
            ->get();
        $this->decorateLogs($rows);

        $filename = 'audit-logs-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'User', 'Aksi', 'Entity', 'Keterangan Entity', 'Deskripsi']);

            foreach ($rows as $log) {
                fputcsv($handle, [
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'system',
                    $log->action,
                    $log->entity_title,
                    $log->entity_subtitle,
                    $log->readable_description,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->filters($request);

        $logs = $this->queryWithFilters($filters)
            ->latest()
            ->get();
        $this->decorateLogs($logs);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('audit-logs.export-pdf', [
            ...$filters,
            'logs' => $logs,
            'printedAt' => now(),
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('audit-logs-' . now()->format('Ymd-His') . '.pdf');
    }

    private function filters(Request $request): array
    {
        return [
            'q' => $request->query('q'),
            'userId' => $request->query('user_id'),
            'dateFrom' => $request->query('date_from'),
            'dateTo' => $request->query('date_to'),
        ];
    }

    private function queryWithFilters(array $filters): Builder
    {
        return AuditLog::with('user')
            ->when($filters['q'], function ($query) use ($filters) {
                $q = $filters['q'];
                $query->where(function ($sub) use ($q) {
                    $sub->where('description', 'like', "%{$q}%")
                        ->orWhere('action', 'like', "%{$q}%")
                        ->orWhere('entity_type', 'like', "%{$q}%")
                        ->orWhere('metadata', 'like', "%{$q}%");
                });
            })
            ->when($filters['userId'], fn ($query) => $query->where('user_id', $filters['userId']))
            ->when($filters['dateFrom'], fn ($query) => $query->whereDate('created_at', '>=', $filters['dateFrom']))
            ->when($filters['dateTo'], fn ($query) => $query->whereDate('created_at', '<=', $filters['dateTo']));
    }

    private function decorateLogs($logs): void
    {
        $context = $this->resolveAuditEntities($logs);

        $logs->each(function (AuditLog $log) use ($context) {
            [$title, $subtitle] = $this->entityDisplay($log, $context);

            $log->entity_title = $title;
            $log->entity_subtitle = $subtitle;
            $log->readable_description = $this->readableDescription($log, $context);
        });
    }

    private function resolveAuditEntities($logs): array
    {
        $patientIds = $logs
            ->where('entity_type', 'patient')
            ->pluck('entity_id')
            ->filter()
            ->unique();

        $registrationIds = $logs
            ->where('entity_type', 'registration')
            ->pluck('entity_id')
            ->filter()
            ->unique()
            ->merge($logs
                ->pluck('metadata.registration_id')
                ->filter()
                ->unique());

        $medicalRecordIds = $logs
            ->where('entity_type', 'medical_record')
            ->pluck('entity_id')
            ->filter()
            ->unique();

        $photoIds = $logs
            ->where('entity_type', 'medical_record_photo')
            ->pluck('entity_id')
            ->filter()
            ->unique();

        return [
            'patients' => Patient::whereIn('id', $patientIds)->get()->keyBy('id'),
            'registrations' => Registration::with('patient')->whereIn('id', $registrationIds)->get()->keyBy('id'),
            'medicalRecords' => MedicalRecord::with(['registration.patient', 'doctor'])->whereIn('id', $medicalRecordIds)->get()->keyBy('id'),
            'photos' => MedicalRecordPhoto::with('medicalRecord.registration.patient')->whereIn('id', $photoIds)->get()->keyBy('id'),
        ];
    }

    private function entityDisplay(AuditLog $log, array $context): array
    {
        return match ($log->entity_type) {
            'patient' => $this->patientDisplay($log, $context),
            'registration' => $this->registrationDisplay($log, $context),
            'medical_record' => $this->medicalRecordDisplay($log, $context),
            'medical_record_photo' => $this->medicalRecordPhotoDisplay($log, $context),
            default => [
                ucwords(str_replace('_', ' ', (string) $log->entity_type)),
                'Referensi sistem #' . $log->entity_id,
            ],
        };
    }

    private function readableDescription(AuditLog $log, array $context): string
    {
        $verb = match ($log->action) {
            'create' => 'Membuat',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            default => ucfirst((string) $log->action),
        };

        return match ($log->entity_type) {
            'patient' => $this->patientDescription($log, $context, $verb),
            'registration' => $this->registrationDescription($log, $context, $verb),
            'medical_record' => $this->medicalRecordDescription($log, $context, $verb),
            'medical_record_photo' => $this->medicalRecordPhotoDescription($log, $context, $verb),
            default => $log->description,
        };
    }

    private function patientDisplay(AuditLog $log, array $context): array
    {
        $patient = $context['patients']->get((int) $log->entity_id);

        if (! $patient) {
            if ($label = $this->metadataPatientLabel($log)) {
                return ['Pasien', $label];
            }

            return ['Pasien', 'Data pasien sudah dihapus'];
        }

        return ['Pasien', $this->patientLabel($patient)];
    }

    private function registrationDisplay(AuditLog $log, array $context): array
    {
        $registration = $context['registrations']->get((int) $log->entity_id);

        if (! $registration) {
            if ($label = $this->metadataPatientLabel($log)) {
                $metadata = $log->metadata ?? [];

                return [
                    'Pendaftaran',
                    '#' . ($metadata['nomor_antrian'] ?? '-') . ' - ' . $label,
                ];
            }

            return ['Pendaftaran', 'Data pendaftaran sudah dihapus'];
        }

        return [
            'Pendaftaran',
            '#' . $registration->nomor_antrian . ' - ' . $this->patientLabel($registration->patient),
        ];
    }

    private function medicalRecordDisplay(AuditLog $log, array $context): array
    {
        $record = $context['medicalRecords']->get((int) $log->entity_id);

        if (! $record) {
            if ($label = $this->metadataPatientLabel($log)) {
                $metadata = $log->metadata ?? [];

                return [
                    'Rekam Medis',
                    $label . ' | Antrian #' . ($metadata['nomor_antrian'] ?? '-'),
                ];
            }

            return ['Rekam Medis', 'Data rekam medis sudah dihapus'];
        }

        return [
            'Rekam Medis',
            $this->patientLabel($record->registration?->patient) . ' | Antrian #' . ($record->registration?->nomor_antrian ?? '-'),
        ];
    }

    private function medicalRecordPhotoDisplay(AuditLog $log, array $context): array
    {
        $photo = $context['photos']->get((int) $log->entity_id);

        if (! $photo) {
            if ($label = $this->metadataPatientLabel($log)) {
                $metadata = $log->metadata ?? [];

                return [
                    'Foto Rekam Medis',
                    ($metadata['file_name'] ?? 'Foto') . ' | ' . $label,
                ];
            }

            return ['Foto Rekam Medis', 'Data foto sudah dihapus'];
        }

        return [
            'Foto Rekam Medis',
            ($photo->file_name ?: 'Foto') . ' | ' . $this->patientLabel($photo->medicalRecord?->registration?->patient),
        ];
    }

    private function patientDescription(AuditLog $log, array $context, string $verb): string
    {
        $patient = $context['patients']->get((int) $log->entity_id);

        return $patient
            ? $verb . ' data pasien ' . $this->patientLabel($patient)
            : ($this->metadataPatientLabel($log)
                ? $verb . ' data pasien ' . $this->metadataPatientLabel($log)
                : $log->description);
    }

    private function registrationDescription(AuditLog $log, array $context, string $verb): string
    {
        $registration = $context['registrations']->get((int) $log->entity_id);

        if (! $registration) {
            if ($label = $this->metadataPatientLabel($log)) {
                $metadata = $log->metadata ?? [];

                return $verb . ' pendaftaran #' . ($metadata['nomor_antrian'] ?? '-')
                    . ' untuk ' . $label;
            }

            return $log->description;
        }

        return $verb . ' pendaftaran #' . $registration->nomor_antrian
            . ' untuk ' . $this->patientLabel($registration->patient)
            . ' pada ' . ($registration->tanggal_kunjungan?->format('d-m-Y') ?? '-')
            . ' (' . $registration->poli . ')';
    }

    private function medicalRecordDescription(AuditLog $log, array $context, string $verb): string
    {
        $record = $context['medicalRecords']->get((int) $log->entity_id);

        if (! $record) {
            if ($label = $this->metadataPatientLabel($log)) {
                $metadata = $log->metadata ?? [];

                return $verb . ' rekam medis untuk ' . $label
                    . ' - pendaftaran #' . ($metadata['nomor_antrian'] ?? '-');
            }

            return $log->description;
        }

        return $verb . ' rekam medis untuk ' . $this->patientLabel($record->registration?->patient)
            . ' - pendaftaran #' . ($record->registration?->nomor_antrian ?? '-')
            . ' tanggal ' . ($record->registration?->tanggal_kunjungan?->format('d-m-Y') ?? '-');
    }

    private function medicalRecordPhotoDescription(AuditLog $log, array $context, string $verb): string
    {
        $photo = $context['photos']->get((int) $log->entity_id);

        if (! $photo) {
            if ($label = $this->metadataPatientLabel($log)) {
                $metadata = $log->metadata ?? [];

                return $verb . ' foto rekam medis untuk ' . $label
                    . ': ' . ($metadata['file_name'] ?? 'foto');
            }

            return $log->description;
        }

        return $verb . ' foto rekam medis untuk '
            . $this->patientLabel($photo->medicalRecord?->registration?->patient)
            . ': ' . ($photo->file_name ?: 'foto');
    }

    private function patientLabel($patient): string
    {
        if (! $patient) {
            return 'pasien tidak ditemukan';
        }

        return $patient->nama . ' (' . $patient->no_rm . ')';
    }

    private function metadataPatientLabel(AuditLog $log): ?string
    {
        $metadata = $log->metadata ?? [];
        $name = $metadata['patient_name'] ?? null;
        $noRm = $metadata['patient_no_rm'] ?? null;

        if (! $name) {
            return null;
        }

        return $noRm ? $name . ' (' . $noRm . ')' : $name;
    }
}
