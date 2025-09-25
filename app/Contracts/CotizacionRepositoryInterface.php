<?php

namespace App\Contracts;

use App\Models\CotizacionC;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CotizacionRepositoryInterface
{
    public function create(array $data): CotizacionC;
    public function update(int $id, array $data): CotizacionC;
    public function delete(int $id): bool;
    public function findById(int $id): ?CotizacionC;
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 5): LengthAwarePaginator;
    public function getByUser(int $userId): Collection;
    public function getByDateRange(?string $startDate, ?string $endDate): Collection;
    public function getByMinAmount(?float $minAmount): Collection;
    public function getFilteredPaginated(
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?float $minAmount = null, 
        int $perPage = 5
    ): LengthAwarePaginator;
    public function getFilteredPaginatedByUser(
        int $userId,
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?float $minAmount = null, 
        int $perPage = 5
    ): LengthAwarePaginator;
}