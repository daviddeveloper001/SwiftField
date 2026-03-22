<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantLandingController;

use App\Livewire\Auth\TenantRegistration;

Route::get('/registrar-mi-negocio', TenantRegistration::class)->name('register');

Route::get('/{slug}', [TenantLandingController::class, 'show'])->name('tenant.landing');
