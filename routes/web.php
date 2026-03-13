<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantLandingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{slug}', [TenantLandingController::class, 'show'])->name('tenant.landing');
