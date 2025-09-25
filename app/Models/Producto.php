<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'sku';

    /**
     * The "type" of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     */
    protected $table = 'productos';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sku',
        'nombre',
        'precio_unitario',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
        ];
    }

    /**
     * Get the cotizacion details for the product.
     */
    public function cotizacionDetalles(): HasMany
    {
        return $this->hasMany(CotizacionD::class, 'producto_sku', 'sku');
    }
}
