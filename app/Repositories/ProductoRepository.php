<?php

namespace App\Repositories;

use App\Contracts\ProductoRepositoryInterface;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;

class ProductoRepository implements ProductoRepositoryInterface
{
    public function create(array $data): Producto
    {
        return Producto::create($data);
    }

    public function update(string $sku, array $data): Producto
    {
        $producto = Producto::findOrFail($sku);
        $producto->update($data);
        return $producto->fresh();
    }

    public function delete(string $sku): bool
    {
        $producto = Producto::findOrFail($sku);
        return $producto->delete();
    }

    public function findBySku(string $sku): ?Producto
    {
        return Producto::find($sku);
    }

    public function getAll(): Collection
    {
        return Producto::all();
    }

    public function getBySkus(array $skus): Collection
    {
        return Producto::whereIn('sku', $skus)->get();
    }
}