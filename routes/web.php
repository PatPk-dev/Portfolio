<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

// Frontend Portfolio
Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.index');

// Admin Panel Routes
Route::prefix('admin')->group(function () {
    Route::get('/', [PortfolioController::class, 'adminIndex'])->name('admin.dashboard');
    Route::post('/fetch-metadata', [PortfolioController::class, 'fetchMetadata'])->name('admin.fetch-metadata');
    Route::post('/projects', [PortfolioController::class, 'store'])->name('admin.projects.store');
    Route::delete('/projects/{id}', [PortfolioController::class, 'destroy'])->name('admin.projects.destroy');
});
