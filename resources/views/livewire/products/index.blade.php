@extends('layouts.app')

@section('content')
@php
    use App\Models\User;
    use App\Models\Product;
    use App\Models\Category;
    use App\Models\Inventory;
    use App\Models\TotalProduct;
@endphp

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Productos</h1>
        @if(Auth::User()->role == 'Productor')
        <a href="{{ route('products.create') }}" class="btn btn-success">
            Registrar Nuevo Producto
        </a>
        @endif
    </div>
    
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-users"></i> Productos
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th scope="col">Nro.</th>
                            <th scope="col">Nombre Producto</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Detalles</th>
                        </tr>
                    </thead>
                    @if(Auth::User()->role == 'Administrador')
                    <tbody>
                        @php
                        $cont = 1;
                        @endphp
                        @foreach ($products as $product)
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->description }}</td>

                                <td>   
                                    <button type="button" class="btn btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#toggleStatusModal" 
                                        data-product-name="{{ $product->name }}" 
                                        data-product-stock="{{ $product->stock }}" 
                                        data-product-unitprice="{{ optional($product->inventory->first())->unitPrice }}" 
                                        data-product-measurementunit="{{ optional($product->inventory->first())->measurementUnit }}" 
                                        data-product-categoryid="{{ optional($product->categories)->name }}">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                            @php $cont++; @endphp
                        @endforeach
                    </tbody>
                    @else
                        <tbody>
                            @php
                            $cont = 1;
                            @endphp
                            @foreach ($products as $product)
                                @if(Auth::User()->id == $product->userId)
                                <tr>
                                    <th scope="row">{{ $cont }}</th>
                                    <td>{{ optional(Product::find($product->id))->name }}</td>
                                    <td>{{ $product->description }}</td>

                                    <td>   
                                        <button 
                                            type="button" 
                                            class="btn btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#toggleStatusModal" 
                                            data-product-id="{{ $product->id }}"
                                            onclick="loadProductDetails(this)">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                                @php $cont++; @endphp
                                @endif
                            @endforeach
                        </tbody>
                    @endif
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(['sort_field' => $sortField, 'sort_direction' => $sortDirection])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="toggleStatusModalLabel">DETALLE DE PRODUCTO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Stock Disponible</th>
                            <th>Precio Unitario</th>
                            <th>Unidad de Medida</th>
                        </tr>
                    </thead>
                    <tbody id="productDetailsBody">
                        <!-- Las filas de productos se agregarán aquí dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
// Escuchar el evento 'show.bs.modal' para mostrar los detalles del producto
function loadProductDetails(button) {
    const productId = button.getAttribute('data-product-id');
    
    fetch(`/products/${productId}/details`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('productDetailsBody');
            tbody.innerHTML = '';

            if (data.inventory.length > 0) {
                data.inventory.forEach(item => {
                    const row = `
                        <tr>
                            <td>${data.product.name ?? No encontrado}</td>
                            <td>${item.quantity ?? No encontrado}</td>
                            <td>${item.unitPrice ?? No encontrado}</td>
                            <td>${item.measurementUnit ?? No encontrado}</td>
                        </tr>`;
                    tbody.innerHTML += row;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="4">No hay registros disponibles</td></tr>`;
            }
        })
        .catch(error => console.error('Error al cargar los detalles:', error));
}

</script>
@endpush

@endsection
