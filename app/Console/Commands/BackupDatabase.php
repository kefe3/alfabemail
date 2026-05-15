<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
        {--disk=local : Depolama diski}
        {--keep=7 : Kaç günlük backup saklanacağı}';

    protected $description = 'Veritabanı yedeği alır ve eski yedekleri temizler';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $keep = (int) $this->option('keep');
        $db = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $date = now()->format('Y-m-d_H-i-s');
        $filename = "backups/{$db}_{$date}.sql";

        $this->components->info("Veritabanı yedeği alınıyor: {$filename}");

        $this->ensureBackupDirectoryExists($disk);

        if (App::runningInConsole()) {
            $command = "mysqldump --no-tablespaces -h {$host} -u {$user} -p'{$pass}' {$db}";
            $output = shell_exec($command . ' 2>&1');
            $exitCode = (int) shell_exec('echo $?');

            if ($exitCode !== 0) {
                $this->components->error("mysqldump başarısız: " . substr((string) $output, 0, 500));
                return self::FAILURE;
            }

            Storage::disk($disk)->put($filename, $output);
        }

        $this->components->info("Yedek alındı: {$filename}");
        $this->cleanOldBackups($disk, $db, $keep);

        return self::SUCCESS;
    }

    protected function ensureBackupDirectoryExists(string $disk): void
    {
        $path = Storage::disk($disk)->path('backups');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    protected function cleanOldBackups(string $disk, string $db, int $keep): void
    {
        $files = Storage::disk($disk)->files("backups/{$db}_");
        $cutoff = now()->subDays($keep);

        $deleted = 0;
        foreach ($files as $file) {
            $timestamp = Storage::disk($disk)->lastModified($file);
            if ($timestamp && now()->createFromTimestamp($timestamp)->lt($cutoff)) {
                Storage::disk($disk)->delete($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->components->info("{$deleted} eski yedek temizlendi.");
        }
    }
}
