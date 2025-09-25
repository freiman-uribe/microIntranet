@extends('layouts.app')

@section('title', 'Inicio - Micro Intranet')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="jumbotron bg-primary text-white p-5 rounded mb-4">
            <div class="container">
                <h1 class="display-4">
                    <i class="fas fa-home"></i> ¡Bienvenido, {{ $user->nombre }} {{ $user->apellido }}!
                </h1>
                <p class="lead">
                    Sistema de Gestión de Cotizaciones - Micro Intranet
                    @if($user->isAdmin())
                        <span class="badge bg-warning text-dark fs-6">Administrador</span>
                    @endif
                </p>
                <hr class="my-4">
                <p>Desde aquí puedes gestionar tus cotizaciones y acceder a todas las funcionalidades del sistema.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tarjeta de Cotizaciones -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice-dollar"></i> Cotizaciones
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="card-text flex-grow-1">
                    Gestiona tus cotizaciones: crear nuevas, ver existentes y exportar reportes.
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('cotizaciones.index') }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i> Ver Cotizaciones
                    </a>
                    <a href="{{ route('cotizaciones.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Nueva Cotización
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($user->isAdmin())
    <!-- Tarjeta de Usuarios (Solo Admin) -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users"></i> Gestión de Usuarios
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="card-text flex-grow-1">
                    Administra los usuarios del sistema: crear, editar y eliminar cuentas.
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-success">
                        <i class="fas fa-eye"></i> Ver Usuarios
                    </a>
                    <a href="{{ route('users.create') }}" class="btn btn-outline-success">
                        <i class="fas fa-user-plus"></i> Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tarjeta de Perfil -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border-info">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-circle"></i> Mi Perfil
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="card-text flex-grow-1">
                    Administra tu información personal y configuraciones de cuenta.
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Perfil
                    </a>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información del Usuario -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Información de la Cuenta
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nombre Completo:</strong> {{ $user->nombre }} {{ $user->apellido }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Edad:</strong> {{ $user->edad }} años</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Rol:</strong> 
                            @if($user->isAdmin())
                                <span class="badge bg-warning text-dark">Administrador</span>
                            @else
                                <span class="badge bg-secondary">Usuario</span>
                            @endif
                        </p>
                        <p><strong>Miembro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                        <p><strong>Última actualización:</strong> {{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection