<?php

namespace App\Console\Commands;

use App\Models\CotizacionC;
use Illuminate\Console\Command;

class RecalcularTotalBruto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cotizacion:recalculo_bruto {--id= : ID específico de la cotización a recalcular} {--all : Recalcular todas las cotizaciones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula los montos brutos de las cotizaciones basándose en los detalles de productos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cotizacionId = $this->option('id');
        $recalcularTodas = $this->option('all');

        if (!$cotizacionId && !$recalcularTodas) {
            $this->error('Debe especificar --id=X para una cotización específica o --all para todas las cotizaciones.');
            return Command::FAILURE;
        }

        if ($cotizacionId && $recalcularTodas) {
            $this->error('No puede especificar --id y --all al mismo tiempo.');
            return Command::FAILURE;
        }

        $this->info('Iniciando recálculo de totales brutos...');
        
        try {
            if ($cotizacionId) {
                // Recalcular cotización específica
                $this->recalcularCotizacion($cotizacionId);
            } else {
                // Recalcular todas las cotizaciones
                $this->recalcularTodasCotizaciones();
            }

            $this->info('¡Recálculo completado exitosamente!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error durante el recálculo: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Recalcular una cotización específica
     */
    private function recalcularCotizacion(int $cotizacionId): void
    {
        $cotizacion = CotizacionC::with('detalles')->find($cotizacionId);

        if (!$cotizacion) {
            throw new \Exception("Cotización con ID {$cotizacionId} no encontrada.");
        }

        $totalAnterior = $cotizacion->total_bruto;

        // Calcular nuevo total
        $nuevoTotal = $cotizacion->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });

        // Actualizar total
        $cotizacion->update(['total_bruto' => $nuevoTotal]);

        $this->line("Cotización #{$cotizacionId}:");
        $this->line("  - Total anterior: $" . number_format($totalAnterior, 2));
        $this->line("  - Nuevo total: $" . number_format($nuevoTotal, 2));
        $this->line("  - Diferencia: $" . number_format($nuevoTotal - $totalAnterior, 2));

        if ($totalAnterior != $nuevoTotal) {
            $this->warn("  ⚠️  Total actualizado");
        } else {
            $this->info("  ✓ Total ya era correcto");
        }
    }

    /**
     * Recalcular todas las cotizaciones
     */
    private function recalcularTodasCotizaciones(): void
    {
        $cotizaciones = CotizacionC::with('detalles')->get();
        $totalCotizaciones = $cotizaciones->count();

        if ($totalCotizaciones === 0) {
            $this->warn('No hay cotizaciones para recalcular.');
            return;
        }

        $this->info("Recalculando {$totalCotizaciones} cotizaciones...");
        
        $bar = $this->output->createProgressBar($totalCotizaciones);
        $bar->start();

        $cotizacionesActualizadas = 0;
        $totalDiferencia = 0;

        foreach ($cotizaciones as $cotizacion) {
            $totalAnterior = $cotizacion->total_bruto;

            // Calcular nuevo total
            $nuevoTotal = $cotizacion->detalles->sum(function ($detalle) {
                return $detalle->cantidad * $detalle->precio_unitario;
            });

            // Actualizar solo si hay diferencia
            if ($totalAnterior != $nuevoTotal) {
                $cotizacion->update(['total_bruto' => $nuevoTotal]);
                $cotizacionesActualizadas++;
                $totalDiferencia += abs($nuevoTotal - $totalAnterior);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Mostrar resumen
        $this->info("Resumen del recálculo:");
        $this->line("  - Total de cotizaciones procesadas: {$totalCotizaciones}");
        $this->line("  - Cotizaciones actualizadas: {$cotizacionesActualizadas}");
        $this->line("  - Cotizaciones sin cambios: " . ($totalCotizaciones - $cotizacionesActualizadas));
        
        if ($totalDiferencia > 0) {
            $this->line("  - Diferencia total acumulada: $" . number_format($totalDiferencia, 2));
        }

        if ($cotizacionesActualizadas > 0) {
            $this->warn("⚠️  {$cotizacionesActualizadas} cotizaciones fueron actualizadas");
        } else {
            $this->info("✓ Todos los totales ya eran correctos");
        }
    }
}
