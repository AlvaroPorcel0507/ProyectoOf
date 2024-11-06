@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Venta</h1>

    <!-- Formulario para seleccionar categoría y producto -->
    <form method="GET" action="{{ route('sales.create') }}">
        <!-- Selector de categoría -->
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

    <!-- Mostrar inventarios relacionados al producto seleccionado -->
    @if (isset($inventory) && count($inventory) > 0)
        <div class="form-group mt-3">
            <label>Inventarios del Producto Seleccionado:</label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Productor</th>
                        <th>Stock Disponible</th>
                        <th>Precio Unitario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                        <tr>
                            <!-- Si 'producer_name' no existe, muestra el nombre del productor, y usa el 'user' asociado -->
                            <td>{{ $item->user->name ?? 'Desconocido' }}</td> <!-- Asumiendo que hay una relación con User -->
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unitPrice }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>No se encontraron inventarios para el producto seleccionado.</p>
    @endif
</div>

<script>
// Función para cargar productos según la categoría seleccionada
document.addEventListener('DOMContentLoaded', function() {
    const selectedCategoryId = "{{ old('categoryId', request()->categoryId) }}";
    const selectedProductId = "{{ old('productId', request()->productId) }}";
    
    const productSelect = document.getElementById('productId');

    // Si hay una categoría seleccionada, actualizar productos
    if (selectedCategoryId) {
        loadProductsByCategory(selectedCategoryId, selectedProductId);
    }

    // Si ya hay un producto previamente seleccionado, mantener la selección
    if (selectedProductId) {
        productSelect.value = selectedProductId;
    }
});

// Almacena todos los productos en un objeto, agrupados por categoría
const productsByCategory = @json($products->groupBy('categoryId'));

// Función para cargar productos según la categoría seleccionada
function loadProductsByCategory(categoryId, selectedProductId = null) {
    const productSelect = document.getElementById('productId');
    productSelect.innerHTML = '<option value="">Seleccione un producto</option>'; // Reiniciar opciones

    // Verifica si existen productos en la categoría seleccionada
    if (categoryId && productsByCategory[categoryId]) {
        productsByCategory[categoryId].forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.name;
            if (product.id == selectedProductId) {
                option.selected = true;
            }
            productSelect.appendChild(option);
        });
    }
}
</script>

@endsection
