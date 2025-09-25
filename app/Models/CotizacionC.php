<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CotizacionC extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cotizacion_c';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'usuario_id',
        'fecha_emision',
        'total_bruto',
        'fecha_registro',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'fecha_registro' => 'datetime',
            'total_bruto' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the cotización.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Get the cotización details.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(CotizacionD::class, 'cotizacion_id');
    }

    /**
     * Calculate and update the total bruto
     */
    public function calcularTotal(): void
    {
        $total = $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });

        $this->update(['total_bruto' => $total]);
    }

    /**
     * Get the total quantity of products in the cotización
     */
    public function getTotalProductosAttribute(): int
    {
        return $this->detalles->sum('cantidad');
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('fecha_emision', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('fecha_emision', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope for filtering by minimum amount
     */
    public function scopeByMinAmount($query, $minAmount = null)
    {
        if ($minAmount) {
            $query->where('total_bruto', '>=', $minAmount);
        }

        return $query;
    }
}
