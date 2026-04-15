<?php

use Livewire\Volt\Component; // Asegúrate de que use Volt
use App\Models\Persona;
use App\Models\Asistencia;

new class extends Component {
    public string $codigo = '';

    public function registrar()
    {
        $this->codigo = trim($this->codigo);

        if (empty($this->codigo)) {
            return;
        }

        $persona = Persona::where('codigo', $this->codigo)->first();

        if ($persona) {
            // 2. VALIDACIÓN: ¿Ya registró asistencia HOY?
            $yaExiste = Asistencia::where('codigo', $this->codigo)
                ->whereDate('created_at', now()->today()) // Filtra solo por la fecha de hoy
                ->exists();

            if ($yaExiste) {
                session()->flash('error', "Ya registró su entrada hoy.");
                $this->reset('codigo');
                return; // Detiene el proceso
            }

            // 3. Si no existe, procedemos a crear el registro
            Asistencia::create([
                'codigo' => $persona->codigo,
                'genero' => $persona->genero,
                'aula' => $persona->aula,
            ]);

            session()->flash('message', "Registrado: {$persona->nombre}");
        } else {
            session()->flash('error', "Código {$this->codigo} no encontrado");
        }

        $this->reset('codigo');
    }
};
?>