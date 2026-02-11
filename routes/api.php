<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/clients', function () {
    return \App\Models\Client::with('websites')->get();
});

Route::post('/websites', [\App\Http\Controllers\WebsiteController::class, 'store']);
Route::delete('/websites/{website}', [\App\Http\Controllers\WebsiteController::class, 'destroy']);
