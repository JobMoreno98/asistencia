<?php

namespace App\View\Components;

use App\Models\Asistencia;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class AsistenciaReporte extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.asistencia-reporte', [
            'porGenero' => Asistencia::select('genero', DB::raw('count(*) as total'))
                ->groupBy('genero')
                ->get(),
            'porAula' => Asistencia::select('aula', DB::raw('count(*) as total'))
                ->groupBy('aula')
                ->get()
        ]);
    }
}
