<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::middleware('auth')->group(function () {
    Route::get('/painel', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas para gerenciamento de serviços
    Route::get('/servicos', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/servicos', [ServiceController::class, 'store'])->name('services.store');
    Route::delete('/servicos/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // Rota para exibir a agenda
    Route::get('/agenda', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

    // Rotas para gerenciamento de horários
    Route::post('/horarios', [AvailabilityController::class, 'store'])->name('availabilities.store');
    Route::delete('/horarios/{id}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');

    Route::get('/clientes', [ClientController::class, 'index'])->name('clients');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('perfil.destroy');
});

require __DIR__ . '/auth.php';
