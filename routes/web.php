<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/companies/kanban', [\App\Http\Controllers\CompanyController::class, 'kanban'])->name('companies.kanban');
    Route::patch('/companies/{company}/status', [\App\Http\Controllers\CompanyController::class, 'updateStatus'])->name('companies.updateStatus');
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);

    Route::get('/contacts/kanban', [\App\Http\Controllers\ContactController::class, 'kanban'])->name('contacts.kanban');
    Route::patch('/contacts/{contact}/status', [\App\Http\Controllers\ContactController::class, 'updateStatus'])->name('contacts.updateStatus');
    Route::resource('contacts', \App\Http\Controllers\ContactController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
