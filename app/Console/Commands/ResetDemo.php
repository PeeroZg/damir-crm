<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('demo:reset {--force : Skip the confirmation prompt}')]
#[Description('Wipe the database and load fresh demo data (only when DEMO_MODE=true)')]
class ResetDemo extends Command
{
    public function handle(): int
    {
        if (! config('crm.demo.enabled')) {
            $this->error('DEMO_MODE is not enabled — refusing to wipe the database.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('This deletes ALL data and loads demo data. Continue?')) {
            return self::FAILURE;
        }

        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $this->info('Demo data reloaded.');

        return self::SUCCESS;
    }
}
