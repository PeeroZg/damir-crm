<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::get('/dashboard', function () {
    $stats = [
        'companies' => \App\Models\Company::count(),
        'contacts' => \App\Models\Contact::count(),
        'deals' => \App\Models\Deal::count(),
        'offers' => \App\Models\Offer::count(),
        'users' => \App\Models\User::count(),
        'open_deals_value' => \App\Models\Deal::whereNotIn('status', ['dobiven', 'izgubljen'])->sum('value'),
        'won_deals_value' => \App\Models\Deal::where('status', 'dobiven')->sum('value'),
    ];
    return view('dashboard', compact('stats'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/companies/kanban', [\App\Http\Controllers\CompanyController::class, 'kanban'])->name('companies.kanban');
    Route::patch('/companies/{company}/status', [\App\Http\Controllers\CompanyController::class, 'updateStatus'])->name('companies.updateStatus');
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);

    Route::get('/contacts/kanban', [\App\Http\Controllers\ContactController::class, 'kanban'])->name('contacts.kanban');
    Route::patch('/contacts/{contact}/status', [\App\Http\Controllers\ContactController::class, 'updateStatus'])->name('contacts.updateStatus');
    Route::resource('contacts', \App\Http\Controllers\ContactController::class);

    Route::get('/deals/kanban', [\App\Http\Controllers\DealController::class, 'kanban'])->name('deals.kanban');
    Route::patch('/deals/{deal}/status', [\App\Http\Controllers\DealController::class, 'updateStatus'])->name('deals.updateStatus');
    Route::resource('deals', \App\Http\Controllers\DealController::class);

    Route::get('/offers/kanban', [\App\Http\Controllers\OfferController::class, 'kanban'])->name('offers.kanban');
    Route::patch('/offers/{offer}/status', [\App\Http\Controllers\OfferController::class, 'updateStatus'])->name('offers.updateStatus');
    Route::get('/offers/{offer}/pdf', [\App\Http\Controllers\OfferController::class, 'pdf'])->name('offers.pdf');
    Route::resource('offers', \App\Http\Controllers\OfferController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
