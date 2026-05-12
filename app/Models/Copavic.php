<?php
// app/Models/Copavic.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Copavic extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'copavic';

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
        'nombre',
        'cedula',
        'aporte',
        'solidaridad',
        'prestamo',
        'tarjeta',
        'construccion',
        'habilitado',
        'direccion',
        'telefono'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'socio' => 'integer',
        'aporte' => 'decimal:2',
        'solidaridad' => 'decimal:2',
        'prestamo' => 'decimal:2',
        'tarjeta' => 'decimal:2',
        'construccion' => 'decimal:2',
        'habilitado' => 'boolean',
    ];

    /**
     * Get the socio's full name with cedula.
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' (' . $this->cedula . ')';
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
        }
        
        return $this->telefono;
    }

    /**
     * Get total aportes (aporte + solidaridad).
     */
    public function getTotalAportesAttribute()
    {
        return ($this->aporte ?? 0) + ($this->solidaridad ?? 0);
    }

    /**
     * Get total deudas (prestamo + tarjeta + construccion).
     */
    public function getTotalDeudasAttribute()
    {
        return ($this->prestamo ?? 0) + ($this->tarjeta ?? 0) + ($this->construccion ?? 0);
    }

    /**
     * Get saldo disponible (aportes - deudas).
     */
    public function getSaldoDisponibleAttribute()
    {
        return $this->total_aportes - $this->total_deudas;
    }

    /**
     * Get habilitado text.
     */
    public function getHabilitadoTextAttribute()
    {
        return $this->habilitado ? 'Habilitado' : 'Inhabilitado';
    }

    /**
     * Get habilitado badge.
     */
    public function getHabilitadoBadgeAttribute()
    {
        if ($this->habilitado) {
            return '<span class="badge bg-success">Habilitado</span>';
        }
        return '<span class="badge bg-danger">Inhabilitado</span>';
    }

    /**
     * Get estado financiero badge.
     */
    public function getEstadoFinancieroBadgeAttribute()
    {
        $saldo = $this->saldo_disponible;
        
        if ($saldo > 0) {
            return '<span class="badge bg-success">Saldo a favor: Gs. ' . number_format($saldo, 0, ',', '.') . '</span>';
        } elseif ($saldo < 0) {
            return '<span class="badge bg-danger">Deuda: Gs. ' . number_format(abs($saldo), 0, ',', '.') . '</span>';
        } else {
            return '<span class="badge bg-warning">Saldo cero</span>';
        }
    }

    /**
     * Check if socio has debts.
     */
    public function getTieneDeudasAttribute()
    {
        return $this->total_deudas > 0;
    }

    /**
     * Check if socio is eligible for new loan.
     */
    public function getElegiblePrestamoAttribute()
    {
        return $this->habilitado && $this->saldo_disponible > 0;
    }

    /**
     * Scope a query to only habilitados socios.
     */
    public function scopeHabilitados($query)
    {
        return $query->where('habilitado', 1);
    }

    /**
     * Scope a query to only inhabilitados socios.
     */
    public function scopeInhabilitados($query)
    {
        return $query->where('habilitado', 0);
    }

    /**
     * Scope a query to filter by socio number.
     */
    public function scopeBySocio($query, $socio)
    {
        return $query->where('socio', $socio);
    }

    /**
     * Scope a query to filter by name.
     */
    public function scopeByNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', "%{$nombre}%");
    }

    /**
     * Scope a query to filter by cedula.
     */
    public function scopeByCedula($query, $cedula)
    {
        return $query->where('cedula', 'like', "%{$cedula}%");
    }

    /**
     * Scope a query to filter socios with debts.
     */
    public function scopeConDeudas($query)
    {
        return $query->whereRaw('(prestamo > 0 OR tarjeta > 0 OR construccion > 0)');
    }

    /**
     * Scope a query to filter socios without debts.
     */
    public function scopeSinDeudas($query)
    {
        return $query->where('prestamo', 0)
            ->where('tarjeta', 0)
            ->where('construccion', 0);
    }

    /**
     * Scope a query to filter by minimum balance.
     */
    public function scopeSaldoMinimo($query, $monto)
    {
        return $query->havingRaw('(aporte + solidaridad - prestamo - tarjeta - construccion) >= ?', [$monto]);
    }

    /**
     * Get formatted currency values.
     */
    public function getAporteFormateadoAttribute()
    {
        return 'Gs. ' . number_format($this->aporte ?? 0, 0, ',', '.');
    }

    public function getSolidaridadFormateadaAttribute()
    {
        return 'Gs. ' . number_format($this->solidaridad ?? 0, 0, ',', '.');
    }

    public function getPrestamoFormateadoAttribute()
    {
        return 'Gs. ' . number_format($this->prestamo ?? 0, 0, ',', '.');
    }

    public function getTarjetaFormateadaAttribute()
    {
        return 'Gs. ' . number_format($this->tarjeta ?? 0, 0, ',', '.');
    }

    public function getConstruccionFormateadaAttribute()
    {
        return 'Gs. ' . number_format($this->construccion ?? 0, 0, ',', '.');
    }

    public function getTotalAportesFormateadoAttribute()
    {
        return 'Gs. ' . number_format($this->total_aportes, 0, ',', '.');
    }

    public function getTotalDeudasFormateadoAttribute()
    {
        return 'Gs. ' . number_format($this->total_deudas, 0, ',', '.');
    }

    public function getSaldoDisponibleFormateadoAttribute()
    {
        return 'Gs. ' . number_format($this->saldo_disponible, 0, ',', '.');
    }

    /**
     * Get financial summary as array.
     */
    public function getResumenFinancieroAttribute()
    {
        return [
            'aportes' => [
                'aporte' => $this->aporte ?? 0,
                'solidaridad' => $this->solidaridad ?? 0,
                'total' => $this->total_aportes
            ],
            'deudas' => [
                'prestamo' => $this->prestamo ?? 0,
                'tarjeta' => $this->tarjeta ?? 0,
                'construccion' => $this->construccion ?? 0,
                'total' => $this->total_deudas
            ],
            'saldo' => $this->saldo_disponible,
            'habilitado' => $this->habilitado
        ];
    }
}