<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::middleware('auth')->group(function () {
    Route::get('/painel', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/agendamentos', [AppointmentController::class, 'index'])->name('admin.appointments');
    Route::get('/clientes', [ClientController::class, 'index'])->name('admin.clients');
    Route::get('/horarios', [AvailabilityController::class, 'index'])->name('admin.availabilities');

    // Rotas para gerenciamento de serviços
    Route::get('/servicos', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::post('/servicos', [ServiceController::class, 'store'])->name('admin.services.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
