@extends('layouts.app')

@section('content')
@php
    use App\Models\Product;
    use App\Models\Category;
    use App\Models\Inventory;
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

            <form id="productsForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="productSelect">Seleccionar Producto Existente</label>
                    <select name="productSelect" id="productSelect" class="form-control" required>
                        <option value="" selected>SELECCIONE UN PRODUCTO</option>
                        @foreach (Product::all() as $product)
                            @if ($product->status == 1)
                            @php
                                $totalQuantity = Inventory::where('productId', $product->id)->sum('quantity');
                                $latestInventory = Inventory::where('productId', $product->id)->latest()->first();
                                $measurementUnit = $latestInventory ? $latestInventory->measurementUnit : '';
                                $unitPrice = $latestInventory ? $latestInventory->unitPrice : 0;
            
                                if ($measurementUnit === 'Caja') {
                                    $convertedQuantity = $totalQuantity / 25;
                                } elseif ($measurementUnit === 'Carga') {
                                    $convertedQuantity = $totalQuantity / 60;
                                } else {
                                    $convertedQuantity = $totalQuantity;
                                }
                            @endphp
                            <option value="{{ $product->id }}" 
                                    data-description="{{ $product->description }}" 
                                    data-measurement-unit="{{ $measurementUnit }}" 
                                    data-category-id="{{ $product->categoryId }}"
                                    data-quantity="{{ $convertedQuantity }}"
                                    data-unit-price="{{ $unitPrice }}">
                                {{ $product->name }}
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="newProductName">Nombre del Nuevo Producto</label>
                    <input type="text" name="newProductName" id="newProductName" class="form-control" placeholder="Ingrese el nuevo producto" disabled>
                </div>

                <div class="form-group">
                    <label for="description">Descripción Breve</label>
                    <input type="text" name="description" id="description" class="form-control" required readonly>
                </div>

                <div class="form-group">
                    <label for="measurementUnit">Medida</label>
                    <select name="measurementUnit" id="measurementUnit" class="form-control" required readonly>
                        <option value="" selected>SELECCIONE UNA CATEGORIA</option>
                        <option value="Caja">Caja</option>
                        <option value="Carga">Carga</option>
                    </select>
                </div>

                <div class="form-group">
                    <h1>Stock Disponible: <span id="currentStock">0</span></h1>
                </div>

                <div class="form-group">
                    <label for="quantity">Cantidad</label>
                    <input type="text" name="quantity" id="quantity" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="unitPrice">Precio Unitario Bs.</label>
                    <input type="number" name="unitPrice" id="unitPrice" class="form-control" value="{{ old('unitPrice', $lastInventory->unitPrice ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="categoryId">Categoría</label>
                    <select name="categoryId" id="categoryId" class="form-control" required readonly>
                        <option value="" selected>SELECCIONE UNA CATEGORIA</option>
                        @foreach (Category::all() as $category)
                            @if(($category->status) == 1)
                            <option value="{{ $category->id }}">{{ optional(Category::find($category->id))->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <button type="button" class="btn btn-primary" id="openConfirmModal">Registrar Producto</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Transacción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas realizar esta transacción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('newProductName');
        const descriptionInput = document.getElementById('description');
        const stockInput = document.getElementById('quantity');
        const unitPriceInput = document.getElementById('unitPrice');
        const categoryIdInput = document.getElementById('categoryId');
        const currentStock = document.getElementById('currentStock');

        document.getElementById('openConfirmModal').addEventListener('click', function() {
            $('#confirmModal').modal('show');
        });

        document.getElementById('confirmSubmit').addEventListener('click', function() {
            document.getElementById('productsForm').submit();
        });

        document.getElementById('productSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption.value) {
                // Rellenar los campos con los datos del producto seleccionado
                document.getElementById('description').value = selectedOption.getAttribute('data-description');
                document.getElementById('measurementUnit').value = selectedOption.getAttribute('data-measurement-unit');
                document.getElementById('categoryId').value = selectedOption.getAttribute('data-category-id');
                document.getElementById('unitPrice').value = selectedOption.getAttribute('data-unit-price');
                currentStock.textContent = selectedOption.getAttribute('data-quantity');

                // Hacer los campos solo lectura
                document.getElementById('description').readOnly = true;
                document.getElementById('measurementUnit').readOnly = true;
                document.getElementById('categoryId').readOnly = true;

                // Bloquear el input de nuevo producto
                document.getElementById('newProductName').disabled = true;
            } else {
                // Limpiar los campos si no hay selección
                document.getElementById('description').value = '';
                document.getElementById('measurementUnit').value = '';
                document.getElementById('categoryId').value = '';
                document.getElementById('unitPrice').value = ''; // Limpiar el precio
                currentStock.textContent = '0'; // Restablecer el stock

                // Hacer los campos editables
                document.getElementById('description').readOnly = false;
                document.getElementById('measurementUnit').disabled = false;
                document.getElementById('categoryId').disabled = false;

                // Habilitar el input de nuevo producto
                document.getElementById('newProductName').disabled = false;
            }
        });
    });
</script>

@endsection
