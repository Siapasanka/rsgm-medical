<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
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

        $filename = 'audit-logs-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'User', 'Action', 'Entity', 'Entity ID', 'Deskripsi']);

            foreach ($rows as $log) {
                fputcsv($handle, [
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'system',
                    $log->action,
                    $log->entity_type,
                    $log->entity_id,
                    $log->description,
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
                        ->orWhere('entity_type', 'like', "%{$q}%");
                });
            })
            ->when($filters['userId'], fn ($query) => $query->where('user_id', $filters['userId']))
            ->when($filters['dateFrom'], fn ($query) => $query->whereDate('created_at', '>=', $filters['dateFrom']))
            ->when($filters['dateTo'], fn ($query) => $query->whereDate('created_at', '<=', $filters['dateTo']));
    }
}
