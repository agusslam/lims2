<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LimsDatabaseBackupCommand extends Command
{
    protected $signature = 'lims:backup-database {--full : Include all data} {--structure-only : Export structure only}';
    protected $description = 'Backup LIMS database with compression and verification';

    public function handle()
    {
        $this->info('🔬 LIMS Database Backup Tool');
        $this->newLine();

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        // Determine backup type
        $backupType = $this->option('structure-only') ? 'structure' : 'full';
        $filename = "lims_backup_{$backupType}_{$timestamp}.sql";
        $backupPath = storage_path("app/backups/{$filename}");

        // Ensure backup directory exists
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
            $this->info('✅ Created backup directory');
        }

        // Build mysqldump command
        $command = sprintf(
            'mysqldump -h%s -P%d -u%s -p%s %s %s > %s',
            $host,
            $port,
            $username,
            $password,
            $this->option('structure-only') ? '--no-data' : '',
            $database,
            $backupPath
        );

        $this->info("🔄 Creating {$backupType} backup...");
        
        // Execute backup
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar === 0 && file_exists($backupPath)) {
            $fileSize = $this->formatBytes(filesize($backupPath));
            $this->info("✅ Backup created successfully");
            $this->info("📁 File: {$filename}");
            $this->info("📦 Size: {$fileSize}");

            // Compress backup
            if ($this->confirm('Compress backup file?', true)) {
                $this->compressBackup($backupPath, $filename);
            }

            // Verify backup
            if ($this->option('full') && $this->confirm('Verify backup integrity?', true)) {
                $this->verifyBackup($backupPath);
            }

            // Cleanup old backups
            $this->cleanupOldBackups();

            $this->newLine();
            $this->info('🎉 Database backup completed successfully!');
        } else {
            $this->error('❌ Backup failed');
            if (!empty($output)) {
                $this->error(implode("\n", $output));
            }
        }
    }

    private function compressBackup($backupPath, $filename)
    {
        $compressedPath = str_replace('.sql', '.sql.gz', $backupPath);
        
        $this->info('🗜️  Compressing backup...');
        exec("gzip {$backupPath}", $output, $returnVar);
        
        if ($returnVar === 0 && file_exists($compressedPath)) {
            $originalSize = $this->formatBytes(filesize($backupPath . '.gz'));
            $this->info("✅ Backup compressed: {$originalSize}");
        } else {
            $this->warn('⚠️  Compression failed or not available');
        }
    }

    private function verifyBackup($backupPath)
    {
        $this->info('🔍 Verifying backup integrity...');
        
        // Check if file is readable and has content
        if (!is_readable($backupPath) || filesize($backupPath) < 1000) {
            $this->error('❌ Backup file appears to be invalid');
            return false;
        }

        // Check for SQL structure
        $content = file_get_contents($backupPath, false, null, 0, 5000);
        if (!str_contains($content, 'CREATE TABLE') && !str_contains($content, 'INSERT INTO')) {
            $this->error('❌ Backup file does not contain valid SQL');
            return false;
        }

        $this->info('✅ Backup verification passed');
        return true;
    }

    private function cleanupOldBackups()
    {
        $retentionDays = config('lims.settings.backup_retention_days', 90);
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        
        $backupFiles = Storage::files('backups');
        $deletedCount = 0;

        foreach ($backupFiles as $file) {
            $lastModified = Carbon::createFromTimestamp(Storage::lastModified($file));
            
            if ($lastModified->lt($cutoffDate)) {
                Storage::delete($file);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("🧹 Cleaned up {$deletedCount} old backup files");
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
