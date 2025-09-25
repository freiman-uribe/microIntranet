@extends('layouts.app')

@section('title', 'Ver Usuario - Micro Intranet')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-user-circle"></i> Perfil de Usuario</h2>
                <p class="text-muted">Detalles de {{ $user->nombre }} {{ $user->apellido }}</p>
            </div>
            <div class="btn-group">
                @if(auth()->user()->isAdmin() || auth()->id() === $user->id)
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                @endif
                @if(auth()->user()->isAdmin())
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                @else
                <a href="{{ route('home') }}" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Inicio
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Tarjeta de Perfil -->
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center" 
                     style="width: 100px; height: 100px; border-radius: 50%; font-size: 2rem; font-weight: bold;">
                    {{ strtoupper(substr($user->nombre, 0, 1) . substr($user->apellido, 0, 1)) }}
                </div>
                
                <h4 class="card-title">{{ $user->nombre }} {{ $user->apellido }}</h4>
                <p class="card-text text-muted">{{ $user->email }}</p>
                
                @if($user->isAdmin())
                    <span class="badge bg-warning text-dark fs-6">
                        <i class="fas fa-crown"></i> Administrador
                    </span>
                @else
                    <span class="badge bg-secondary fs-6">
                        <i class="fas fa-user"></i> Usuario
                    </span>
                @endif
                
                @if($user->edad < 18)
                    <span class="badge bg-info fs-6 ms-1">
                        <i class="fas fa-child"></i> Menor de Edad
                    </span>
                @endif
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar"></i> Estadísticas
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $user->cotizaciones->count() }}</h4>
                            <small class="text-muted">Cotizaciones</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">${{ number_format($user->cotizaciones->sum('total_bruto'), 2) }}</h4>
                        <small class="text-muted">Total Cotizado</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Información Personal -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Información Personal
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-user text-muted"></i> Nombre:</strong>
                        <p class="mb-0">{{ $user->nombre }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-user text-muted"></i> Apellido:</strong>
                        <p class="mb-0">{{ $user->apellido }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-envelope text-muted"></i> Email:</strong>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-calendar text-muted"></i> Edad:</strong>
                        <p class="mb-0">{{ $user->edad }} años</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-shield-alt text-muted"></i> Tipo de Usuario:</strong>
                        <p class="mb-0">
                            @if($user->isAdmin())
                                <span class="badge bg-warning text-dark">Administrador</span>
                            @else
                                <span class="badge bg-secondary">Usuario</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-calendar-plus text-muted"></i> Fecha de Registro:</strong>
                        <p class="mb-0">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                <hr>
                <div class="row">
                    <div class="col-12 mb-3">
                        <strong><i class="fas fa-key text-muted"></i> Token de Acceso:</strong>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $user->token }}" readonly id="userToken">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToken()">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                        </div>
                        <small class="text-muted">Token de solo lectura para acceso API</small>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Cotizaciones Recientes -->
        @if($user->cotizaciones->count() > 0)
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-file-invoice-dollar"></i> Cotizaciones Recientes
                </h6>
                <a href="{{ route('cotizaciones.index') }}" class="btn btn-sm btn-outline-primary">
                    Ver Todas
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Productos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->cotizaciones->take(5) as $cotizacion)
                            <tr>
                                <td>{{ $cotizacion->id }}</td>
                                <td>{{ $cotizacion->fecha_emision->format('d/m/Y') }}</td>
                                <td>${{ number_format($cotizacion->total_bruto, 2) }}</td>
                                <td>{{ $cotizacion->detalles->count() }}</td>
                                <td>
                                    <a href="{{ route('cotizaciones.show', $cotizacion) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToken() {
    const tokenInput = document.getElementById('userToken');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(tokenInput.value);
    
    // Mostrar feedback
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Copiado';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}
</script>
@endpush