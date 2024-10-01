@extends('layouts.app')

@section('content')
@php
 use App\Models\Products;
 use App\Models\Category;
@endphp

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Registrar Nuevo Producto</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div class="card">
        <div class="card-header">
            Añadir Producto
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

            <form id="productsForm" action="{{ route('products.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre del Producto</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Descripcion Breve</label>
                    <input type="text" name="description" id="description" class="form-control" value="{{ old('description') }}" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock (Unidades)</label>
                    <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock') }}" required>
                </div>

                <div class="form-group">
                    <label for="unitPrice">Precio Unitario Bs.</label>
                    <input type="number" name="unitPrice" id="unitPrice" class="form-control" value="{{ old('unitPrice') }}" required>
                </div>

                <div class="form-group">
                    <label for="categoryId">Categoria</label>
                    <select name="categoryId" id="categoryId" class="form-control" require>
                    <option value="" selected>SELECCIONE UNA CATEGORIA</option>
                    @foreach (App\Models\Category::all() as $category)
                        @if(($category->status)==1)
                        <option value="{{ $category->id }}">{{ optional(Category::find($category->id))->name }}</option>
                        @endif
                    @endforeach
                    </select>
                </div>                              

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
                    Registrar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Estás a punto de registrar un nuevo producto con los siguientes datos:</p>
                <ul>
                    <li><strong>Nombre Categoria:</strong> <span id="modalName"></span></li>
                    <li><strong>Descripcion:</strong> <span id="modalDescription"></span></li>
                    <li><strong>Stock:</strong> <span id="modalStock"></span></li>
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
        // Cargar datos en el modal cuando se hace clic en "Registrar"
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

        // Enviar el formulario al confirmar
        document.getElementById('confirmButton').addEventListener('click', function() {
            document.getElementById('productsForm').submit();
        });
    });
</script>

@endsection
