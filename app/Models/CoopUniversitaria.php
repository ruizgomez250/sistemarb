<?php
// app/Models/CoopUniversitaria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoopUniversitaria extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coop_universitaria';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Si no tienes created_at y updated_at

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'socio',
        'cedula',
        'situacion',
        'telefono',
        'mesa',
        'orden',
        'depto',
        'ciudad'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'socio' => 'integer',
        'orden' => 'integer',
        'situacion' => 'boolean',
    ];

    /**
     * Get the socio's full info.
     */
    public function getNombreCompletoAttribute()
    {
        return 'Socio N° ' . $this->socio . ' - Cédula: ' . $this->cedula;
    }

    /**
     * Get formatted phone number.
     */
    public function getTelefonoFormateadoAttribute()
    {
        if (empty($this->telefono)) {
            return 'No registrado';
        }
        
        // Formatear teléfono (ej: 0981123456 -> (0981) 123-456)
        $telefono = preg_replace('/[^0-9]/', '', $this->telefono);
        $longitud = strlen($telefono);
        
        if ($longitud == 10) {
            return '(' . substr($telefono, 0, 4) . ') ' . substr($telefono, 4, 3) . '-' . substr($telefono, 7, 3);
        } elseif ($longitud == 7) {
            return substr($telefono, 0, 3) . '-' . substr($telefono, 3, 4);
        }
        
        return $this->telefono;
    }

    /**
     * Get situacion text.
     */
    public function getSituacionTextAttribute()
    {
        $situaciones = [
            0 => 'Inactivo',
            1 => 'Activo',
            2 => 'Suspendido',
            3 => 'En proceso'
        ];
        return $situaciones[$this->situacion] ?? 'Desconocido';
    }

    /**
     * Get situacion badge.
     */
    public function getSituacionBadgeAttribute()
    {
        $badges = [
            0 => '<span class="badge bg-danger">Inactivo</span>',
            1 => '<span class="badge bg-success">Activo</span>',
            2 => '<span class="badge bg-warning">Suspendido</span>',
            3 => '<span class="badge bg-info">En proceso</span>'
        ];
        return $badges[$this->situacion] ?? '<span class="badge bg-secondary">Desconocido</span>';
    }

    /**
     * Get situacion color class.
     */
    public function getSituacionColorAttribute()
    {
        $colors = [
            0 => 'danger',
            1 => 'success',
            2 => 'warning',
            3 => 'info'
        ];
        return $colors[$this->situacion] ?? 'secondary';
    }

    /**
     * Get mesa formatted.
     */
    public function getMesaFormateadaAttribute()
    {
        return $this->mesa ? 'Mesa ' . $this->mesa : 'Sin asignar';
    }

    /**
     * Get ubicación completa.
     */
    public function getUbicacionCompletaAttribute()
    {
        $ubicacion = [];
        if ($this->depto) $ubicacion[] = $this->depto;
        if ($this->ciudad) $ubicacion[] = $this->ciudad;
        
        return !empty($ubicacion) ? implode(', ', $ubicacion) : 'No especificada';
    }

    /**
     * Get departamento and ciudad as array.
     */
    public function getUbicacionArrayAttribute()
    {
        return [
            'departamento' => $this->depto,
            'ciudad' => $this->ciudad
        ];
    }

    /**
     * Check if socio is active.
     */
    public function getEsActivoAttribute()
    {
        return $this->situacion == 1;
    }

    /**
     * Check if socio is inactive.
     */
    public function getEsInactivoAttribute()
    {
        return $this->situacion == 0;
    }

    /**
     * Get numero de socio with leading zeros.
     */
    public function getSocioFormateadoAttribute()
    {
        return str_pad($this->socio, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get orden with leading zeros.
     */
    public function getOrdenFormateadoAttribute()
    {
        return $this->orden ? str_pad($this->orden, 4, '0', STR_PAD_LEFT) : 'N/A';
    }

    /**
     * Scope a query to only active socios.
     */
    public function scopeActivos($query)
    {
        return $query->where('situacion', 1);
    }

    /**
     * Scope a query to only inactive socios.
     */
    public function scopeInactivos($query)
    {
        return $query->where('situacion', 0);
    }

    /**
     * Scope a query to filter by socio number.
     */
    public function scopeBySocio($query, $socio)
    {
        return $query->where('socio', $socio);
    }

    /**
     * Scope a query to filter by cedula.
     */
    public function scopeByCedula($query, $cedula)
    {
        return $query->where('cedula', 'like', "%{$cedula}%");
    }

    /**
     * Scope a query to filter by mesa.
     */
    public function scopeByMesa($query, $mesa)
    {
        return $query->where('mesa', $mesa);
    }

    /**
     * Scope a query to filter by departamento.
     */
    public function scopeByDepto($query, $depto)
    {
        return $query->where('depto', 'like', "%{$depto}%");
    }

    /**
     * Scope a query to filter by ciudad.
     */
    public function scopeByCiudad($query, $ciudad)
    {
        return $query->where('ciudad', 'like', "%{$ciudad}%");
    }

    /**
     * Scope a query to filter by orden range.
     */
    public function scopeOrdenEntre($query, $desde, $hasta)
    {
        return $query->whereBetween('orden', [$desde, $hasta]);
    }

    /**
     * Get los datos básicos para tarjeta de socio.
     */
    public function getTarjetaSocioAttribute()
    {
        return [
            'socio' => $this->socio_formateado,
            'cedula' => $this->cedula,
            'nombre' => $this->nombre ?? 'N/A',
            'situacion' => $this->situacion_text,
            'mesa' => $this->mesa_formateada,
            'orden' => $this->orden_formateado,
            'ubicacion' => $this->ubicacion_completa
        ];
    }
}