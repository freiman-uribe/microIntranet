@extends('layouts.app')

@section('title', 'Crear Cotización - Micro Intranet')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-plus-circle"></i> Crear Nueva Cotización</h2>
                <p class="text-muted">Selecciona los productos y cantidades para la cotización</p>
            </div>
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<form id="cotizacionForm" method="POST" action="{{ route('cotizaciones.store') }}">
    @csrf
    
    <div class="row">
        <!-- Información General -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Información General
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Usuario:</label>
                        <p class="form-control-plaintext">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <p class="form-control-plaintext">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Emisión:</label>
                        <p class="form-control-plaintext">{{ now()->format('d/m/Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Bruto:</label>
                        <h4 class="text-success" id="totalBruto">$0.00</h4>
                    </div>
                </div>
            </div>

            <!-- Resumen de Productos -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-shopping-cart"></i> Resumen (<span id="totalItems">0</span> items)
                    </h6>
                </div>
                <div class="card-body" id="resumenProductos">
                    <p class="text-muted text-center">No hay productos seleccionados</p>
                </div>
            </div>
        </div>

        <!-- Selección de Productos -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-boxes"></i> Seleccionar Productos
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="agregarTodos()">
                        <i class="fas fa-plus"></i> Agregar Todos
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($productos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $producto)
                                <tr id="producto-{{ $producto->sku }}">
                                    <td>
                                        <code>{{ $producto->sku }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $producto->nombre }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-success">
                                            ${{ number_format($producto->precio_unitario, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               class="form-control form-control-sm cantidad-input" 
                                               id="cantidad-{{ $producto->sku }}"
                                               min="0" 
                                               max="999"
                                               value="0"
                                               data-sku="{{ $producto->sku }}"
                                               data-precio="{{ $producto->precio_unitario }}"
                                               data-nombre="{{ $producto->nombre }}"
                                               style="width: 80px;"
                                               onchange="actualizarProducto('{{ $producto->sku }}')">
                                    </td>
                                    <td>
                                        <strong class="subtotal-{{ $producto->sku }}">$0.00</strong>
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary"
                                                onclick="agregarProducto('{{ $producto->sku }}')"
                                                title="Agregar/Actualizar">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay productos disponibles</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Total de la Cotización: <span class="text-success" id="totalFinal">$0.00</span></h5>
                            <small class="text-muted">Productos seleccionados: <span id="productosSeleccionados">0</span></small>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-success btn-lg" id="btnGuardar" disabled>
                                <i class="fas fa-save"></i> Guardar Cotización
                            </button>
                            <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inputs ocultos para los productos seleccionados -->
    <div id="productosSeleccionadosInputs"></div>
</form>
@endsection

@push('scripts')
<script>
let productosSeleccionados = {};
let totalBruto = 0;

function actualizarProducto(sku) {
    const cantidadInput = document.getElementById(`cantidad-${sku}`);
    const cantidad = parseInt(cantidadInput.value) || 0;
    const precio = parseFloat(cantidadInput.dataset.precio);
    const subtotal = cantidad * precio;
    
    // Actualizar subtotal en la tabla
    document.querySelector(`.subtotal-${sku}`).textContent = `$${subtotal.toFixed(2)}`;
    
    // Actualizar productos seleccionados
    if (cantidad > 0) {
        productosSeleccionados[sku] = {
            sku: sku,
            cantidad: cantidad,
            precio: precio,
            nombre: cantidadInput.dataset.nombre,
            subtotal: subtotal
        };
    } else {
        delete productosSeleccionados[sku];
    }
    
    actualizarResumen();
}

function agregarProducto(sku) {
    const cantidadInput = document.getElementById(`cantidad-${sku}`);
    const cantidadActual = parseInt(cantidadInput.value) || 0;
    cantidadInput.value = cantidadActual + 1;
    actualizarProducto(sku);
}

function agregarTodos() {
    document.querySelectorAll('.cantidad-input').forEach(input => {
        if (parseInt(input.value) === 0) {
            input.value = 1;
            actualizarProducto(input.dataset.sku);
        }
    });
}

function eliminarProducto(sku) {
    document.getElementById(`cantidad-${sku}`).value = 0;
    actualizarProducto(sku);
}

function actualizarResumen() {
    const resumenDiv = document.getElementById('resumenProductos');
    const totalItems = document.getElementById('totalItems');
    const totalBrutoElement = document.getElementById('totalBruto');
    const totalFinalElement = document.getElementById('totalFinal');
    const productosSeleccionadosElement = document.getElementById('productosSeleccionados');
    const btnGuardar = document.getElementById('btnGuardar');
    const inputsContainer = document.getElementById('productosSeleccionadosInputs');
    
    const productos = Object.values(productosSeleccionados);
    const cantidadTotal = productos.length;
    totalBruto = productos.reduce((sum, producto) => sum + producto.subtotal, 0);
    
    // Actualizar contadores
    totalItems.textContent = cantidadTotal;
    productosSeleccionadosElement.textContent = cantidadTotal;
    totalBrutoElement.textContent = `$${totalBruto.toFixed(2)}`;
    totalFinalElement.textContent = `$${totalBruto.toFixed(2)}`;
    
    // Actualizar resumen
    if (cantidadTotal > 0) {
        let resumenHTML = '';
        productos.forEach(producto => {
            resumenHTML += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <small><strong>${producto.nombre}</strong></small><br>
                        <small class="text-muted">${producto.cantidad} x $${producto.precio.toFixed(2)}</small>
                    </div>
                    <div class="text-end">
                        <strong class="text-success">$${producto.subtotal.toFixed(2)}</strong><br>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarProducto('${producto.sku}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        resumenDiv.innerHTML = resumenHTML;
        btnGuardar.disabled = false;
    } else {
        resumenDiv.innerHTML = '<p class="text-muted text-center">No hay productos seleccionados</p>';
        btnGuardar.disabled = true;
    }
    
    // Actualizar inputs ocultos
    inputsContainer.innerHTML = '';
    productos.forEach((producto, index) => {
        inputsContainer.innerHTML += `
            <input type="hidden" name="productos[${index}][sku]" value="${producto.sku}">
            <input type="hidden" name="productos[${index}][cantidad]" value="${producto.cantidad}">
        `;
    });
}

// Validación del formulario
document.getElementById('cotizacionForm').addEventListener('submit', function(e) {
    if (Object.keys(productosSeleccionados).length === 0) {
        e.preventDefault();
        alert('Debe seleccionar al menos un producto para crear la cotización.');
        return false;
    }
});
</script>
@endpush