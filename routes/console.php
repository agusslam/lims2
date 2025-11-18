<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// LIMS specific console commands
Artisan::command('lims:cleanup-sessions', function () {
    $this->info('Cleaning up expired sessions...');
    // Cleanup logic here
})->purpose('Clean up expired user sessions');

Artisan::command('lims:backup-database', function () {
    $this->info('Creating database backup...');
    // Backup logic here
})->purpose('Create database backup');

Artisan::command('lims:archive-old-samples', function () {
    $this->info('Archiving old completed samples...');
    // Archive logic here
})->purpose('Archive old completed samples');
