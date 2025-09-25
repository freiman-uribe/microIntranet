@extends('layouts.app')

@section('title', 'Editar Cotización #' . $cotizacion->id . ' - Micro Intranet')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-edit"></i> Editar Cotización #{{ $cotizacion->id }}</h2>
                <p class="text-muted">Modifica los detalles de la cotización</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> Ver Cotización
                </a>
                <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Información de la Cotización</h5>
            </div>
            <div class="card-body">
                <form id="cotizacionForm" method="POST" action="{{ route('cotizaciones.update', $cotizacion) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_emision" class="form-label">
                                    <i class="fas fa-calendar"></i> Fecha de Emisión
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_emision') is-invalid @enderror" 
                                       id="fecha_emision" 
                                       name="fecha_emision" 
                                       value="{{ old('fecha_emision', $cotizacion->fecha_emision?->format('Y-m-d')) }}"
                                       required>
                                @error('fecha_emision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_actual" class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Total Actual
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="total_actual"
                                       value="${{ number_format($cotizacion->total_bruto, 2) }}"
                                       readonly>
                                <small class="text-muted">El total se recalcula automáticamente</small>
                            </div>
                        </div>
                    </div>

                    <!-- Productos de la cotización -->
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-boxes"></i> Productos Cotizados</h5>
                                <button type="button" class="btn btn-success btn-sm" onclick="agregarProducto()">
                                    <i class="fas fa-plus"></i> Agregar Producto
                                </button>
                            </div>
                        </div>
                    </div>
                <div id="productos-container">
                    @foreach($cotizacion->detalles as $index => $detalle)
                    <div class="producto-item border rounded p-3 mb-3" data-index="{{ $index }}">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Producto</label>
                                <select name="productos[{{ $index }}][sku]" 
                                        class="form-select producto-select @error('productos.'.$index.'.sku') is-invalid @enderror"
                                        onchange="actualizarPrecio(this, {{ $index }})" required>
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($productos as $producto)
                                    <option value="{{ $producto->sku }}" 
                                            data-precio="{{ $producto->precio_unitario }}"
                                            {{ $detalle->producto_sku == $producto->sku ? 'selected' : '' }}>
                                        {{ $producto->nombre }} - ${{ number_format($producto->precio_unitario, 2) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('productos.'.$index.'.sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Cantidad</label>
                                <input type="number" 
                                       name="productos[{{ $index }}][cantidad]" 
                                       class="form-control @error('productos.'.$index.'.cantidad') is-invalid @enderror"
                                       value="{{ old('productos.'.$index.'.cantidad', $detalle->cantidad) }}"
                                       min="1" 
                                       step="1"
                                       onchange="calcularSubtotal({{ $index }})"
                                       required>
                                @error('productos.'.$index.'.cantidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Precio Unit.</label>
                                <input type="number" 
                                       name="productos[{{ $index }}][precio_unitario]" 
                                       class="form-control precio-input"
                                       value="{{ old('productos.'.$index.'.precio_unitario', $detalle->precio_unitario) }}"
                                       step="0.01"
                                       min="0"
                                       onchange="calcularSubtotal({{ $index }})"
                                       readonly>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Subtotal</label>
                                <input type="text" 
                                       class="form-control subtotal-display"
                                       value="${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}"
                                       readonly>
                            </div>
                            
                            <div class="col-md-2 text-center">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm"
                                            onclick="eliminarProducto({{ $index }})"
                                            {{ count($cotizacion->detalles) <= 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="fas fa-calculator"></i> Total de la Cotización:</strong>
                            </div>
                            <h4 class="mb-0 text-primary" id="total-cotizacion">
                                ${{ number_format($cotizacion->total_bruto, 2) }}
                            </h4>
                        </div>
                    </div>
                </form>
            </div>
        </div>

                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" onclick="submitForm()">
                            <i class="fas fa-save"></i> Actualizar Cotización
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let contadorProductos = {{ count($cotizacion->detalles) }};

// Array de productos disponibles para JavaScript
const productosDisponibles = [
    @foreach($productos as $producto)
    {
        sku: '{{ $producto->sku }}',
        nombre: '{{ $producto->nombre }}',
        precio: {{ $producto->precio_unitario }}
    },
    @endforeach
];

function agregarProducto() {
    const container = document.getElementById('productos-container');
    
    console.log('Agregando producto. Contador actual:', contadorProductos);
    console.log('Productos existentes antes de agregar:', document.querySelectorAll('.producto-item').length);
    
    // Crear las opciones del select dinámicamente
    let optionsHtml = '<option value="">Seleccionar producto...</option>';
    productosDisponibles.forEach(producto => {
        optionsHtml += `<option value="${producto.sku}" data-precio="${producto.precio}">
            ${producto.nombre} - $${producto.precio.toFixed(2)}
        </option>`;
    });
    
    const productoHtml = `
        <div class="producto-item border rounded p-3 mb-3" data-index="${contadorProductos}">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Producto</label>
                    <select name="productos[${contadorProductos}][sku]" 
                            class="form-select producto-select"
                            onchange="actualizarPrecio(this, ${contadorProductos})" required>
                        ${optionsHtml}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" 
                           name="productos[${contadorProductos}][cantidad]" 
                           class="form-control"
                           value="1"
                           min="1" 
                           step="1"
                           onchange="calcularSubtotal(${contadorProductos})"
                           required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Precio Unit.</label>
                    <input type="number" 
                           name="productos[${contadorProductos}][precio_unitario]" 
                           class="form-control precio-input"
                           value="0"
                           step="0.01"
                           min="0"
                           onchange="calcularSubtotal(${contadorProductos})"
                           readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Subtotal</label>
                    <input type="text" 
                           class="form-control subtotal-display"
                           value="$0.00"
                           readonly>
                </div>
                <div class="col-md-2 text-center">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="button" 
                                class="btn btn-danger btn-sm"
                                onclick="eliminarProducto(${contadorProductos})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', productoHtml);
    console.log('Productos después de agregar:', document.querySelectorAll('.producto-item').length);
    
    reindexarProductos();
    
    console.log('Contador después de reindexar:', contadorProductos);
    console.log('Productos después de reindexar:', document.querySelectorAll('.producto-item').length);
    
    actualizarBotonesEliminar();
}

function eliminarProducto(index) {
    const elemento = document.querySelector(`[data-index="${index}"]`);
    if (elemento) {
        elemento.remove();
        reindexarProductos();
        calcularTotalCotizacion();
        actualizarBotonesEliminar();
    }
}

function actualizarBotonesEliminar() {
    const productos = document.querySelectorAll('.producto-item');
    const botones = document.querySelectorAll('.producto-item .btn-danger');
    
    botones.forEach(boton => {
        boton.disabled = productos.length <= 1;
    });
}

function reindexarProductos() {
    const productos = document.querySelectorAll('.producto-item');
    
    productos.forEach((producto, index) => {
        // Actualizar el data-index
        producto.setAttribute('data-index', index);
        
        // Actualizar los nombres de los campos
        const select = producto.querySelector('select');
        const cantidadInput = producto.querySelector('input[name*="cantidad"]');
        const precioInput = producto.querySelector('input[name*="precio_unitario"]');
        const eliminarBtn = producto.querySelector('.btn-danger');
        
        if (select) {
            select.name = `productos[${index}][sku]`;
            select.setAttribute('onchange', `actualizarPrecio(this, ${index})`);
        }
        
        if (cantidadInput) {
            cantidadInput.name = `productos[${index}][cantidad]`;
            cantidadInput.setAttribute('onchange', `calcularSubtotal(${index})`);
        }
        
        if (precioInput) {
            precioInput.name = `productos[${index}][precio_unitario]`;
            precioInput.setAttribute('onchange', `calcularSubtotal(${index})`);
        }
        
        if (eliminarBtn) {
            eliminarBtn.setAttribute('onclick', `eliminarProducto(${index})`);
        }
    });
    
    // Actualizar el contador para el próximo producto
    contadorProductos = productos.length;
}

function actualizarPrecio(select, index) {
    const option = select.options[select.selectedIndex];
    const precio = option.getAttribute('data-precio') || 0;
    const precioInput = document.querySelector(`input[name="productos[${index}][precio_unitario]"]`);
    
    if (precioInput) {
        precioInput.value = parseFloat(precio).toFixed(2);
        calcularSubtotal(index);
    }
}

function calcularSubtotal(index) {
    const cantidad = document.querySelector(`input[name="productos[${index}][cantidad]"]`).value || 0;
    const precio = document.querySelector(`input[name="productos[${index}][precio_unitario]"]`).value || 0;
    const subtotal = parseFloat(cantidad) * parseFloat(precio);
    
    const subtotalDisplay = document.querySelector(`[data-index="${index}"] .subtotal-display`);
    if (subtotalDisplay) {
        subtotalDisplay.value = `$${subtotal.toFixed(2)}`;
    }
    
    calcularTotalCotizacion();
}

function calcularTotalCotizacion() {
    let total = 0;
    
    document.querySelectorAll('.producto-item').forEach((item, index) => {
        const cantidad = item.querySelector('input[name*="[cantidad]"]').value || 0;
        const precio = item.querySelector('input[name*="[precio_unitario]"]').value || 0;
        total += parseFloat(cantidad) * parseFloat(precio);
    });
    
    document.getElementById('total-cotizacion').textContent = `$${total.toFixed(2)}`;
    document.getElementById('total_actual').value = `$${total.toFixed(2)}`;
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarBotonesEliminar();
});

function debugForm() {
    const form = document.getElementById('cotizacionForm');
    if (form) {
        const formData = new FormData(form);
        
        console.log('Form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
    }
}

function submitForm() {
    console.log('Submit button clicked');
    
    const form = document.getElementById('cotizacionForm');
    if (form) {
        console.log('Form found, submitting...');
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        
        // Debug detallado de productos
        const productItems = document.querySelectorAll('.producto-item');
        console.log('Total product items found:', productItems.length);
        
        // Analizar cada producto individualmente
        productItems.forEach((item, index) => {
            const sku = item.querySelector('select[name*="[sku]"]');
            const cantidad = item.querySelector('input[name*="[cantidad]"]');
            const precio = item.querySelector('input[name*="[precio_unitario]"]');
            
            // Verificar si el elemento está dentro del formulario
            const dentroDelForm = form.contains(item);
            
            console.log(`=== PRODUCTO ${index} ===`);
            console.log('SKU:', {
                name: sku ? sku.name : 'NO FOUND',
                value: sku ? sku.value : 'NO VALUE',
                disabled: sku ? sku.disabled : 'N/A',
                dentroDelForm: sku ? form.contains(sku) : 'N/A'
            });
            console.log('Cantidad:', {
                name: cantidad ? cantidad.name : 'NO FOUND',
                value: cantidad ? cantidad.value : 'NO VALUE',
                disabled: cantidad ? cantidad.disabled : 'N/A',
                dentroDelForm: cantidad ? form.contains(cantidad) : 'N/A'
            });
            console.log('Precio:', {
                name: precio ? precio.name : 'NO FOUND',
                value: precio ? precio.value : 'NO VALUE',
                disabled: precio ? precio.disabled : 'N/A',
                dentroDelForm: precio ? form.contains(precio) : 'N/A'
            });
            console.log('Contenedor dentro del form:', dentroDelForm);
        });
        
        // Mostrar datos del formulario antes de enviar
        debugForm();
        
        // Debug específico de FormData
        const formData = new FormData(form);
        console.log('=== DEBUGGING FORMDATA ===');
        
        // Verificar todos los campos productos en FormData
        const productosEnFormData = [];
        for (let [key, value] of formData.entries()) {
            if (key.includes('productos[')) {
                productosEnFormData.push({key, value});
            }
        }
        console.log('Todos los campos productos en FormData:');
        productosEnFormData.forEach((campo, index) => {
            console.log(`  ${index}: ${campo.key} = ${campo.value}`);
        });
        
        let productCount = 0;
        for (let [key, value] of formData.entries()) {
            if (key.includes('productos[') && key.includes('[sku]')) {
                productCount++;
                console.log(`SKU encontrado en FormData: ${key} = ${value}`);
            }
        }
        console.log('Products with SKU to be sent:', productCount);
        
        if (productCount === 0) {
            alert('Error: No hay productos para enviar. Agrega al menos un producto.');
            return false;
        }
        
        // Enviar el formulario
        form.submit();
    } else {
        console.error('Form not found!');
    }
}
</script>
@endpush