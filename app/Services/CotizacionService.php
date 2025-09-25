<?php

namespace App\Services;

use App\Contracts\CotizacionServiceInterface;
use App\Contracts\CotizacionRepositoryInterface;
use App\Contracts\ProductoRepositoryInterface;
use App\Models\CotizacionC;
use App\Models\CotizacionD;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CotizacionService implements CotizacionServiceInterface
{
    public function __construct(
        private CotizacionRepositoryInterface $cotizacionRepository,
        private ProductoRepositoryInterface $productoRepository
    ) {}

    public function createCotizacion(int $userId, array $productos): CotizacionC
    {
        return DB::transaction(function () use ($userId, $productos) {
            // Crear la cotización principal
            $cotizacion = $this->cotizacionRepository->create([
                'usuario_id' => $userId,
                'fecha_emision' => now()->toDateString(),
                'total_bruto' => 0,
                'fecha_registro' => now(),
            ]);

            $totalBruto = 0;

            // Agregar los detalles de productos
            foreach ($productos as $productoData) {
                $producto = $this->productoRepository->findBySku($productoData['sku']);
                
                if (!$producto) {
                    throw new \Exception("Producto con SKU {$productoData['sku']} no encontrado");
                }

                $precioUnitario = $producto->precio_unitario;
                $cantidad = $productoData['cantidad'];
                $subtotal = $precioUnitario * $cantidad;
                $totalBruto += $subtotal;

                CotizacionD::create([
                    'cotizacion_id' => $cotizacion->id,
                    'producto_sku' => $producto->sku,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'fecha_registro' => now(),
                ]);
            }

            // Actualizar el total bruto
            $cotizacion->update(['total_bruto' => $totalBruto]);

            return $cotizacion->fresh(['detalles.producto', 'usuario']);
        });
    }

    public function getCotizacionById(int $id): ?CotizacionC
    {
        return $this->cotizacionRepository->findById($id);
    }

    public function getAllCotizaciones(): Collection
    {
        return $this->cotizacionRepository->getAll();
    }

    public function getPaginatedCotizaciones(int $perPage = 5): LengthAwarePaginator
    {
        return $this->cotizacionRepository->getPaginated($perPage);
    }

    public function getCotizacionesByUser(int $userId): Collection
    {
        return $this->cotizacionRepository->getByUser($userId);
    }

    public function getFilteredCotizaciones(
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?float $minAmount = null, 
        int $perPage = 5
    ): LengthAwarePaginator {
        return $this->cotizacionRepository->getFilteredPaginated(
            $startDate, 
            $endDate, 
            $minAmount, 
            $perPage
        );
    }

    public function getFilteredCotizacionesForUser(
        int $userId,
        bool $isAdmin,
        ?string $startDate = null, 
        ?string $endDate = null, 
        ?float $minAmount = null, 
        int $perPage = 5
    ): LengthAwarePaginator {
        if ($isAdmin) {
            // Los administradores ven todas las cotizaciones
            return $this->cotizacionRepository->getFilteredPaginated(
                $startDate, 
                $endDate, 
                $minAmount, 
                $perPage
            );
        } else {
            // Los usuarios normales solo ven sus propias cotizaciones
            return $this->cotizacionRepository->getFilteredPaginatedByUser(
                $userId,
                $startDate, 
                $endDate, 
                $minAmount, 
                $perPage
            );
        }
    }

    public function recalcularTotal(int $cotizacionId): void
    {
        $cotizacion = $this->getCotizacionById($cotizacionId);
        
        if ($cotizacion) {
            $cotizacion->calcularTotal();
        }
    }

    public function exportarResumen(): array
    {
        $cotizaciones = $this->getAllCotizaciones();
        
        return $this->formatCotizacionesForExport($cotizaciones);
    }

    public function exportarResumenForUser(int $userId, bool $isAdmin): array
    {
        if ($isAdmin) {
            $cotizaciones = $this->getAllCotizaciones();
        } else {
            $cotizaciones = $this->getCotizacionesByUser($userId);
        }
        
        return $this->formatCotizacionesForExport($cotizaciones);
    }

    private function formatCotizacionesForExport($cotizaciones): array
    {
        return $cotizaciones->map(function ($cotizacion) {
            // Obtener detalles de productos de la cotización
            $productosDetalle = $cotizacion->detalles->map(function ($detalle) {
                return $detalle->producto->nombre . ' (Cant: ' . $detalle->cantidad . 
                       ', Precio: $' . number_format($detalle->precio_unitario, 2) . ')';
            })->implode(' | ');
            
            return [
                'id' => $cotizacion->id,
                'numero' => $cotizacion->id,
                'usuario_nombre' => $cotizacion->usuario->nombre ?? 'N/A',
                'usuario_email' => $cotizacion->usuario->email ?? 'N/A',
                'fecha_emision' => $cotizacion->fecha_emision->format('Y-m-d'),
                'fecha_registro' => $cotizacion->created_at->format('Y-m-d'),
                'total_bruto' => $cotizacion->total_bruto,
                'total_productos' => $cotizacion->detalles->count(),
                'detalle_productos' => $productosDetalle ?: 'Sin productos',
                'estado' => 'Activa'
            ];
        })->toArray();
    }

    public function exportarProductosCotizados(): array
    {
        $productos = DB::table('cotizacion_d as cd')
            ->join('productos as p', 'cd.producto_sku', '=', 'p.sku')
            ->select(
                'p.nombre',
                DB::raw('SUM(cd.cantidad) as cantidad_total'),
                DB::raw('SUM(cd.cantidad * cd.precio_unitario) as total_calculado')
            )
            ->groupBy('p.sku', 'p.nombre')
            ->get();

        return $productos->map(function ($producto) {
            return [
                'nombre' => $producto->nombre,
                'cantidad_total' => $producto->cantidad_total,
                'total_calculado' => $producto->total_calculado,
            ];
        })->toArray();
    }

    public function exportarProductosCotizadosForUser(int $userId, bool $isAdmin): array
    {
        $query = DB::table('cotizacion_d as cd')
            ->join('productos as p', 'cd.producto_sku', '=', 'p.sku')
            ->join('cotizacion_c as cc', 'cd.cotizacion_id', '=', 'cc.id');

        if (!$isAdmin) {
            $query->where('cc.usuario_id', $userId);
        }

        $productos = $query->select(
                'p.nombre',
                DB::raw('SUM(cd.cantidad) as cantidad_total'),
                DB::raw('SUM(cd.cantidad * cd.precio_unitario) as total_calculado')
            )
            ->groupBy('p.sku', 'p.nombre')
            ->get();

        return $productos->map(function ($producto) {
            return [
                'nombre' => $producto->nombre,
                'cantidad_total' => $producto->cantidad_total,
                'total_calculado' => $producto->total_calculado,
            ];
        })->toArray();
    }
}