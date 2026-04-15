<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/asistencias', 'reporte')->name('reporte');


Route::get('/reporte', function () {
    // Esta vista SOLO debe tener el layout y llamar a @livewire('asistencia-reporte')
    return view('reporte'); 
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__ . '/settings.php';
