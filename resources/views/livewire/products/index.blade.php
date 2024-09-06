@extends('layouts.app')



@section('content')
@php
 use App\Models\Products;
@endphp

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Productos</h1>
        <a href="{{ route('products.create') }}" class="btn btn-success">
            Registrar Nuevo Producto
        </a>
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
                            <th scope="col">Nombre Categoria</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Precio Unitario</th>
                            <th scope="col">Categoria</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $cont=1;
                        @endphp
                        @foreach ($products as $product)
                        
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>
                                    {{ optional(Products::find($product->id))->name }}
                                </td>
                                <td>{{ $product->description }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>{{ $product->unitPrice }}</td>
                                <td>{{ $product->categoryId }}</td>
                                <td>
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#toggleStatusModal" 
                                        data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-description="{{ $product->description }}" 
                                        data-product-stock="{{ $product->stock }}" data-product-unitPrice="{{ $product->unitPrice }}" data-product-categoryId="{{ $product->categoryId }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @php $cont++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(['sort_field' => $sortField, 'sort_direction' => $sortDirection])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cambio de Estado -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="toggleStatusModalLabel">Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar el producto <strong id="´productName"></strong>? Esta acción elimiara el producto.
            </div>
            <div class="modal-footer">
                <form id="toggleStatusFormProducts" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleStatusModal = document.getElementById('toggleStatusModal');
        toggleStatusModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; 
            var productId = button.getAttribute('data-product-id'); 
            var productName = button.getAttribute('data-product-name'); 
            var productDescription = button.getAttribute('data-product-description'); 
            var productStock = button.getAttribute('data-product-stock'); 
            var productUnitPrice = button.getAttribute('data-product-unitPrice'); 
            var productCategoryId = button.getAttribute('data-product-categoryId'); 
            var form = toggleStatusModal.querySelector('#toggleStatusFormProducts');
            form.action = '/products/' + productId + '/softDelete';

            var actionText = productStatus == 1 ? 'deshabilitar' : 'habilitar';
            var toggleStatusActionElement = document.getElementById('toggleStatusAction');
            toggleStatusActionElement.textContent = actionText;

            var productNameElement = document.getElementById('productName');
            productNameElement.textContent = productName;
        });
    });
</script>
@endpush
@endsection