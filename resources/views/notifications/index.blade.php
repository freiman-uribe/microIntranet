@extends('layouts.app')

@section('title', 'Notificaciones - Micro Intranet')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-bell"></i> Notificaciones del Sistema</h2>
                <p class="text-muted">
                    <i class="fas fa-shield-alt text-warning"></i> 
                    Panel de administrador - Gestiona las notificaciones de usuarios menores de edad
                </p>
            </div>
            @if(auth()->user()->unreadNotifications()->count() > 0)
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary" onclick="markAllAsRead()">
                    <i class="fas fa-check-double"></i> Marcar Todas como Leídas
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Notificaciones ({{ $notifications->total() }} total)</h5>
                <div>
                    <span class="badge bg-danger">{{ auth()->user()->unreadNotifications()->count() }} sin leer</span>
                    <span class="badge bg-info">Página {{ $notifications->currentPage() }} de {{ $notifications->lastPage() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        <div class="list-group-item {{ is_null($notification->read_at) ? 'list-group-item-warning' : '' }} notification-item" 
                             data-id="{{ $notification->id }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @if(isset($notification->data['type']))
                                            @switch($notification->data['type'])
                                                @case('minor_user_created')
                                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-bell text-info me-2"></i>
                                            @endswitch
                                        @else
                                            <i class="fas fa-bell text-info me-2"></i>
                                        @endif
                                        
                                        <h6 class="mb-0">{{ $notification->data['title'] ?? 'Notificación' }}</h6>
                                        
                                        @if(is_null($notification->read_at))
                                            <span class="badge bg-danger ms-2">Nueva</span>
                                        @endif
                                    </div>
                                    
                                    <p class="mb-2 text-muted">{{ $notification->data['message'] ?? 'Sin descripción' }}</p>
                                    
                                    @if(isset($notification->data['user_data']))
                                        <div class="alert alert-info mb-2">
                                            <h6><i class="fas fa-user"></i> Detalles del Usuario:</h6>
                                            <ul class="mb-0">
                                                <li><strong>Nombre:</strong> {{ $notification->data['user_data']['nombre'] }} {{ $notification->data['user_data']['apellido'] }}</li>
                                                <li><strong>Email:</strong> {{ $notification->data['user_data']['email'] }}</li>
                                                <li><strong>Edad:</strong> {{ $notification->data['user_data']['edad'] }} años</li>
                                                <li><strong>Registrado:</strong> {{ \Carbon\Carbon::parse($notification->data['user_data']['created_at'])->format('d/m/Y H:i:s') }}</li>
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex gap-2 mt-2">
                                        @if(isset($notification->data['action_url']))
                                            <button type="button" class="btn btn-sm btn-primary"
                                                    onclick="viewDetails('{{ $notification->data['action_url'] }}', '{{ $notification->id }}')">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </button>
                                        @endif
                                        
                                        @if(is_null($notification->read_at))
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="markAsRead('{{ $notification->id }}')">
                                                <i class="fas fa-check"></i> Marcar como Leída
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                    <br>
                                    @if(!is_null($notification->read_at))
                                        <small class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            Leída {{ $notification->read_at->diffForHumans() }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No tienes notificaciones</p>
                    </div>
                @endif
            </div>
            
            @if($notifications->hasPages())
            <div class="card-footer py-3">
                {{ $notifications->links('pagination.custom') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewDetails(url, notificationId) {
    console.log('Ver detalles - URL:', url, 'Notification ID:', notificationId);
    
    // Marcar como leída primero
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Redirigir después de marcar como leída
            window.location.href = url;
        } else {
            showToast('Error al marcar la notificación', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Redirigir aunque haya error al marcar como leída
        window.location.href = url;
    });
}

function markAsRead(notificationId) {
    console.log('Intentando marcar notificación como leída:', notificationId);
    
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Respuesta del servidor:', response.status);
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            showToast('Notificación marcada como leída', 'success');
            // Recargar la página después de mostrar el toast
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Error al marcar la notificación', 'error');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        showToast('Error al marcar la notificación: ' + error.message, 'error');
    });
}

function markAllAsRead() {
    console.log('Intentando marcar todas las notificaciones como leídas');
    
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Respuesta del servidor:', response.status);
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            showToast('Todas las notificaciones marcadas como leídas', 'success');
            // Recargar la página después de mostrar el toast
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Error al marcar todas las notificaciones', 'error');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        showToast('Error al marcar todas las notificaciones: ' + error.message, 'error');
    });
}

function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    }
}

function showToast(message, type = 'info') {
    console.log('Mostrando toast:', message, type);
    
    // Crear toast dinámicamente
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Remover automáticamente después de 3 segundos
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}

// Función para verificar que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Sistema de notificaciones iniciado');
    
    // Verificar que existe el CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token no encontrado');
        showToast('Error: Token CSRF no encontrado', 'error');
    } else {
        console.log('CSRF token encontrado:', csrfToken.getAttribute('content').substring(0, 10) + '...');
    }
});
</script>
@endpush