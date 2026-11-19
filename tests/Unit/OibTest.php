<?php

use App\Rules\Oib;
use App\Support\Money;

it('accepts OIBs with a correct ISO 7064 check digit', function () {
    foreach (['1234567890', '9876543210', '0000000001'] as $first10) {
        expect(Oib::isValid($first10.Oib::checkDigit($first10)))->toBeTrue();
    }
});

it('rejects OIBs with a wrong check digit, wrong length or letters', function (string $oib) {
    expect(Oib::isValid($oib))->toBeFalse();
})->with([
    'wrong check digit' => fn () => '1234567890'.((Oib::checkDigit('1234567890') + 1) % 10),
    'too short' => '1234567890',
    'letters' => '1234567890A',
]);

it('formats money per locale', function () {
    expect(Money::format(1234.5, 'EUR', 'en'))->toBe('€1,234.50')
        ->and(Money::format(1234.5, 'EUR', 'hr'))->toBe('1.234,50 €')
        ->and(Money::compact(12500, 'en'))->toBe('€12.5k')
        ->and(Money::compact(2_500_000, 'hr'))->toBe('2,5 mil. €');
});
