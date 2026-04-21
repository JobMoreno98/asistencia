<?php

use Livewire\Volt\Component;
use App\Models\Asistencia;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;

new class extends Component {
    // Función para obtener los datos procesados
    public function with(): array
    {
        // 1. Conteo por género para las cards superiores
        $porGenero = Asistencia::select('genero', DB::raw('count(*) as total'))->groupBy('genero')->get();

        // 2. Listado por aula: Cruza la tabla personas (esperados) con asistencias (registrados)
        // Usamos un LEFT JOIN para asegurar que aparezcan las aulas incluso si tienen 0 registrados
        $reporteAulas = DB::table('personas')
        ->select('aula', DB::raw('count(*) as esperados'), DB::raw('(SELECT count(*) FROM asistencias WHERE asistencias.aula = personas.aula) as registrados'))
        ->groupBy('aula')->orderBy('registrados', 'desc')->get();

        $total = Persona::get();

        $asistidos = Asistencia::get();

        return [
            'porGenero' => $porGenero,
            'reporteAulas' => $reporteAulas,
            'totalRegistrados' => $asistidos,
            'totalEsperados' => $total,
        ];
    }
};
?>
