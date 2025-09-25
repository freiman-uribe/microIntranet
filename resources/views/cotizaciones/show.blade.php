@extends('layouts.app')

@section('title', 'Cotización #' . $cotizacion->id . ' - Micro Intranet')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-file-invoice-dollar"></i> Cotización #{{ $cotizacion->id }}</h2>
                <p class="text-muted">
                    Detalles de la cotización 
                    @if($cotizacion->created_at)
                        creada el {{ $cotizacion->created_at->format('d/m/Y H:i') }}
                    @else
                        (fecha no disponible)
                    @endif
                </p>
            </div>
            <div class="btn-group">
                @if(auth()->id() === $cotizacion->usuario_id || auth()->user()->isAdmin())
                <a href="{{ route('cotizaciones.edit', $cotizacion) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                @endif
                <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Información General -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Información General
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong><i class="fas fa-hashtag text-muted"></i> Número de Cotización:</strong>
                    <p class="mb-0">#{{ $cotizacion->id }}</p>
                </div>
                <div class="mb-3">
                    <strong><i class="fas fa-calendar text-muted"></i> Fecha de Emisión:</strong>
                    <p class="mb-0">
                        @if($cotizacion->fecha_emision)
                            {{ $cotizacion->fecha_emision->format('d/m/Y') }}
                        @else
                            <span class="text-muted">No disponible</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <strong><i class="fas fa-clock text-muted"></i> Fecha de Registro:</strong>
                    <p class="mb-0">
                        @if($cotizacion->fecha_registro)
                            {{ $cotizacion->fecha_registro->format('d/m/Y H:i') }}
                        @else
                            <span class="text-muted">No disponible</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <strong><i class="fas fa-dollar-sign text-muted"></i> Total Bruto:</strong>
                    <h4 class="text-success mb-0">${{ number_format($cotizacion->total_bruto, 2) }}</h4>
                </div>
                <div class="mb-3">
                    <strong><i class="fas fa-boxes text-muted"></i> Cantidad de Productos:</strong>
                    <p class="mb-0">{{ $cotizacion->detalles->count() }} items</p>
                </div>
            </div>
        </div>

        <!-- Información del Usuario -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-user"></i> Creado por
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center" 
                         style="width: 50px; height: 50px; border-radius: 50%; font-size: 1.2rem; font-weight: bold;">
                        {{ strtoupper(substr($cotizacion->usuario->nombre, 0, 1) . substr($cotizacion->usuario->apellido, 0, 1)) }}
                    </div>
                    <div>
                        <strong>{{ $cotizacion->usuario->nombre }} {{ $cotizacion->usuario->apellido }}</strong>
                        @if($cotizacion->usuario->isAdmin())
                            <span class="badge bg-warning text-dark ms-1">Admin</span>
                        @endif
                        <br>
                        <small class="text-muted">{{ $cotizacion->usuario->email }}</small>
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Edad:</strong> {{ $cotizacion->usuario->edad }} años
                </div>
                <div class="mb-2">
                    <strong>Cotizaciones totales:</strong> {{ $cotizacion->usuario->cotizaciones->count() }}
                </div>
                <div class="mb-2">
                    <strong>Total cotizado:</strong> ${{ number_format($cotizacion->usuario->cotizaciones->sum('total_bruto'), 2) }}
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar"></i> Estadísticas
                </h6>
            </div>
            <div class="card-body">
                @php
                    $totalCantidad = $cotizacion->detalles->sum('cantidad');
                    $promedioUnitario = $cotizacion->detalles->avg('precio_unitario');
                    $productoMasCaro = $cotizacion->detalles->sortByDesc('precio_unitario')->first();
                    $productoMasBarato = $cotizacion->detalles->sortBy('precio_unitario')->first();
                @endphp
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-primary">{{ $totalCantidad }}</h5>
                            <small class="text-muted">Total Unidades</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-info">${{ number_format($promedioUnitario, 2) }}</h5>
                        <small class="text-muted">Precio Promedio</small>
                    </div>
                </div>
                
                @if($productoMasCaro && $productoMasBarato)
                <hr>
                <div class="mb-2">
                    <small class="text-muted">Más caro:</small><br>
                    <strong>{{ $productoMasCaro->producto->nombre }}</strong><br>
                    <span class="text-success">${{ number_format($productoMasCaro->precio_unitario, 2) }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Más barato:</small><br>
                    <strong>{{ $productoMasBarato->producto->nombre }}</strong><br>
                    <span class="text-primary">${{ number_format($productoMasBarato->precio_unitario, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Detalles de Productos -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-list"></i> Productos Cotizados ({{ $cotizacion->detalles->count() }} items)
                </h6>
                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
            <div class="card-body p-0">
                @if($cotizacion->detalles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Precio Unit.</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>% del Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cotizacion->detalles as $index => $detalle)
                            @php
                                $subtotal = $detalle->cantidad * $detalle->precio_unitario;
                                $porcentaje = ($subtotal / $cotizacion->total_bruto) * 100;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <code>{{ $detalle->producto_sku }}</code>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $detalle->producto->nombre }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-info">${{ number_format($detalle->precio_unitario, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $detalle->cantidad }}</span>
                                </td>
                                <td>
                                    <strong class="text-success">${{ number_format($subtotal, 2) }}</strong>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" 
                                             role="progressbar" 
                                             style="width: {{ $porcentaje }}%"
                                             title="{{ number_format($porcentaje, 1) }}%">
                                            {{ number_format($porcentaje, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="4">TOTAL GENERAL</th>
                                <th>{{ $cotizacion->detalles->sum('cantidad') }}</th>
                                <th>${{ number_format($cotizacion->total_bruto, 2) }}</th>
                                <th>100%</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Esta cotización no tiene productos</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Historial de Cambios -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-history"></i> Historial
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6>Cotización Creada</h6>
                            <p class="text-muted mb-1">
                                @if($cotizacion->created_at)
                                    {{ $cotizacion->created_at->format('d/m/Y H:i') }}
                                @else
                                    Fecha no disponible
                                @endif
                            </p>
                            <small class="text-muted">
                                Por {{ $cotizacion->usuario->nombre }} {{ $cotizacion->usuario->apellido }}
                            </small>
                        </div>
                    </div>
                    
                    @if($cotizacion->updated_at && $cotizacion->created_at && $cotizacion->updated_at != $cotizacion->created_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6>Última Modificación</h6>
                            <p class="text-muted mb-1">{{ $cotizacion->updated_at->format('d/m/Y H:i') }}</p>
                            <small class="text-muted">
                                Hace {{ $cotizacion->updated_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.timeline {
    position: relative;
    padding: 0;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 12px;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

@media print {
    .btn, .card-header .btn-group, nav, .alert {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush