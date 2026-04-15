<?php

namespace App\View\Components;

use App\Models\Asistencia;
use App\Models\Persona;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AsistenciaScanner extends Component
{
    /**
     * Create a new component instance.
     */
    public $codigo = '';

    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.asistencia-scanner');
    }

    public function registrar()
    {
        // Buscamos a la persona en tu tabla de usuarios/alumnos
        $persona = Persona::where('codigo', $this->codigo)->first();
        $yaRegistro = Asistencia::where('codigo_persona', $this->codigo)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();
        if ($yaRegistro) {
            Asistencia::create([
                'codigo_persona' => $persona->codigo,
                'genero' => $persona->genero,
                'aula' => $persona->aula_actual, // O el aula que definas
            ]);

            session()->flash('message', "Asistencia registrada: {$persona->nombre}");
        } else {
            session()->flash('error', "Código no reconocido");
        }

        $this->reset('codigo'); // Limpia el input para el siguiente escaneo
    }
}
