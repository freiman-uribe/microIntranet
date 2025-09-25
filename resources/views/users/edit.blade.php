@extends('layouts.app')

@section('title', 'Editar Usuario - Micro Intranet')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-user-edit"></i> Editar Usuario</h2>
                <p class="text-muted">Modificar información de {{ $user->nombre }} {{ $user->apellido }}</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('users.show', $user) }}" class="btn btn-info">
                    <i class="fas fa-eye"></i> Ver Perfil
                </a>
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

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actualizar Información del Usuario</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-user"></i> Nombre *
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre', $user->nombre) }}" 
                                   required
                                   placeholder="Nombre del usuario">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">
                                <i class="fas fa-user"></i> Apellido *
                            </label>
                            <input type="text" 
                                   class="form-control @error('apellido') is-invalid @enderror" 
                                   id="apellido" 
                                   name="apellido" 
                                   value="{{ old('apellido', $user->apellido) }}" 
                                   required
                                   placeholder="Apellido del usuario">
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edad" class="form-label">
                                <i class="fas fa-calendar"></i> Edad *
                            </label>
                            <input type="number" 
                                   class="form-control @error('edad') is-invalid @enderror" 
                                   id="edad" 
                                   name="edad" 
                                   value="{{ old('edad', $user->edad) }}" 
                                   required
                                   min="1"
                                   max="120"
                                   placeholder="Edad del usuario">
                            @error('edad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email *
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required
                                   placeholder="usuario@ejemplo.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Nueva Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password"
                                   placeholder="Dejar en blanco para mantener actual">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Solo completa si deseas cambiar la contraseña
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock"></i> Confirmar Nueva Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation"
                                   placeholder="Confirmar nueva contraseña">
                        </div>
                    </div>
                    
                    @if(auth()->user()->isAdmin())
                    <div class="mb-4">
                        @if(auth()->id() === $user->id)
                            <!-- El admin no puede cambiar su propio rol -->
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Restricción de seguridad:</strong> No puedes modificar tu propio rol de administrador.
                                Contacta a otro administrador si necesitas cambios en tus permisos.
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="admin_readonly" 
                                       value="1"
                                       {{ $user->admin ? 'checked' : '' }}
                                       disabled>
                                <input type="hidden" name="admin" value="{{ $user->admin ? '1' : '0' }}">
                                <label class="form-check-label text-muted" for="admin_readonly">
                                    <i class="fas fa-crown text-warning"></i> Usuario Administrador (No editable)
                                </label>
                                <div class="form-text text-muted">
                                    Tu rol actual: {{ $user->admin ? 'Administrador' : 'Usuario Normal' }}
                                </div>
                            </div>
                        @else
                            <!-- El admin puede cambiar el rol de otros usuarios -->
                            <div class="form-check">
                                <!-- Campo hidden que siempre se envía con valor 0 -->
                                <input type="hidden" name="admin" value="0">
                                <!-- Checkbox que sobrescribe con valor 1 si está marcado -->
                                <input class="form-check-input @error('admin') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="admin" 
                                       name="admin" 
                                       value="1"
                                       {{ old('admin', $user->admin) ? 'checked' : '' }}>
                                <label class="form-check-label" for="admin">
                                    <i class="fas fa-crown text-warning"></i> Usuario Administrador
                                </label>
                                <div class="form-text">
                                    Los administradores pueden gestionar usuarios y acceder a todas las funciones del sistema
                                </div>
                                @error('admin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8 offset-md-2">
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle"></i> Información sobre la Actualización:</h6>
            <ul class="mb-0">
                <li>Deja el campo de contraseña vacío si no deseas cambiarla</li>
                <li>El token de acceso se mantiene sin cambios</li>
                <li>Los campos marcados con * son obligatorios</li>
                @if(!auth()->user()->isAdmin())
                <li>Solo puedes modificar tu propio perfil</li>
                @else
                    @if(auth()->id() === $user->id)
                    <li><strong>Restricción de seguridad:</strong> No puedes modificar tu propio rol de administrador</li>
                    @else
                    <li>Como administrador, puedes modificar el rol de otros usuarios</li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminCheckbox = document.getElementById('admin');
    const form = document.querySelector('form');
    
    if (adminCheckbox && form) {
        let originalAdminStatus = adminCheckbox.checked;
        
        form.addEventListener('submit', function(e) {
            const currentAdminStatus = adminCheckbox.checked;
            
            // Si cambió el estado de admin
            if (originalAdminStatus !== currentAdminStatus) {
                const userName = '{{ $user->nombre }} {{ $user->apellido }}';
                const action = currentAdminStatus ? 'otorgar' : 'revocar';
                const actionText = currentAdminStatus ? 'tendrá acceso completo' : 'perderá acceso de administrador';
                
                const confirmed = confirm(
                    `¿Estás seguro de que deseas ${action} privilegios de administrador a ${userName}?\n\n` +
                    `Si continúas, este usuario ${actionText} al sistema.`
                );
                
                if (!confirmed) {
                    e.preventDefault();
                    adminCheckbox.checked = originalAdminStatus;
                }
            }
        });
    }
});
</script>
@endpush