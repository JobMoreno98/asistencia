<?php

namespace App\Livewire; // Asegúrate de que el namespace sea Livewire

use App\Models\Asistencia;
use App\Models\Persona;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsistenciaReporte extends Component
{
    public function render(): View
    {
        // Conteo de registros actuales por género
        $asistencias = Asistencia::select('genero', DB::raw('count(*) as total'))
            ->groupBy('genero')
            ->get();

        // Obtener listado por aula comparando Registrados vs Esperados
        $reporteAulas = DB::table('personas')
            ->select(
                'aula',
                DB::raw('count(*) as esperados'),
                DB::raw('(SELECT count(*) FROM asistencias WHERE asistencias.aula = personas.aula) as registrados')
            )
            ->groupBy('aula')
            ->get();

        // IMPORTANTE: Retornar la vista y pasar los datos en un array
        return view('livewire.asistencia-reporte', [
            'porGenero' => $asistencias,
            'reporteAulas' => $reporteAulas,
            'totalRegistrados' => Asistencia::count(),
            'totalEsperados' => Persona::count(),
        ]);
    }
}