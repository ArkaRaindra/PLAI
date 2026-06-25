<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/exports/users', [ExportController::class, 'downloadUsers'])
    ->name('exports.users');
