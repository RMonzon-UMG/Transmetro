<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LineaController;
use App\Http\Controllers\EstacionController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\PilotoController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\ReporteController;

// ── Auth ──────────────────────────────────────────────────────
Route::get('/login',   [LoginController::class, 'showLogin'])->name('login');
Route::post('/login',  [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Rutas protegidas ──────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard (Tarea 5)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Módulo Líneas (Tarea 6)
    Route::resource('lineas', LineaController::class);

    // Módulo Estaciones (Tarea 7)
    Route::resource('estaciones', EstacionController::class)->parameters(['estaciones' => 'estacion']);

    // Módulo Buses (Tarea 8)
    Route::resource('buses', BusController::class)->parameters(['buses' => 'bus']);

    // Módulo Pilotos (Tarea 9)
    Route::resource('pilotos', PilotoController::class);

    // Panel Alertas (Tarea 10)
    Route::get('/alertas',                    [AlertaController::class, 'index'])->name('alertas.index');
    Route::post('/alertas/{alerta}/atender',  [AlertaController::class, 'atender'])->name('alertas.atender');
    Route::post('/alertas/registrar-espera',  [AlertaController::class, 'registrarEspera'])->name('alertas.registrar-espera');

    // Módulo Reportes (Tarea 11) — solo admin
    Route::middleware('rol:admin')->group(function () {
        Route::get('/reportes',           [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/rf19',      [ReporteController::class, 'rf19'])->name('reportes.rf19');
        Route::get('/reportes/rf19/pdf',  fn() => app(ReporteController::class)->rf19('pdf'))->name('reportes.rf19.pdf');
        Route::get('/reportes/rf20',      [ReporteController::class, 'rf20'])->name('reportes.rf20');
        Route::get('/reportes/rf20/pdf',  fn() => app(ReporteController::class)->rf20('pdf'))->name('reportes.rf20.pdf');
        Route::get('/reportes/rf21',      [ReporteController::class, 'rf21'])->name('reportes.rf21');
        Route::get('/reportes/rf21/pdf',  fn() => app(ReporteController::class)->rf21('pdf'))->name('reportes.rf21.pdf');
    });
});
