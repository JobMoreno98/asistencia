<?php

use Livewire\Volt\Component;
use App\Models\Persona;
use App\Models\Asistencia;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public string $codigo = '';

    public function with(): array
    {
        // Enviamos la lista de personas para la validación offline
        return [
            'dbLocal' => Persona::select('codigo', 'nombre', 'genero', 'aula')->get(),
        ];
    }

    public function sincronizarMasivo($lote)
    {
        \Log::info("Sincronizando lote de: " . count($lote));
        try {
            DB::beginTransaction();

            foreach ($lote as $item) {
                // Evitar duplicados en el servidor para el mismo día
                $yaExiste = Asistencia::where('codigo', $item['codigo'])
                    ->whereDate('created_at', now()->today())
                    ->exists();

                dd('Prueba');

                if (!$yaExiste) {
                    Asistencia::create([
                        'codigo' => $item['codigo'],
                        'genero' => $item['genero'],
                        'aula' => $item['aula'],
                        // Usamos la fecha real que capturó el dispositivo
                        'created_at' => $item['fecha'],
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
};
?>
