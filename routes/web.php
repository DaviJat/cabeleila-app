<?php

use App\Http\Controllers\Admin\ProfileController;
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
    // Routes for the admin dashboard
    Route::get('/painel', [DashboardController::class, 'index'])->name('dashboard');

    // Routes for managing services
    Route::get('/servicos', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/servicos', [ServiceController::class, 'store'])->name('services.store');
    Route::delete('/servicos/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // Routes for managing clients
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clientes', [ClientController::class, 'store'])->name('clients.store');
    Route::delete('/clientes/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Routes for managing the admin's profile
    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route for displaying the schedule
    Route::get('/agenda', [ScheduleController::class, 'index'])->name('schedule.index');

    // Routes for managing appointments
    Route::post('/agendamentos', [AppointmentController::class, 'store'])->name('appointments.store');

    // Routes for managing availabilities
    Route::post('/horarios', [AvailabilityController::class, 'store'])->name('availabilities.store');
    Route::delete('/horarios/{id}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');
});

require __DIR__ . '/auth.php';
