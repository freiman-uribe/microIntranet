<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionD extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cotizacion_d';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'cotizacion_id',
        'producto_sku',
        'cantidad',
        'precio_unitario',
        'fecha_registro',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_unitario' => 'decimal:2',
            'fecha_registro' => 'datetime',
        ];
    }

    /**
     * Get the cotización that owns the detail.
     */
    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(CotizacionC::class, 'cotizacion_id');
    }

    /**
     * Get the producto that belongs to the detail.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_sku', 'sku');
    }

    /**
     * Get the subtotal for this detail line
     */
    public function getSubtotalAttribute(): float
    {
        return $this->cantidad * $this->precio_unitario;
    }
}
