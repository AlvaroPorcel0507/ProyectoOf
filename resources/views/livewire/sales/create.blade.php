@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 text-green-800">Registrar Nueva Venta</h1>
    
    <form action="{{ route('sales.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="customerId" class="form-label">Cliente</label>
            <select name="customerId" id="customerId" class="form-select" required>
                <option value="">Seleccione un cliente</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-3">
            <label for="products" class="form-label">Productos</label>
            <div id="product-list">
                <div class="d-flex align-items-center mb-2">
                    <select name="products[0][id]" class="form-select me-2" required>
                        <option value="">Seleccione un producto</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} - ${{ $product->price }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="products[0][quantity]" class="form-control me-2" placeholder="Cantidad" min="1" required>
                    <button type="button" class="btn btn-danger" onclick="removeProduct(this)">Eliminar</button>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addProduct()">Agregar Producto</button>
        </div>
        
        <button type="submit" class="btn btn-success">Registrar Venta</button>
    </form>
</div>

<script>
    let productIndex = 1; // Contador para los productos

    function addProduct() {
        const productList = document.getElementById('product-list');
        const productDiv = document.createElement('div');
        productDiv.className = 'd-flex align-items-center mb-2';
        productDiv.innerHTML = `
            <select name="products[${productIndex}][id]" class="form-select me-2" required>
                <option value="">Seleccione un producto</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} - ${{ $product->price }}</option>
                @endforeach
            </select>
            <input type="number" name="products[${productIndex}][quantity]" class="form-control me-2" placeholder="Cantidad" min="1" required>
            <button type="button" class="btn btn-danger" onclick="removeProduct(this)">Eliminar</button>
        `;
        productList.appendChild(productDiv);
        productIndex++;
    }

    function removeProduct(button) {
        button.parentElement.remove();
    }
</script>
@endsection
