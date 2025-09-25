<?php

namespace App\Repositories;

use App\Contracts\CotizacionRepositoryInterface;
use App\Models\CotizacionC;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CotizacionRepository implements CotizacionRepositoryInterface
{
    public function create(array $data): CotizacionC
    {
        return CotizacionC::create($data);
    }

    public function update(int $id, array $data): CotizacionC
    {
        $cotizacion = CotizacionC::findOrFail($id);
        $cotizacion->update($data);
        return $cotizacion->fresh();
    }

    public function delete(int $id): bool
    {
        $cotizacion = CotizacionC::findOrFail($id);
        return $cotizacion->delete();
    }

    public function findById(int $id): ?CotizacionC
    {
        return CotizacionC::with(['usuario', 'detalles.producto'])->find($id);
    }

    public function getAll(): Collection
    {
        return CotizacionC::with(['usuario', 'detalles.producto'])->get();
    }

    public function getPaginated(int $perPage = 5): LengthAwarePaginator
    {
        return CotizacionC::with(['usuario', 'detalles.producto'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByUser(int $userId): Collection
    {
        return CotizacionC::with(['detalles.producto'])
            ->where('usuario_id', $userId)
            ->get();
    }

    public function getByDateRange(?string $startDate, ?string $endDate): Collection
    {
        return CotizacionC::with(['usuario', 'detalles.producto'])
            ->byDateRange($startDate, $endDate)
            ->get();
    }

    public function getByMinAmount(?float $minAmount): Collection
    {
        return CotizacionC::with(['usuario', 'detalles.producto'])
            ->byMinAmount($minAmount)
            ->get();
    }

    public function getFilteredPaginated(
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?float $minAmount = null, 
        int $perPage = 5
    ): LengthAwarePaginator {
        return CotizacionC::with(['usuario', 'detalles.producto'])
            ->byDateRange($startDate, $endDate)
            ->byMinAmount($minAmount)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getFilteredPaginatedByUser(
        int $userId,
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?float $minAmount = null, 
        int $perPage = 5
    ): LengthAwarePaginator {
        return CotizacionC::with(['usuario', 'detalles.producto'])
            ->where('usuario_id', $userId)
            ->byDateRange($startDate, $endDate)
            ->byMinAmount($minAmount)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}