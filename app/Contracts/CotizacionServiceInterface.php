<?php

namespace App\Contracts;

use App\Models\CotizacionC;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CotizacionServiceInterface
{
    public function createCotizacion(int $userId, array $productos): CotizacionC;
    public function getCotizacionById(int $id): ?CotizacionC;
    public function getAllCotizaciones(): \Illuminate\Database\Eloquent\Collection;
    public function getPaginatedCotizaciones(int $perPage = 5): LengthAwarePaginator;
    public function getCotizacionesByUser(int $userId): \Illuminate\Database\Eloquent\Collection;
    public function getFilteredCotizaciones(
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?float $minAmount = null, 
        int $perPage = 5
    ): LengthAwarePaginator;
    public function getFilteredCotizacionesForUser(
        int $userId,
        bool $isAdmin,
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?float $minAmount = null, 
        int $perPage = 5
    ): LengthAwarePaginator;
    public function recalcularTotal(int $cotizacionId): void;
    public function exportarResumen(): array;
    public function exportarProductosCotizados(): array;
    public function exportarResumenForUser(int $userId, bool $isAdmin): array;
    public function exportarProductosCotizadosForUser(int $userId, bool $isAdmin): array;
}