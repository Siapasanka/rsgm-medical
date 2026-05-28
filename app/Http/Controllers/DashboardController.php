<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Registration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->loadMissing('role');
        $role = $user?->role?->name;
        $today = now()->toDateString();

        if (!in_array($role, ['superadmin', 'admin', 'petugas', 'dokter'])) {
            abort(403, 'Role belum diatur. Hubungi admin.');
        }

        $cards = [
            'total_pasien' => Patient::count(),
            'kunjungan_hari_ini' => Registration::whereDate('tanggal_kunjungan', $today)->count(),
            'menunggu' => Registration::whereDate('tanggal_kunjungan', $today)->where('status_antrian', 'menunggu')->count(),
            'diperiksa' => Registration::whereDate('tanggal_kunjungan', $today)->where('status_antrian', 'diperiksa')->count(),
            'selesai' => Registration::whereDate('tanggal_kunjungan', $today)->where('status_antrian', 'selesai')->count(),
            'rekam_medis_hari_ini' => MedicalRecord::whereHas('registration', fn ($q) => $q->whereDate('tanggal_kunjungan', $today))->count(),
        ];

        $queueToday = Registration::with('patient')
            ->whereDate('tanggal_kunjungan', $today)
            ->orderBy('nomor_antrian')
            ->limit(20)
            ->get();

        $recentActivities = AuditLog::with('user')
            ->latest('id')
            ->limit(10)
            ->get();

        $trend7DaysRaw = Registration::selectRaw('DATE(tanggal_kunjungan) as tanggal, COUNT(*) as total')
            ->whereDate('tanggal_kunjungan', '>=', now()->subDays(6)->toDateString())
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('total', 'tanggal');

        $trend7Days = collect(range(6, 0))->map(function ($daysAgo) use ($trend7DaysRaw) {
            $date = now()->subDays($daysAgo)->toDateString();

            return [
                'tanggal' => $date,
                'label' => now()->subDays($daysAgo)->translatedFormat('D, d M'),
                'total' => (int) ($trend7DaysRaw[$date] ?? 0),
            ];
        });

        $doctorPending = Registration::with('patient')
            ->whereDate('tanggal_kunjungan', $today)
            ->whereDoesntHave('medicalRecord')
            ->orderBy('nomor_antrian')
            ->limit(20)
            ->get();

        $myRecordsToday = MedicalRecord::with('registration.patient')
            ->where('dokter_id', $user->id)
            ->whereHas('registration', fn ($q) => $q->whereDate('tanggal_kunjungan', $today))
            ->latest('id')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'role',
            'cards',
            'queueToday',
            'recentActivities',
            'trend7Days',
            'doctorPending',
            'myRecordsToday'
        ));
    }
}
