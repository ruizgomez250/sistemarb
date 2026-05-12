<?php
// app/Models/Profesione.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesion extends Model
{
    protected $table = 'profesiones';
    
    protected $fillable = [
        'descripcion',
        'observacion'
    ];
    
    public function registros(): HasMany
    {
        return $this->hasMany(Registro::class);
    }
}