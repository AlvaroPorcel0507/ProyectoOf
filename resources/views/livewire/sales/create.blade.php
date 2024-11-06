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
                    <label for="categoryId">Selecciona Categoría:</label>
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

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="productId">Selecciona Producto:</label>
                    <select name="productId" id="productId" class="form-control" onchange="this.form.submit()" required>
                        <option value="">Seleccione un producto</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('productId', request()->productId) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>

    @if (isset($inventory) && count($inventory) > 0)
        <div class="form-group mt-3">
            <label>Inventarios del Producto Seleccionado:</label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto (ID)</th>
                        <th>Productor (ID)</th>
                        <th>Stock Disponible</th>
                        <th>Precio Unitario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                        <tr>
                            <!-- Mostrar nombre y ID del producto -->
                            <td>{{ $item['product']->name }} (ID: {{ $item['product']->id }})</td>
                            
                            <!-- Mostrar nombre y ID del productor -->
                            <td>{{ $item['user']->name ?? 'Desconocido' }} (ID: {{ $item['user']->id }})</td>
                            
                            <!-- Stock disponible -->
                            <td>{{ $item['quantity'] }}</td>
                            
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
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $item['quantity'] }}" class="form-control" style="width: 70px; display: inline-block;" required>
                                    <button type="submit" class="btn btn-success btn-sm">Agregar</button>
                                </form>
                                
                                <!-- Formulario para disminuir la cantidad -->
                                <form action="{{ route('sales.removeFromCart') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="productId" value="{{ $item['product']->id }}">
                                    <button type="submit" class="btn btn-danger btn-sm">Disminuir</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>No se encontraron inventarios para el producto seleccionado.</p>
    @endif

    <!-- Carrito -->
    <div class="mt-3">
        <h4>Carrito de Compras</h4>
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
            <p>No hay productos en el carrito.</p>
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
</script>
@endsection
