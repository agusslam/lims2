<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\SystemSetting;

class LimsCleanupCommand extends Command
{
    protected $signature = 'lims:cleanup {--days=30 : Number of days to keep files} {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up old files, temporary data, and optimize storage';

    public function handle()
    {
        $this->info('🧹 LIMS System Cleanup Tool');
        $this->newLine();

        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = Carbon::now()->subDays($days);

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No files will be deleted');
            $this->newLine();
        }

        $this->info("🗓️  Cleaning files older than {$days} days (before {$cutoffDate->format('Y-m-d H:i:s')})");
        $this->newLine();

        // Clean up temporary files
        $this->cleanupTempFiles($cutoffDate, $dryRun);
        
        // Clean up old sample files
        $this->cleanupSampleFiles($cutoffDate, $dryRun);
        
        // Clean up session files
        $this->cleanupSessionFiles($cutoffDate, $dryRun);
        
        // Clean up log files
        $this->cleanupLogFiles($cutoffDate, $dryRun);
        
        // Clean up orphaned files
        $this->cleanupOrphanedFiles($dryRun);
        
        // Optimize database
        $this->optimizeDatabase($dryRun);

        $this->newLine();
        $this->info('🎉 Cleanup completed successfully!');
    }

    private function cleanupTempFiles($cutoffDate, $dryRun)
    {
        $this->info('🗂️  Cleaning temporary files...');
        
        $tempPaths = ['temp', 'uploads/temp', 'cache'];
        $deletedCount = 0;
        $freedSpace = 0;

        foreach ($tempPaths as $path) {
            if (!Storage::exists($path)) continue;

            $files = Storage::files($path);
            
            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp(Storage::lastModified($file));
                
                if ($lastModified->lt($cutoffDate)) {
                    $size = Storage::size($file);
                    
                    if (!$dryRun) {
                        Storage::delete($file);
                    }
                    
                    $deletedCount++;
                    $freedSpace += $size;
                }
            }
        }

        $this->displayCleanupResults('Temporary files', $deletedCount, $freedSpace, $dryRun);
    }

    private function cleanupSampleFiles($cutoffDate, $dryRun)
    {
        $this->info('🧪 Cleaning old sample files...');
        
        // Get sample files that are older than cutoff and sample is completed/archived
        $oldFiles = DB::table('sample_files')
            ->join('samples', 'sample_files.sample_id', '=', 'samples.id')
            ->where('sample_files.created_at', '<', $cutoffDate)
            ->whereIn('samples.status', ['completed', 'archived'])
            ->select('sample_files.*')
            ->get();

        $deletedCount = 0;
        $freedSpace = 0;

        foreach ($oldFiles as $fileRecord) {
            if (Storage::exists($fileRecord->file_path)) {
                $size = Storage::size($fileRecord->file_path);
                
                if (!$dryRun) {
                    Storage::delete($fileRecord->file_path);
                    DB::table('sample_files')->where('id', $fileRecord->id)->delete();
                }
                
                $deletedCount++;
                $freedSpace += $size;
            }
        }

        $this->displayCleanupResults('Sample files', $deletedCount, $freedSpace, $dryRun);
    }

    private function cleanupSessionFiles($cutoffDate, $dryRun)
    {
        $this->info('🔐 Cleaning old session files...');
        
        $sessionPath = storage_path('framework/sessions');
        $deletedCount = 0;
        $freedSpace = 0;

        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/*');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $lastModified = Carbon::createFromTimestamp(filemtime($file));
                    
                    if ($lastModified->lt($cutoffDate)) {
                        $size = filesize($file);
                        
                        if (!$dryRun) {
                            unlink($file);
                        }
                        
                        $deletedCount++;
                        $freedSpace += $size;
                    }
                }
            }
        }

        $this->displayCleanupResults('Session files', $deletedCount, $freedSpace, $dryRun);
    }

    private function cleanupLogFiles($cutoffDate, $dryRun)
    {
        $this->info('📝 Cleaning old log files...');
        
        $logPath = storage_path('logs');
        $deletedCount = 0;
        $freedSpace = 0;

        if (is_dir($logPath)) {
            $files = glob($logPath . '/laravel-*.log');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $lastModified = Carbon::createFromTimestamp(filemtime($file));
                    
                    if ($lastModified->lt($cutoffDate)) {
                        $size = filesize($file);
                        
                        if (!$dryRun) {
                            unlink($file);
                        }
                        
                        $deletedCount++;
                        $freedSpace += $size;
                    }
                }
            }
        }

        $this->displayCleanupResults('Log files', $deletedCount, $freedSpace, $dryRun);
    }

    private function cleanupOrphanedFiles($dryRun)
    {
        $this->info('🔍 Cleaning orphaned files...');
        
        // Find files in upload directories that don't have database records
        $uploadPaths = ['uploads/samples', 'uploads/certificates'];
        $deletedCount = 0;
        $freedSpace = 0;

        foreach ($uploadPaths as $path) {
            if (!Storage::exists($path)) continue;

            $files = Storage::files($path);
            
            foreach ($files as $file) {
                // Check if file exists in database
                $exists = DB::table('sample_files')
                    ->where('file_path', $file)
                    ->exists();

                if (!$exists) {
                    $size = Storage::size($file);
                    
                    if (!$dryRun) {
                        Storage::delete($file);
                    }
                    
                    $deletedCount++;
                    $freedSpace += $size;
                }
            }
        }

        $this->displayCleanupResults('Orphaned files', $deletedCount, $freedSpace, $dryRun);
    }

    private function optimizeDatabase($dryRun)
    {
        $this->info('🗄️  Optimizing database...');
        
        if (!$dryRun) {
            // Clean up old audit logs
            $auditRetentionDays = config('lims.security.audit_retention_days', 2555);
            $auditCutoff = Carbon::now()->subDays($auditRetentionDays);
            
            $deletedAudits = DB::table('audit_logs')
                ->where('created_at', '<', $auditCutoff)
                ->delete();

            // Optimize database tables
            $tables = ['samples', 'sample_files', 'audit_logs', 'sessions'];
            
            foreach ($tables as $table) {
                DB::statement("OPTIMIZE TABLE {$table}");
            }

            $this->info("✅ Database optimized, removed {$deletedAudits} old audit logs");
        } else {
            $this->info("✅ Database optimization would be performed");
        }
    }

    private function displayCleanupResults($type, $count, $size, $dryRun)
    {
        $sizeFormatted = $this->formatBytes($size);
        $action = $dryRun ? 'Would delete' : 'Deleted';
        
        if ($count > 0) {
            $this->info("✅ {$action} {$count} {$type} ({$sizeFormatted})");
        } else {
            $this->info("✅ No {$type} to clean");
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
