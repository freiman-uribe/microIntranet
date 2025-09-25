@extends('layouts.app')

@section('title', 'Recuperar Contraseña - Micro Intranet')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg">
            <div class="card-header bg-warning text-dark text-center">
                <h4 class="mb-0">
                    <i class="fas fa-key"></i> Recuperar Contraseña
                </h4>
                <p class="mb-0 mt-2">Ingresa tu email para recibir un enlace de recuperación</p>
            </div>
            <div class="card-body p-4">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               placeholder="tu-email@ejemplo.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle"></i> 
                            Te enviaremos un enlace seguro para restablecer tu contraseña.
                        </small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-paper-plane"></i> Enviar Enlace de Recuperación
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Login
                        </a>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt"></i> 
                        <strong>Proceso seguro:</strong> El enlace expira en 60 minutos por seguridad
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header.bg-warning {
        background-color: #ffc107 !important;
    }
    
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }
    
    .alert-success {
        background-color: #d1edff;
        border-color: #b8daff;
        color: #0c5460;
    }
</style>
@endpush