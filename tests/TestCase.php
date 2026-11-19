<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Sleep;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Auth timeboxing sleeps on failed logins; faking it keeps the suite fast.
        Sleep::fake();
    }
}
