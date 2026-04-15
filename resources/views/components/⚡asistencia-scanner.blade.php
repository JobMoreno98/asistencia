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

<div class="p-5">
    <form wire:submit.prevent="registrar">
        <input type="text" id="input-escaneo" wire:model="codigo"
            class="w-full p-4 text-3xl border-2 border-gray-300 rounded focus:border-blue-500 outline-none"
            placeholder="Esperando escaneo..." autofocus autocomplete="off">
        <button type="submit" class="hidden"></button>
    </form>

    <div class="mt-4 text-2xl">
        @if (session()->has('message'))
            <div class="text-green-600 font-bold">{{ session('message') }}</div>
        @endif

        @if (session()->has('error'))
            <div class="text-red-600 font-bold">{{ session('error') }}</div>
        @endif
    </div>

    <div wire:loading class="text-blue-500 animate-pulse">
        Registrando...
    </div>

    <script>
        // Mantiene el foco en el input pase lo que pase
        setInterval(() => {
            const el = document.getElementById('input-escaneo');
            if (document.activeElement !== el) el.focus();
        }, 100);
    </script>
</div>
