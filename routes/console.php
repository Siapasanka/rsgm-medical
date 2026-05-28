<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;
use Symfony\Component\Process\Process;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('backup:database {--days=14 : Hapus backup yang lebih lama dari X hari}', function () {
    $connection = config('database.default');

    if ($connection !== 'mysql') {
        $this->error('Backup command saat ini hanya mendukung database mysql.');
        return 1;
    }

    $host = config('database.connections.mysql.host');
    $port = (string) config('database.connections.mysql.port', '3306');
    $database = config('database.connections.mysql.database');
    $username = config('database.connections.mysql.username');
    $password = (string) config('database.connections.mysql.password');

    if (! $database || ! $username) {
        $this->error('Konfigurasi DB belum lengkap. Cek file .env (DB_DATABASE, DB_USERNAME).');
        return 1;
    }

    $backupDir = storage_path('app/backups/database');
    if (! File::exists($backupDir)) {
        File::makeDirectory($backupDir, 0755, true);
    }

    $fileName = sprintf('%s-%s.sql', $database, now()->format('Ymd-His'));
    $filePath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

    $mysqldumpPath = env('MYSQLDUMP_PATH', 'mysqldump');

    $command = [
        $mysqldumpPath,
        '--host=' . $host,
        '--port=' . $port,
        '--user=' . $username,
        '--single-transaction',
        '--quick',
        '--skip-lock-tables',
    ];

    if ($password !== '') {
        $command[] = '--password=' . $password;
    }

    $command[] = $database;
    $command[] = '--result-file=' . $filePath;

    $this->info('Menjalankan backup database...');

    $process = new Process($command, base_path(), null, null, 180);
    $process->run();

    if (! $process->isSuccessful()) {
        $this->error('Backup gagal: ' . $process->getErrorOutput());
        return 1;
    }

    $this->info('Backup sukses: ' . $filePath);

    $days = (int) $this->option('days');
    $deleted = 0;

    if ($days > 0) {
        $threshold = now()->subDays($days)->timestamp;
        foreach (File::files($backupDir) as $file) {
            if ($file->getExtension() === 'sql' && $file->getMTime() < $threshold) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }
    }

    $this->info("Cleanup selesai. File lama terhapus: {$deleted}");

    return 0;
})->purpose('Backup database MySQL ke storage/app/backups/database');

Schedule::command('backup:database --days=14')->dailyAt('01:00');
