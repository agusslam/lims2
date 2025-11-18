<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\LimsHealthCheckCommand::class,
        Commands\LimsCleanupCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Daily cleanup
        $schedule->command('lims:cleanup --days=30')
                 ->daily()
                 ->at('02:00')
                 ->appendOutputTo(storage_path('logs/cleanup.log'));

        // Weekly database backup
        $schedule->command('lims:backup-database --full')
                 ->weekly()
                 ->sundays()
                 ->at('03:00')
                 ->appendOutputTo(storage_path('logs/backup.log'));

        // Daily health check
        $schedule->command('lims:health-check')
                 ->daily()
                 ->at('06:00')
                 ->appendOutputTo(storage_path('logs/health-check.log'));

        // Clear expired sessions
        $schedule->command('session:gc')
                 ->hourly();

        // Clear cache daily
        $schedule->command('cache:clear')
                 ->daily()
                 ->at('01:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
