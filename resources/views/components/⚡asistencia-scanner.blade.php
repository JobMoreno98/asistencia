<?php

use Livewire\Volt\Component;
use App\Models\Persona;
use App\Models\Asistencia;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public string $codigo = '';

    public function with(): array
    {
        // 1. Obtenemos los códigos que YA registraron asistencia hoy
        $asistenciasHoy = Asistencia::whereDate('created_at', now()->today())
            ->pluck('codigo') // Trae solo un array de códigos ['123', '456']
            ->toArray();

        // 2. Traemos a las personas
        $personas = Persona::select('codigo', 'nombre', 'genero', 'aula')->get();

        return [
            'dbLocal' => $personas,
            'yaRegistrados' => $asistenciasHoy, // Enviamos este array extra
        ];
    }

    public function sincronizarMasivo($lote)
    {
        \Log::info('Sincronizando lote de: ' . count($lote));
        try {
            DB::beginTransaction();

            foreach ($lote as $item) {
                // Evitar duplicados en el servidor para el mismo día
                $yaExiste = Asistencia::where('codigo', $item['codigo'])
                    ->whereDate('created_at', now()->today())
                    ->exists();

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
