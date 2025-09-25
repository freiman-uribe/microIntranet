@if ($paginator->hasPages())
    <nav aria-label="Navegación de páginas">
        <ul class="pagination pagination-sm justify-content-center mb-0 flex-wrap">
            {{-- Botón Primera Página --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled d-none d-sm-block">
                    <span class="page-link">
                        <i class="fas fa-angle-double-left"></i>
                        <span class="d-none d-md-inline ms-1">Primera</span>
                    </span>
                </li>
            @else
                <li class="page-item d-none d-sm-block">
                    <a class="page-link" href="{{ $paginator->url(1) }}" rel="first" title="Primera página">
                        <i class="fas fa-angle-double-left"></i>
                        <span class="d-none d-md-inline ms-1">Primera</span>
                    </a>
                </li>
            @endif

            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-angle-left"></i>
                        <span class="d-none d-sm-inline ms-1">Anterior</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Página anterior">
                        <i class="fas fa-angle-left"></i>
                        <span class="d-none d-sm-inline ms-1">Anterior</span>
                    </a>
                </li>
            @endif

            {{-- Enlaces de páginas --}}
            @php
                $start = max($paginator->currentPage() - 1, 1);
                $end = min($start + 2, $paginator->lastPage());
                $start = max($end - 2, 1);
            @endphp

            {{-- Mostrar páginas en dispositivos grandes --}}
            <div class="d-none d-md-flex">
                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor
            </div>

            {{-- Mostrar solo página actual en dispositivos pequeños --}}
            <div class="d-flex d-md-none">
                <li class="page-item active">
                    <span class="page-link">{{ $paginator->currentPage() }}</span>
                </li>
                @if ($paginator->lastPage() > 1)
                    <li class="page-item disabled">
                        <span class="page-link">de {{ $paginator->lastPage() }}</span>
                    </li>
                @endif
            </div>

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Página siguiente">
                        <span class="d-none d-sm-inline me-1">Siguiente</span>
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <span class="d-none d-sm-inline me-1">Siguiente</span>
                        <i class="fas fa-angle-right"></i>
                    </span>
                </li>
            @endif

            {{-- Botón Última Página --}}
            @if ($paginator->hasMorePages())
                <li class="page-item d-none d-sm-block">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" rel="last" title="Última página">
                        <span class="d-none d-md-inline me-1">Última</span>
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled d-none d-sm-block">
                    <span class="page-link">
                        <span class="d-none d-md-inline me-1">Última</span>
                        <i class="fas fa-angle-double-right"></i>
                    </span>
                </li>
            @endif
        </ul>

        {{-- Información adicional --}}
        <div class="row mt-2">
            <div class="col-12 text-center">
                <small class="text-muted">
                    Mostrando {{ $paginator->firstItem() ?: 0 }} - {{ $paginator->lastItem() ?: 0 }} 
                    de {{ $paginator->total() }} resultados
                </small>
            </div>
        </div>
    </nav>

    {{-- Estilos CSS personalizados --}}
    <style>
        .pagination-sm .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
        }
        
        .pagination .page-item .page-link {
            transition: all 0.2s ease;
        }
        
        .pagination .page-item .page-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            font-weight: 600;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: transparent;
            border-color: #dee2e6;
        }

        @media (max-width: 576px) {
            .pagination-sm .page-link {
                padding: 0.25rem 0.4rem;
                font-size: 0.8rem;
            }
            
            .pagination .page-item .page-link i {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            .pagination .flex-wrap {
                gap: 0.25rem;
            }
        }
    </style>
@endif