<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

Route::post('/locale/{locale}', LocaleController::class)->name('locale');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/search', SearchController::class)->name('search');

    // Companies
    Route::get('/companies/kanban', [CompanyController::class, 'kanban'])->name('companies.kanban');
    Route::get('/companies/export', [CompanyController::class, 'export'])->name('companies.export');
    Route::patch('/companies/{company}/status', [CompanyController::class, 'updateStatus'])->name('companies.status');
    Route::resource('companies', CompanyController::class);

    // Contacts
    Route::get('/contacts/kanban', [ContactController::class, 'kanban'])->name('contacts.kanban');
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::patch('/contacts/{contact}/status', [ContactController::class, 'updateStatus'])->name('contacts.status');
    Route::resource('contacts', ContactController::class);

    // Deals
    Route::get('/deals/kanban', [DealController::class, 'kanban'])->name('deals.kanban');
    Route::get('/deals/export', [DealController::class, 'export'])->name('deals.export');
    Route::patch('/deals/{deal}/stage', [DealController::class, 'updateStage'])->name('deals.stage');
    Route::resource('deals', DealController::class);

    // Offers
    Route::get('/offers/kanban', [OfferController::class, 'kanban'])->name('offers.kanban');
    Route::patch('/offers/{offer}/status', [OfferController::class, 'updateStatus'])->name('offers.status');
    Route::post('/offers/{offer}/revision', [OfferController::class, 'revise'])->name('offers.revise');
    Route::get('/offers/{offer}/pdf', [OfferController::class, 'pdf'])->name('offers.pdf');
    Route::resource('offers', OfferController::class);

    // Activities & tasks
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::patch('/activities/{activity}/toggle', [ActivityController::class, 'toggle'])->name('activities.toggle');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

    // Team (admins)
    Route::resource('users', UserController::class)->except(['show', 'destroy']);

    // Profile & API tokens
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/tokens', [ProfileController::class, 'storeToken'])->name('profile.tokens.store');
    Route::delete('/profile/tokens/{token}', [ProfileController::class, 'destroyToken'])->name('profile.tokens.destroy');
});

require __DIR__.'/auth.php';
