<?php
// app/Models/Registro.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registro extends Model
{
    protected $table = 'registros';
    
    protected $fillable = [
        'cedula',
        'nombres_y_apellidos',
        'telefono1',
        'telefono2',
        'telefono3',
        'direccion',
        'barrio',
        'observacion_general',
        'motivo_id',
        'fecha_nacimiento',
        'afiliacion',
        'profesion_id',
        'local_interna',
        'local_generales'
    ];
    
    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];
    
    public function motivo(): BelongsTo
    {
        return $this->belongsTo(Motivo::class);
    }
    
    public function profesion(): BelongsTo
    {
        return $this->belongsTo(Profesion::class);
    }
}