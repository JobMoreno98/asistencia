<?php

use App\Http\Controllers\PersonaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/asistencias', 'reporte')->name('reporte');


Route::get('/reporte/{ciclo}', function ($ciclo) {

    return view('reporte', compact('ciclo'));
});


Route::get('/reporte-pdf/{ciclo}', [PersonaController::class, 'pdf']);


Route::get('/reporte/{ciclo}', function ($ciclo) {
    return view('reporte', compact('ciclo'));
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__ . '/settings.php';
