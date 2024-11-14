@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Venta</h1>

    <!-- Mostrar mensajes de éxito o error -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulario para seleccionar categoría y producto -->
    <form method="GET" action="{{ route('sales.create') }}">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="categoryId" style="font-size: 18px; font-weight: bold;">Selecciona Categoría:</label>
                    <select name="categoryId" id="categoryId" class="form-control" onchange="this.form.submit()" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('categoryId', request()->categoryId) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="product_name" class="form-label">Selecciona Producto</label>
                <div class="col-md-8">
                    <input list="product-list" id="product_name" class="form-control" placeholder="Selecciona un producto" required>
                    
                    <datalist id="product-list">
                        @foreach ($products as $product)
                            <option value="{{ $product->name }}  {{ old('productId', request()->productId) == $product->id ? 'selected' : '' }}" data-id="{{ $product->id }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </datalist>
                    
                    <input type="hidden" id="productId" name="productId" required>
                </div>
            </div>
        </div>
    </form>

    @if (isset($inventory) && count($inventory) > 0)
        <div class="form-group mt-3">
            <label style="font-size: 25px; font-weight: bold;">Inventarios del Producto Seleccionado:</label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Productor</th>
                        <th>Stock Disponible</th>
                        <th>Precio Unitario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                        <tr>
                            <!-- Mostrar nombre y ID del producto -->
                            <td>{{ $item['product_name'] }} </td>
                            
                            <!-- Mostrar nombre y ID del productor -->
                            <td>{{ $item['producer_name'] ?? 'Desconocido' }}</td>
                            
                            <!-- Stock disponible -->
                            <td>{{ $item['stock'] }}</td>
                            
                            <!-- Precio unitario -->
                            <td>{{ $item['unitPrice'] }}</td>
                            
                            <td>
                                <!-- Formulario para agregar al carrito -->
                                <form action="{{ route('sales.addToCart') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="productId" value="{{ $item['product']->id }}"> <!-- ID del producto -->
                                    <input type="hidden" name="unitPrice" value="{{ $item['unitPrice'] }}"> <!-- Precio unitario -->
                                    <input type="hidden" name="productName" value="{{ $item['product']->name }}"> <!-- Nombre del producto -->
                                    <input type="hidden" name="producerName" value="{{ $item['user']->name ?? 'Desconocido' }}"> <!-- Nombre del productor -->
                                    <input type="hidden" name="producerId" value="{{ $item['user']->id }}"> <!-- ID del productor (nuevo campo) -->
                                    
                                    <!-- Validar que la cantidad no sea mayor al stock disponible -->
                                    <input type="number" name="stock" value="1" min="1" max="{{ $item['stock'] }}" class="form-control" style="width: 70px; display: inline-block;" required>
                                    <button type="submit" class="btn btn-success btn-sm">Agregar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="font-size: 15px; font-weight: bold;">No se encontraron inventarios para el producto seleccionado.</p>
    @endif

    <!-- Carrito -->
    <div class="mt-3">
        <h4 style="font-size: 25px; font-weight: bold;">Carrito de Compras</h4>
        @if(session('cart') && count(session('cart')) > 0)
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                        <th>Productor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('cart') as $productId => $item)
                        <tr id="product-{{ $productId }}">
                            <td>{{ $item['name'] ?? 'Producto no especificado' }}</td> <!-- Nombre del producto -->
                            <td>
                                <form action="{{ route('sales.updateQuantity') }}" method="POST" style="display:inline;" class="update-quantity-form">
                                    @csrf
                                    <input type="hidden" name="productId" value="{{ $productId }}">
                                    <input type="hidden" name="producerId" value="{{ $item['producer_id'] ?? '' }}"> <!-- ID del productor -->
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control quantity" style="width: 70px; display: inline-block;" required oninput="updateTotal()">
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Actualizar</button>
                                </form>
                            </td>
                            <td>{{ $item['unitPrice'] }}</td>
                            <td class="total-product">{{ $item['totalProduct'] }}</td> <!-- Total por producto -->
                            <td>{{ $item['producer_name'] ?? 'Productor no especificado' }}</td> <!-- Nombre del productor -->
                            <td>
                                <!-- Botón para eliminar del carrito -->
                                <form action="{{ route('sales.removeFromCart') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="productId" value="{{ $productId }}">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total general -->
            <div>
                <strong>Total General: </strong> <span id="total-general"></span>
            </div>
        @else
            <p style="font-size: 15px; font-weight: bold;">No hay productos en el carrito.</p>
        @endif
    </div>
    <form action="{{ route('sales.processSale') }}" method="POST">
    @csrf
    @foreach(session('cart', []) as $productId => $item)

        <input type="hidden" name="cart[{{ $productId }}][productId]" value="{{ $productId }}">
        <input type="hidden" name="cart[{{ $productId }}][quantity]" value="{{ $item['quantity'] }}">
        <input type="hidden" name="cart[{{ $productId }}][unitPrice]" value="{{ $item['unitPrice'] }}">
        <input type="hidden" name="cart[{{ $productId }}][totalProduct]" value="{{ $item['totalProduct'] }}">
        <input type="hidden" name="cart[{{ $productId }}][productName]" value="{{ $item['name'] }}">
        <input type="hidden" name="cart[{{ $productId }}][producerId]" value="{{ $item['producer_id'] }}">
    @endforeach
    <button type="submit" class="btn btn-success">Vender Todos</button>
</form>

</div>

<script>
// Actualiza el total general del carrito
function updateTotal() {
    let totalGeneral = 0;

    // Iterar sobre todos los productos en el carrito
    document.querySelectorAll('tr[id^="product-"]').forEach(row => {
        const quantity = row.querySelector('.quantity').value;
        const unitPrice = row.querySelector('td:nth-child(3)').textContent;
        const totalProduct = quantity * parseFloat(unitPrice);

        // Actualizar el subtotal de cada producto
        row.querySelector('.total-product').textContent = totalProduct.toFixed(2);

        // Sumar al total general
        totalGeneral += totalProduct;
    });

    // Mostrar el total general
    document.getElementById('total-general').textContent = totalGeneral.toFixed(2);
}

// Llamar a la función al cargar la página para calcular el total inicial
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
});


    // Script para capturar el id del producto seleccionado
    document.getElementById('product_name').addEventListener('input', function() {
        const productList = document.getElementById('product-list');
        const productIdField = document.getElementById('productId');
        
        // Buscar la opción que coincida con el nombre ingresado
        let selectedOption = Array.from(productList.options).find(option => option.value === this.value);
        
        // Si se encuentra la opción, actualizar el campo oculto con el productId
        if (selectedOption) {
            productIdField.value = selectedOption.getAttribute('data-id');
        } else {
            // Si no hay coincidencia, limpiar el campo oculto
            productIdField.value = '';
        }
    });
</script>
@endsection
