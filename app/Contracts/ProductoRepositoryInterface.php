<?php

namespace App\Contracts;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;

interface ProductoRepositoryInterface
{
    public function create(array $data): Producto;
    public function update(string $sku, array $data): Producto;
    public function delete(string $sku): bool;
    public function findBySku(string $sku): ?Producto;
    public function getAll(): Collection;
    public function getBySkus(array $skus): Collection;
}