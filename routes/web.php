<?php

use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AppointmentController as GuestAppointmentController;
use App\Http\Controllers\ClientController as GuestClientController;

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

// Protected routes for authenticated users (admin)
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

    // Route for displaying the schedule
    Route::get('/agenda', [ScheduleController::class, 'index'])->name('schedule.index');

    // Routes for managing appointments (Admin)
    Route::post('/agendamentos', [AppointmentController::class, 'store'])->name('admin.appointments.store');

    // Routes for managing availabilities
    Route::post('/horarios', [AvailabilityController::class, 'store'])->name('availabilities.store');
    Route::delete('/horarios/{id}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');
});

// Public routes for clients to book appointments and manage their bookings
Route::get('/agendar', [GuestAppointmentController::class, 'create'])->name('agendar');
Route::post('/agendar', [GuestAppointmentController::class, 'store'])->name('appointments.store');
Route::put('/agendar/{id}', [GuestAppointmentController::class, 'update'])->name('appointments.update');

// Public routes for clients to view and manage their appointments
Route::get('/meus-agendamentos', [GuestClientController::class, 'myAppointments'])->name('clients.appointments');
Route::post('/cliente/login-otp', [GuestClientController::class, 'loginViaOtp'])->name('clients.loginOtp');
Route::post('/cliente/enviar-otp', [GuestClientController::class, 'sendOtp'])->name('clients.sendOtp');
Route::post('/cliente/logout', [GuestClientController::class, 'logout'])->name('clients.logout');

require __DIR__ . '/auth.php';
