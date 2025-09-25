@extends('layouts.app')

@section('title', 'Gestión de Cotizaciones - Micro Intranet')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div class="flex-grow-1">
                <h2 class="mb-2">
                    <i class="fas fa-file-invoice-dollar"></i> 
                    <span class="d-none d-sm-inline">Gestión de Cotizaciones</span>
                    <span class="d-inline d-sm-none">Cotizaciones</span>
                </h2>
                @if(auth()->user()->isAdmin())
                    <p class="text-muted mb-0 d-none d-md-block">
                        <i class="fas fa-crown text-warning"></i> Vista de administrador - Todas las cotizaciones del sistema
                    </p>
                @else
                    <p class="text-muted mb-0 d-none d-md-block">
                        <i class="fas fa-user"></i> Mis cotizaciones personales
                    </p>
                @endif
            </div>
            <div class="btn-group flex-wrap">
                <a href="{{ route('cotizaciones.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span class="d-none d-sm-inline ms-1">Nueva Cotización</span>
                    <span class="d-inline d-sm-none ms-1">Nueva</span>
                </a>
                <button class="btn btn-outline-primary" onclick="exportCotizaciones()">
                    <i class="fas fa-download"></i>
                    <span class="d-none d-sm-inline ms-1">Exportar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-filter"></i> Filtros de Búsqueda
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('cotizaciones.index') }}">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-3">
                            <label for="start_date" class="form-label small">Fecha Desde:</label>
                            <input type="date" 
                                   class="form-control form-control-sm" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ $startDate }}"
                                   title="Fecha de inicio del rango de búsqueda">
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <label for="end_date" class="form-label small">Fecha Hasta:</label>
                            <input type="date" 
                                   class="form-control form-control-sm" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ $endDate }}"
                                   title="Debe ser mayor o igual a la fecha desde">
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <label for="min_amount" class="form-label small">Monto Mínimo:</label>
                            <input type="number" 
                                   class="form-control form-control-sm" 
                                   id="min_amount" 
                                   name="min_amount" 
                                   value="{{ $minAmount }}"
                                   step="0.01"
                                   placeholder="0.00">
                        </div>
                        <div class="col-sm-6 col-lg-3 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i>
                                    <span class="d-none d-sm-inline ms-1">Filtrar</span>
                                </button>
                                <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                    <span class="d-none d-sm-inline ms-1">Limpiar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Cotizaciones -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Lista de Cotizaciones ({{ $cotizaciones->total() }} total)</h5>
                    @if(!auth()->user()->isAdmin())
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Mostrando solo sus cotizaciones
                        </small>
                    @else
                        <small class="text-muted">
                            <i class="fas fa-crown"></i> Vista completa del sistema
                        </small>
                    @endif
                </div>
                <span class="badge bg-info">Página {{ $cotizaciones->currentPage() }} de {{ $cotizaciones->lastPage() }}</span>
            </div>
            <div class="card-body p-0">
                @if($cotizaciones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-nowrap">Número</th>
                                <th class="text-nowrap d-none d-sm-table-cell">Fecha Emisión</th>
                                <th class="text-nowrap">Usuario</th>
                                <th class="text-nowrap">Total Bruto</th>
                                <th class="text-nowrap d-none d-md-table-cell">Cant. Productos</th>
                                <th class="text-nowrap d-none d-lg-table-cell">Registrada</th>
                                <th class="text-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cotizaciones as $cotizacion)
                            <tr>
                                <td class="text-nowrap">
                                    <strong class="text-primary">#{{ $cotizacion->id }}</strong>
                                    <div class="d-block d-sm-none text-muted small">
                                        {{ $cotizacion->fecha_emision->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="d-none d-sm-table-cell text-nowrap">
                                    {{ $cotizacion->fecha_emision->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-secondary text-white me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 28px; height: 28px; border-radius: 50%; font-size: 0.7rem; font-weight: bold;">
                                            {{ strtoupper(substr($cotizacion->usuario->nombre, 0, 1) . substr($cotizacion->usuario->apellido, 0, 1)) }}
                                        </div>
                                        <div class="min-width-0">
                                            <div class="text-truncate" style="max-width: 120px;">
                                                {{ $cotizacion->usuario->nombre }} {{ $cotizacion->usuario->apellido }}
                                            </div>
                                            <small class="text-muted d-none d-md-block text-truncate" style="max-width: 120px;">
                                                {{ $cotizacion->usuario->email }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <strong class="text-success">${{ number_format($cotizacion->total_bruto, 2) }}</strong>
                                    <div class="d-block d-md-none">
                                        <span class="badge bg-primary">{{ $cotizacion->detalles->count() }}</span>
                                        <small class="text-muted">items</small>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell text-nowrap">
                                    <span class="badge bg-primary">{{ $cotizacion->detalles->count() }}</span>
                                    <small class="text-muted">items</small>
                                </td>
                                <td class="d-none d-lg-table-cell text-nowrap">
                                    <div>{{ $cotizacion->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $cotizacion->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('cotizaciones.show', $cotizacion) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                            <span class="d-none d-xl-inline ms-1">Ver</span>
                                        </a>
                                        @if(auth()->id() === $cotizacion->usuario_id || auth()->user()->isAdmin())
                                        <a href="{{ route('cotizaciones.edit', $cotizacion) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-xl-inline ms-1">Editar</span>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Eliminar"
                                                onclick="confirmDelete({{ $cotizacion->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay cotizaciones que coincidan con los filtros</p>
                    <a href="{{ route('cotizaciones.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Crear Primera Cotización
                    </a>
                </div>
                @endif
            </div>
            @if($cotizaciones->hasPages())
            <div class="card-footer py-3">
                {{ $cotizaciones->appends(request()->query())->links('pagination.custom') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar la cotización <strong id="cotizacionNumber"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-warning"></i> Esta acción eliminará todos los detalles de la cotización y no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar Cotización
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(cotizacionId) {
    document.getElementById('cotizacionNumber').textContent = '#' + cotizacionId;
    document.getElementById('deleteForm').action = `/cotizaciones/${cotizacionId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function exportCotizaciones() {
    window.open('/cotizaciones/export', '_blank');
}

// Validación de fechas
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    function validateDateRange() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end < start) {
                endDateInput.setCustomValidity('La fecha hasta debe ser mayor o igual a la fecha desde');
                endDateInput.classList.add('is-invalid');
                
                // Mostrar mensaje de error
                let errorMessage = endDateInput.parentNode.querySelector('.invalid-feedback');
                if (!errorMessage) {
                    errorMessage = document.createElement('div');
                    errorMessage.className = 'invalid-feedback';
                    endDateInput.parentNode.appendChild(errorMessage);
                }
                errorMessage.textContent = 'La fecha hasta debe ser mayor o igual a la fecha desde';
            } else {
                endDateInput.setCustomValidity('');
                endDateInput.classList.remove('is-invalid');
                
                // Remover mensaje de error si existe
                const errorMessage = endDateInput.parentNode.querySelector('.invalid-feedback');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }
        }
    }
    
    // Establecer fecha mínima para "Fecha Hasta" cuando cambia "Fecha Desde"
    startDateInput.addEventListener('change', function() {
        if (this.value) {
            endDateInput.setAttribute('min', this.value);
        } else {
            endDateInput.removeAttribute('min');
        }
        validateDateRange();
    });
    
    // Validar cuando cambia "Fecha Hasta"
    endDateInput.addEventListener('change', validateDateRange);
    
    // Validación inicial al cargar la página
    if (startDateInput.value) {
        endDateInput.setAttribute('min', startDateInput.value);
    }
    validateDateRange();
    
    // Prevenir envío del formulario si las fechas son inválidas
    const filterForm = document.querySelector('form');
    filterForm.addEventListener('submit', function(e) {
        validateDateRange();
        if (!endDateInput.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            // Mostrar alerta
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show mt-2';
            alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i> 
                Por favor corrige los errores en las fechas antes de continuar.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alert, filterForm);
            
            // Auto-remover alerta después de 5 segundos
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    /* Estilos responsive para la tabla de cotizaciones */
    .table th, .table td {
        vertical-align: middle;
    }
    
    .avatar-circle {
        flex-shrink: 0;
    }
    
    .min-width-0 {
        min-width: 0;
        flex: 1;
    }
    
    .btn-group .btn {
        white-space: nowrap;
    }
    
    /* Ajustes para pantallas pequeñas */
    @media (max-width: 576px) {
        .card-header h5 {
            font-size: 1rem;
        }
        
        .card-header .badge {
            font-size: 0.7rem;
        }
        
        .table th, .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.85rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .avatar-circle {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.65rem !important;
        }
    }
    
    /* Ajustes para pantallas medianas */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .btn-group .btn {
            margin-bottom: 2px;
        }
    }
    
    /* Mejoras visuales */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush