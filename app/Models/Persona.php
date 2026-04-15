<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'genero',
        'aula',
    ];

    // Relación opcional: Una persona tiene muchas asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'codigo', 'codigo');
    }   
}
