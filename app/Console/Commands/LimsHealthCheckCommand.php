<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemSetting;

class LimsHealthCheckCommand extends Command
{
    protected $signature = 'lims:health-check';
    protected $description = 'Check LIMS system health and configuration';

    public function handle()
    {
        $this->info('LIMS System Health Check');
        $this->newLine();

        // Database connectivity
        $this->checkDatabase();
        
        // Storage permissions
        $this->checkStorage();
        
        // System settings
        $this->checkSettings();
        
        // File cleanup
        $this->checkFileCleanup();

        $this->newLine();
        $this->info('Health check completed.');
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            $this->info('✓ Database connection: OK');
            
            $userCount = DB::table('users')->count();
            $this->info("✓ Users in system: {$userCount}");
            
            $sampleCount = DB::table('samples')->count();
            $this->info("✓ Total samples: {$sampleCount}");
            
        } catch (\Exception $e) {
            $this->error('✗ Database connection failed: ' . $e->getMessage());
        }
    }

    private function checkStorage()
    {
        $paths = ['uploads/samples', 'certificates', 'temp'];
        
        foreach ($paths as $path) {
            if (Storage::exists($path)) {
                $this->info("✓ Storage path exists: {$path}");
            } else {
                Storage::makeDirectory($path);
                $this->warn("⚠ Created storage path: {$path}");
            }
        }
    }

    private function checkSettings()
    {
        $requiredSettings = [
            'lab_name',
            'lab_address', 
            'accreditation_number',
            'certificate_validity_days'
        ];

        foreach ($requiredSettings as $setting) {
            $value = SystemSetting::get($setting);
            if ($value) {
                $this->info("✓ Setting '{$setting}': configured");
            } else {
                $this->error("✗ Setting '{$setting}': missing");
            }
        }
    }

    private function checkFileCleanup()
    {
        $cleanupDays = (int) SystemSetting::get('file_cleanup_days', 30);
        $cutoffDate = now()->subDays($cleanupDays);
        
        $oldFiles = DB::table('sample_files')
                     ->where('created_at', '<', $cutoffDate)
                     ->count();
        
        if ($oldFiles > 0) {
            $this->warn("⚠ {$oldFiles} files older than {$cleanupDays} days found");
        } else {
            $this->info('✓ No old files requiring cleanup');
        }
    }
}
