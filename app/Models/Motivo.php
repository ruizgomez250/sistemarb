<?php
// app/Models/Motivo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Motivo extends Model
{
    protected $table = 'motivos';
    
    protected $fillable = [
        'descripcion',
        'observacion'
    ];
    
    public function registros(): HasMany
    {
        return $this->hasMany(Registro::class);
    }
}