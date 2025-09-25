<?php

namespace App\Http\Controllers;

use App\Contracts\CotizacionServiceInterface;
use App\Contracts\ProductoRepositoryInterface;
use App\Exports\CotizacionesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CotizacionRequest;
use App\Models\CotizacionC;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionController extends Controller
{
    public function __construct(
        private CotizacionServiceInterface $cotizacionService,
        private ProductoRepositoryInterface $productoRepository
    ) {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Validar fechas si ambas están presentes
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'min_amount' => 'nullable|numeric|min:0'
        ], [
            'end_date.after_or_equal' => 'La fecha hasta debe ser mayor o igual a la fecha desde.',
            'min_amount.min' => 'El monto mínimo debe ser mayor o igual a 0.',
            'min_amount.numeric' => 'El monto mínimo debe ser un número válido.'
        ]);
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $minAmount = $request->get('min_amount');

        $cotizaciones = $this->cotizacionService->getFilteredCotizacionesForUser(
            auth()->id(),
            auth()->user()->isAdmin(),
            $startDate, 
            $endDate, 
            $minAmount, 
            5
        );

        return view('cotizaciones.index', compact('cotizaciones', 'startDate', 'endDate', 'minAmount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $productos = $this->productoRepository->getAll();
        
        return view('cotizaciones.create', compact('productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CotizacionRequest $request): RedirectResponse
    {
        try {
            $cotizacion = $this->cotizacionService->createCotizacion(
                auth()->id(),
                $request->validated()['productos']
            );
            
            return redirect()->route('cotizaciones.index')
                ->with('success', 'Cotización creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear la cotización: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CotizacionC $cotizacion): View
    {
        // Solo el creador o un administrador pueden ver la cotización
        if (auth()->id() !== $cotizacion->usuario_id && !auth()->user()->isAdmin()) {
            abort(403, 'No autorizado para ver esta cotización.');
        }

        // Cargar relaciones necesarias
        $cotizacion->load(['usuario', 'detalles.producto']);
        
        return view('cotizaciones.show', compact('cotizacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CotizacionC $cotizacion): View
    {
        // Solo el creador o un administrador pueden editar
        if (auth()->id() !== $cotizacion->usuario_id && !auth()->user()->isAdmin()) {
            abort(403, 'No autorizado para editar esta cotización.');
        }

        // Cargar relaciones necesarias
        $cotizacion->load(['detalles.producto', 'usuario']);
        $productos = $this->productoRepository->getAll();
        
        return view('cotizaciones.edit', compact('cotizacion', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CotizacionC $cotizacion): RedirectResponse
    {
        
        // Solo el creador puede editar la cotización
        if (auth()->id() !== $cotizacion->usuario_id && !auth()->user()->isAdmin()) {
            abort(403, 'No autorizado para editar esta cotización.');
        }

        try {
            // Para simplificar, eliminamos los detalles existentes y creamos nuevos
            $cotizacion->detalles()->delete();
            
            // Actualizar la fecha de emisión si se proporcionó
            if ($request->has('fecha_emision') && $request->input('fecha_emision')) {
                $cotizacion->update(['fecha_emision' => $request->input('fecha_emision')]);
            }
            
            // Obtener productos del request
            $productos = $request->input('productos', []);
            
            if (empty($productos)) {
                throw new \Exception('No se encontraron productos en el request');
            }
            
            // Crear nuevos detalles
            $totalBruto = 0;
            foreach ($productos as $index => $productoData) {
                // Validar datos básicos
                if (!isset($productoData['sku']) || empty($productoData['sku'])) {
                    continue;
                }
                
                if (!isset($productoData['cantidad']) || $productoData['cantidad'] < 1) {
                    continue;
                }
                
                $producto = $this->productoRepository->findBySku($productoData['sku']);
                
                if (!$producto) {
                    throw new \Exception("Producto con SKU {$productoData['sku']} no encontrado");
                }

                $precioUnitario = $producto->precio_unitario;
                $cantidad = (int) $productoData['cantidad'];
                $subtotal = $precioUnitario * $cantidad;
                $totalBruto += $subtotal;

                $cotizacion->detalles()->create([
                    'producto_sku' => $producto->sku,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'fecha_registro' => now(),
                ]);
            }
            
            // Actualizar el total bruto
            $cotizacion->update(['total_bruto' => $totalBruto]);
            
            return redirect()->route('cotizaciones.index')
                ->with('success', 'Cotización actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar la cotización: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CotizacionC $cotizacion): RedirectResponse
    {
        // Solo el creador o admin pueden eliminar la cotización
        if (auth()->id() !== $cotizacion->usuario_id && !auth()->user()->isAdmin()) {
            abort(403, 'No autorizado para eliminar esta cotización.');
        }

        try {
            $cotizacion->delete();
            
            return redirect()->route('cotizaciones.index')
                ->with('success', 'Cotización eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar la cotización: ' . $e->getMessage()]);
        }
    }

    /**
     * Export cotizaciones to Excel
     */
    public function export(): Response|JsonResponse|BinaryFileResponse
    {
        try {
            $fileName = 'Reporte_Cotizaciones_' . date('Y_m_d_H_i_s') . '.xlsx';
            
            return Excel::download(new CotizacionesExport($this->cotizacionService, auth()->id(), auth()->user()->isAdmin()), $fileName);
        } catch (\Exception $e) {
            // Fallback a JSON si falla Excel
            $resumen = $this->cotizacionService->exportarResumenForUser(auth()->id(), auth()->user()->isAdmin());
            $productosCotizados = $this->cotizacionService->exportarProductosCotizadosForUser(auth()->id(), auth()->user()->isAdmin());

            $data = [
                'resumen_cotizaciones' => $resumen,
                'productos_cotizados' => $productosCotizados,
                'error' => 'Excel export failed: ' . $e->getMessage()
            ];

            return response()->json($data);
        }
    }
}
