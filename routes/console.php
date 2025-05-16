<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendRentReminders;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Artisan::command('reminders:rent')
//     ->describe('Send rent due reminders to tenants')
//     ->uses(SendRentReminders::class)
//     ->purpose('Send rent due reminders to tenants');


