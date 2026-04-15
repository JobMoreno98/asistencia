<?php

namespace App\View\Components;

use App\Models\Asistencia;
use App\Models\Persona;
use Closure;
use Illuminate\Contracts\View\View;
use Livewire\Component;

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
        return view('livewire.asistencia-scanner');
    }

    public function registrar()
    {
        // Limpiamos espacios en blanco por si el escáner añade alguno
        $this->codigo = trim($this->codigo);

        if (empty($this->codigo)) return;

        $persona = Persona::where('codigo', $this->codigo)->first();

        if ($persona) {
            Asistencia::create([
                'codigo_persona' => $persona->codigo,
                'genero' => $persona->genero,
                'aula' => $persona->aula,
            ]);
            session()->flash('message', "✅ Asistencia: {$persona->nombre}");
        } else {
            session()->flash('error', "❌ Código {$this->codigo} no encontrado");
        }

        // Limpiamos para el siguiente
        $this->reset('codigo');
    }
}
