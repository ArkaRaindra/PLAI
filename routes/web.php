<?php

use App\Http\Controllers\EvidenceFileController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/exports/users', [ExportController::class, 'downloadUsers'])
    ->name('exports.users');

Route::middleware(['web', 'auth'])->prefix('super-admin')->group(function (): void {
    Route::get('evidence-files/{version}/preview', [EvidenceFileController::class, 'preview'])
        ->name('evidence-files.preview');
    Route::get('evidence-files/{version}', [EvidenceFileController::class, 'download'])
        ->name('evidence-files.download');
});
