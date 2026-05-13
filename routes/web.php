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
    Route::get('/agendamentos', [AppointmentController::class, 'index'])->name('appointments');
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients');


    // Rotas para gerenciamento de serviços
    Route::get('/servicos', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/servicos', [ServiceController::class, 'store'])->name('services.store');
    Route::delete('/servicos/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // Rota para exibir a agenda
    Route::get('/agenda', [ScheduleController::class, 'index'])->name('schedule.index');

    // Rotas para gerenciamento de horários
    Route::post('/horarios', [AvailabilityController::class, 'store'])->name('availabilities.store');
    Route::delete('/horarios/{id}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
