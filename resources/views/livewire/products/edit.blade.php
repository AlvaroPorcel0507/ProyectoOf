@extends('layouts.app')

@section('content')
@php
  use App\Models\Category;
  use App\Models\Inventory;
@endphp
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Editar Producto</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Volver</a>
    </div>
    
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            Formulario de Edición de Producto
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="productForm" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nombre de Producto</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-control" disabled required>
                </div>

                <div class="form-group">
                    <label for="description">Descripcion</label>
                    <input type="text" name="description" id="description" value="{{ old('description', $product->description) }}" class="form-control" disabled required>
                </div>

                <div class="form-group">
                    <label for="measurementUnit">Medida</label>
                    <select name="measurementUnit" id="measurementUnit" class="form-control" readonly>
                        <option value="{{ optional(Inventory::find($product->id))->measurementUnit }}" selected>{{ optional(Inventory::find($product->id))->measurementUnit }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <h2>Stock Actual en {{ optional(Inventory::find($product->id))->measurementUnit }} es {{ optional(Inventory::find($product->id))->quantity }}</h2>
                </div>

                <div class="form-group">
                    <label for="quantity">Cantidad</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" required>
                </div> 

                <div class="form-group">
                    <label for="unitPrice">Precio Unitario Bs.</label>
                    <input type="number" step="0.01" name="unitPrice" id="unitPrice" value="{{ number_format(optional(Inventory::find($product->id))->unitPrice, 2, '.', '') }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="categoryId">Categoria</label>
                    <select name="categoryId" id="categoryId" class="form-control" required>
                        <option value="{{ old('categoryId', $product->categoryId) }}" selected>{{ optional(Category::find($product->categoryId))->name }}</option>
                        @foreach (App\Models\Category::all() as $category)
                            @if(($category->status) == 1)
                                <option value="{{ $category->id }}">{{ optional(Category::find($category->id))->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <button type="button" class="btn btn-success mt-4" data-toggle="modal" data-target="#confirmModal">
                    Actualizar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Actualización</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Estás a punto de actualizar los datos del producto con los siguientes detalles:</p>
                <ul>
                    <li><strong>Nombre:</strong> <span id="modalName"></span></li>
                    <li><strong>Descripcion:</strong> <span id="modalDescription"></span></li>
                    <li><strong>Precio Unitario:</strong> <span id="modalUnitPrice"></span></li>
                    <li><strong>Categoria:</strong> <span id="modalCategoryId"></span></li>
                </ul>
                <p>¿Estás seguro de que deseas continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const descriptionInput = document.getElementById('description');
        const stockInput = document.getElementById('stock');
        const unitPriceInput = document.getElementById('unitPrice');
        const categoryIdInput = document.getElementById('categoryId');

        const modalName = document.getElementById('modalName');
        const modalDescription = document.getElementById('modalDescription');
        const modalStock = document.getElementById('modalStock');
        const modalUnitPrice = document.getElementById('modalUnitPrice');
        const modalCategoryId = document.getElementById('modalCategoryId');

        document.querySelector('[data-target="#confirmModal"]').addEventListener('click', function() {
            modalName.textContent = nameInput.value;
            modalDescription.textContent = descriptionInput.value;
            modalStock.textContent = stockInput.value;
            modalUnitPrice.textContent = unitPriceInput.value;
            modalCategoryId.textContent = categoryIdInput.options[categoryIdInput.selectedIndex].text;
        });

        document.getElementById('confirmButton').addEventListener('click', function() {
            document.getElementById('productForm').submit();
        });
    });
</script>
@endsection
