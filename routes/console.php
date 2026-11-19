<?php

use Illuminate\Support\Facades\Schedule;

// Requires the standard Laravel cron entry: * * * * * php artisan schedule:run
Schedule::command('offers:expire')->dailyAt('01:00');
Schedule::command('demo:reset --force')->dailyAt('03:30')->when(fn () => config('crm.demo.enabled'));
