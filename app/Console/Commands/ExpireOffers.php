<?php

namespace App\Console\Commands;

use App\Services\OfferService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('offers:expire')]
#[Description('Mark sent offers whose validity date has passed as expired')]
class ExpireOffers extends Command
{
    public function handle(OfferService $offers): int
    {
        $count = $offers->expireOverdue();

        $this->info("Expired {$count} offer(s).");

        return self::SUCCESS;
    }
}
