<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Micro Intranet')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Navigation -->
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-building"></i> Micro Intranet
            </a>
            
            <button class="navbar-toggler" type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" 
                    aria-expanded="false" 
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" 
                           href="{{ route('home') }}" 
                           aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cotizaciones.*') ? 'active' : '' }}" 
                           href="{{ route('cotizaciones.index') }}" 
                           aria-current="{{ request()->routeIs('cotizaciones.*') ? 'page' : 'false' }}">
                            <i class="fas fa-file-invoice-dollar"></i> Cotizaciones
                        </a>
                    </li>
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                           href="{{ route('users.index') }}" 
                           aria-current="{{ request()->routeIs('users.*') ? 'page' : 'false' }}">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                    </li>
                    @endif
                </ul>
                
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <!-- Notificaciones - Solo para administradores -->
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative" href="#" id="notificationsDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" 
                                  style="display: {{ auth()->user()->unreadNotifications()->count() > 0 ? 'inline' : 'none' }};">
                                {{ auth()->user()->unreadNotifications()->count() }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationsDropdown" style="min-width: 350px;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-bell me-1"></i> Notificaciones</span>
                                @if(auth()->user()->unreadNotifications()->count() > 0)
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                                        Marcar todas
                                    </button>
                                @endif
                            </div>
                            <div id="notifications-list" style="max-height: 400px; overflow-y: auto;">
                                <!-- Las notificaciones se cargarán aquí dinámicamente -->
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-item text-center">
                                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-list"></i> Ver Todas las Notificaciones
                                </a>
                            </div>
                        </div>
                    </li>
                    @endif
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <i class="fas fa-user"></i> {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                            @if(auth()->user()->isAdmin())
                                <span class="badge bg-warning text-dark ms-1">Admin</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('users.show', auth()->id()) }}">
                                    <i class="fas fa-user-circle me-2"></i> Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('users.edit', auth()->id()) }}">
                                    <i class="fas fa-edit me-2"></i> Editar Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Alerts -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    @auth
    @if(auth()->user()->isAdmin())
    <!-- Notificaciones JavaScript - Solo para administradores -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar notificaciones al hacer clic en el dropdown
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            
            if (notificationsDropdown) {
                notificationsDropdown.addEventListener('click', function() {
                    loadRecentNotifications();
                });
                
                // Actualizar contador cada 30 segundos
                setInterval(updateNotificationCount, 30000);
            }
        });
        
        function loadRecentNotifications() {
            fetch('/api/notifications/recent')
                .then(response => response.json())
                .then(data => {
                    const notificationsList = document.getElementById('notifications-list');
                    
                    if (data.notifications.length === 0) {
                        notificationsList.innerHTML = `
                            <div class="dropdown-item text-center text-muted py-3">
                                <i class="fas fa-bell-slash"></i><br>
                                No tienes notificaciones nuevas
                            </div>
                        `;
                    } else {
                        let html = '';
                        data.notifications.forEach(notification => {
                            const typeIcon = notification.type === 'minor_user_created' ? 
                                '<i class="fas fa-exclamation-triangle text-warning"></i>' : 
                                '<i class="fas fa-bell text-info"></i>';
                            
                            html += `
                                <div class="dropdown-item notification-item px-3 py-2 border-bottom" data-id="${notification.id}">
                                    <div class="d-flex align-items-start">
                                        <div class="me-2 mt-1">${typeIcon}</div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">${notification.title}</h6>
                                            <p class="mb-1 small text-muted">${notification.message}</p>
                                            <small class="text-muted">${notification.created_at}</small>
                                            <div class="mt-1">
                                                <button class="btn btn-sm btn-outline-success me-1" 
                                                        onclick="markAsRead('${notification.id}')">
                                                    <i class="fas fa-check"></i> Marcar leída
                                                </button>
                                                ${notification.action_url !== '#' ? 
                                                    `<a href="${notification.action_url}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        notificationsList.innerHTML = html;
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }
        
        function updateNotificationCount() {
            fetch('/api/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.count);
                })
                .catch(error => console.error('Error updating notification count:', error));
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
        
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar contador
                    updateNotificationBadge(data.unread_count);
                    
                    // Remover notificación de la lista
                    const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.remove();
                    }
                    
                    // Recargar lista si está vacía
                    setTimeout(loadRecentNotifications, 500);
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        function markAllAsRead() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(0);
                    loadRecentNotifications();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    @endif
    @endauth
    
    <!-- Scripts adicionales de las páginas -->
    @stack('scripts')
</body>
</html>