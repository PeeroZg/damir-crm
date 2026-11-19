<?php

use App\Http\Controllers\Api\V1;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
| REST API v1 — authenticate with a personal token from the profile page:
|   Authorization: Bearer <id>|<secret>
*/
Route::prefix('v1')->middleware(['auth:api', 'throttle:api'])->name('api.')->group(function () {
    Route::get('me', fn (Request $request) => new UserResource($request->user()))->name('me');

    Route::get('companies', [V1\CompanyController::class, 'index'])->name('companies.index');
    Route::get('companies/{company}', [V1\CompanyController::class, 'show'])->name('companies.show');

    Route::get('contacts', [V1\ContactController::class, 'index'])->name('contacts.index');

    Route::get('deals', [V1\DealController::class, 'index'])->name('deals.index');
    Route::post('deals', [V1\DealController::class, 'store'])->name('deals.store');
    Route::get('deals/{deal}', [V1\DealController::class, 'show'])->name('deals.show');
    Route::patch('deals/{deal}/stage', [V1\DealController::class, 'updateStage'])->name('deals.stage');

    Route::get('offers', [V1\OfferController::class, 'index'])->name('offers.index');
    Route::get('offers/{offer}', [V1\OfferController::class, 'show'])->name('offers.show');
});
