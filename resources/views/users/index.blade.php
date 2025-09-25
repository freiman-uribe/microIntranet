@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Micro Intranet')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
                <p class="text-muted">Administra los usuarios del sistema</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Lista de Usuarios ({{ $users->total() }} total)</h5>
                    <small class="text-muted">
                        <i class="fas fa-crown"></i> Vista completa del sistema
                    </small>
                </div>
                <span class="badge bg-info">Página {{ $users->currentPage() }} de {{ $users->lastPage() }}</span>
            </div>
            <div class="card-body p-0">
                @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Edad</th>
                                <th>Rol</th>
                                <th>Creado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 35px; height: 35px; border-radius: 50%; font-weight: bold;">
                                            {{ strtoupper(substr($user->nombre, 0, 1) . substr($user->apellido, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $user->nombre }} {{ $user->apellido }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    {{ $user->edad }} años
                                    @if($user->edad < 18)
                                        <span class="badge bg-warning text-dark">Menor</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-crown"></i> Admin
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-user"></i> Usuario
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('users.show', $user) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Eliminar"
                                                onclick="confirmDelete({{ $user->id }}, '{{ $user->nombre }} {{ $user->apellido }}')">
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
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay usuarios registrados</p>
                    <a href="{{ route('users.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Crear Primer Usuario
                    </a>
                </div>
                @endif
            </div>
            @if($users->hasPages())
            <div class="card-footer py-3">
                {{ $users->appends(request()->query())->links('pagination.custom') }}
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
                <p>¿Estás seguro de que deseas eliminar al usuario <strong id="userName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-warning"></i> Esta acción no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar Usuario
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('deleteForm').action = `/users/${userId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush