<?php

namespace App\Exports;

use App\Contracts\CotizacionServiceInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CotizacionesExport implements WithMultipleSheets
{
    private CotizacionServiceInterface $cotizacionService;
    private int $userId;
    private bool $isAdmin;

    public function __construct(CotizacionServiceInterface $cotizacionService, int $userId, bool $isAdmin)
    {
        $this->cotizacionService = $cotizacionService;
        $this->userId = $userId;
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new ResumenCotizacionesSheet($this->cotizacionService, $this->userId, $this->isAdmin),
            new ProductosCotizadosSheet($this->cotizacionService, $this->userId, $this->isAdmin)
        ];
    }
}

class ResumenCotizacionesSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    private CotizacionServiceInterface $cotizacionService;
    private int $userId;
    private bool $isAdmin;

    public function __construct(CotizacionServiceInterface $cotizacionService, int $userId, bool $isAdmin)
    {
        $this->cotizacionService = $cotizacionService;
        $this->userId = $userId;
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $resumenData = $this->cotizacionService->exportarResumenForUser($this->userId, $this->isAdmin);
        
        return collect($resumenData)->map(function ($cotizacion) {
            return [
                'ID' => $cotizacion['id'],
                'Usuario' => $cotizacion['usuario_nombre'],
                'Email' => $cotizacion['usuario_email'],
                'Fecha Emisión' => $cotizacion['fecha_emision'],
                'Fecha Registro' => $cotizacion['fecha_registro'],
                'Total Bruto' => '$' . number_format($cotizacion['total_bruto'], 2),
                'Total Productos' => $cotizacion['total_productos'],
                'Detalle de Productos' => $cotizacion['detalle_productos'],
                'Estado' => $cotizacion['estado'] ?? 'Activa'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID Cotización',
            'Usuario',
            'Email',
            'Fecha Emisión',
            'Fecha Registro',
            'Total Bruto',
            'Total Productos',
            'Detalle de Productos',
            'Estado'
        ];
    }

    public function title(): string
    {
        return 'Resumen Cotizaciones';
    }
}

class ProductosCotizadosSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    private CotizacionServiceInterface $cotizacionService;
    private int $userId;
    private bool $isAdmin;

    public function __construct(CotizacionServiceInterface $cotizacionService, int $userId, bool $isAdmin)
    {
        $this->cotizacionService = $cotizacionService;
        $this->userId = $userId;
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $productosData = $this->cotizacionService->exportarProductosCotizadosForUser($this->userId, $this->isAdmin);
        
        return collect($productosData)->map(function ($producto) {
            return [
                'Producto' => $producto['nombre'],
                'Cantidad Total' => $producto['cantidad_total'],
                'Total Facturado' => '$' . number_format($producto['total_calculado'], 2),
                'Promedio por Cotización' => '$' . number_format($producto['total_calculado'] / max($producto['cantidad_total'], 1), 2)
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre del Producto',
            'Cantidad Total Cotizada',
            'Total Facturado',
            'Promedio por Cotización'
        ];
    }

    public function title(): string
    {
        return 'Productos Cotizados';
    }
}