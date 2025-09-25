@extends('layouts.app')

@section('title', 'Restablecer Contraseña - Micro Intranet')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-lock"></i> Restablecer Contraseña
                </h4>
                <p class="mb-0 mt-2">Ingresa tu nueva contraseña</p>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', request('email')) }}" 
                               required 
                               autofocus
                               placeholder="tu-email@ejemplo.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Nueva Contraseña
                        </label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required
                               minlength="6"
                               placeholder="Mínimo 6 caracteres">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Mínimo 6 caracteres. Usa una combinación segura.
                        </small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock"></i> Confirmar Nueva Contraseña
                        </label>
                        <input type="password" 
                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               minlength="6"
                               placeholder="Repite la contraseña">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check"></i> Restablecer Contraseña
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
                        <strong>Seguridad:</strong> Tu contraseña será encriptada y almacenada de forma segura
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        if (confirmPassword.value && password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Las contraseñas no coinciden');
            confirmPassword.classList.add('is-invalid');
            
            // Agregar mensaje de error personalizado
            let errorDiv = confirmPassword.parentNode.querySelector('.invalid-feedback');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                confirmPassword.parentNode.appendChild(errorDiv);
            }
            errorDiv.textContent = 'Las contraseñas no coinciden';
        } else {
            confirmPassword.setCustomValidity('');
            confirmPassword.classList.remove('is-invalid');
            
            // Remover mensaje de error
            const errorDiv = confirmPassword.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    }
    
    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    
    // Indicador de fortaleza de contraseña
    password.addEventListener('input', function() {
        const strength = getPasswordStrength(this.value);
        showPasswordStrength(strength);
    });
    
    function getPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 6) strength += 1;
        if (password.length >= 8) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        return strength;
    }
    
    function showPasswordStrength(strength) {
        const strengthText = ['Muy débil', 'Débil', 'Regular', 'Buena', 'Fuerte', 'Muy fuerte'];
        const strengthColors = ['danger', 'danger', 'warning', 'warning', 'success', 'success'];
        
        let strengthDiv = document.getElementById('password-strength');
        if (!strengthDiv) {
            strengthDiv = document.createElement('div');
            strengthDiv.id = 'password-strength';
            strengthDiv.className = 'small mt-1';
            password.parentNode.appendChild(strengthDiv);
        }
        
        if (password.value.length > 0) {
            strengthDiv.innerHTML = `
                <span class="text-${strengthColors[strength]}">
                    <i class="fas fa-shield-alt"></i> 
                    Fortaleza: ${strengthText[strength]}
                </span>
            `;
        } else {
            strengthDiv.innerHTML = '';
        }
    }
});
</script>
@endpush

@push('styles')
<style>
    .card-header.bg-success {
        background-color: #28a745 !important;
    }
    
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    .password-strength {
        transition: all 0.3s ease;
    }
</style>
@endpush