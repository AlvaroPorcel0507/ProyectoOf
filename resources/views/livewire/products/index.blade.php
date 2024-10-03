@extends('layouts.app')
@section('content')
@php
 use App\Models\User;
 use App\Models\Product;
 use App\Models\Category;
@endphp
<!-----------------------------VISTA ADMINSITRADOR --------------------------------- -->
@if(Auth::User()->role=='Administrador')
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
                            <th scope="col">Nombre Producto</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Ver Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $cont=1;
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
                                        data-product-description="{{ $product->description }}"
                                        data-product-quantity="{{ $product->quantity }}"
                                        data-product-stock="{{ $product->stock }}" 
                                        data-product-unitprice="{{ $product->unitPrice }}" 
                                        data-product-measurementUnit="{{ $product->measurementUnit }}" 
                                        data-product-categoryid="{{ optional(Category::find($product->categoryId))->name }}">
                                            <i class="fas fa-info-circle"></i>
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
                <h5 class="modal-title" id="toggleStatusModalLabel">DETALLE DE PRODUCTO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover align-middle text-center">
                    <tr>
                        <th>Descripcion</th>
                        <th>Medida Ingresada</th>
                        <th>Stock (Kgs.)</th>
                        <th>Precio Unitario Bs.</th>
                        <th>Categoria</th>
                    </tr>
                    <tr>
                        <td><span id="modalDescription"></span></td>
                        <td><span id="modalMeasurementUnit"></span></td>
                        <td><span id="modalStock"></span></td>
                        <td><span id="modalUnitPrice"></span></td>
                        <td><span id="modalCategoryId"></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleStatusModal = document.getElementById('toggleStatusModal');

        toggleStatusModal.addEventListener('show.bs.modal', function (event) {
            // Botón que activó el modal
            var button = event.relatedTarget;

            // Extraer información de los atributos data-*
            var productDescription = button.getAttribute('data-product-description');
            var productStock = button.getAttribute('data-product-stock');
            var productUnitPrice = button.getAttribute('data-product-unitprice');
            var productCategoryId = button.getAttribute('data-product-categoryid');
            var productMeasurementUnit = button.getAttribute('data-product-measurementUnit');

            // Asignar los valores al modal
            document.getElementById('modalDescription').textContent = productDescription;
            document.getElementById('modalStock').textContent = productStock;
            document.getElementById('modalUnitPrice').textContent = productUnitPrice;
            document.getElementById('modalCategoryId').textContent = productCategoryId;
            document.getElementById('modalMeasurementUnit').textContent = productMeasurementUnit;
        });
    });
</script>
@endpush
@elseif(Auth::User()->role=='Productor')
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
                            <th scope="col">Nombre Producto</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Detalles</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $cont=1;
                        @endphp
                        @foreach ($products as $product)
                            @if(Auth::User()->id==$product->userId)
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>
                                    {{ optional(Product::find($product->id))->name }}
                                </td>
                                <td>{{ $product->description }}</td>
                                <td>   
                                    <button type="button" class="btn btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#toggleStatusModal" 
                                        data-product-description="{{ $product->description }}"
                                        data-product-quantity="{{ $product->quantity }}"
                                        data-product-stock="{{ $product->stock }}" 
                                        data-product-unitprice="{{ $product->unitPrice }}" 
                                        data-product-measurementUnit="{{ $product->measurementUnit }}" 
                                        data-product-categoryid="{{ optional(Category::find($product->categoryId))->name }}">
                                            <i class="fas fa-info-circle"></i>
                                    </button>
                                </td>
                                <td>
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#toggleStatusModal" 
                                        data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-description="{{ $product->description }}" 
                                        data-product-stock="{{ $product->stock }}" data-product-unitPrice="{{ $product->unitPrice }}" data-product-categoryId="{{ $product->categoryId }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#surtirProductModal" 
                                        data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-stock="{{ $product->stock }}">
                                        <i class="fas fa-plus-square"></i>
                                    </button>
                                </td>


                            </tr>
                            @php $cont++; @endphp
                            @endif
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

<!-- Modal de Surtir Producto -->
<div class="modal fade" id="surtirProductModal" tabindex="-1" aria-labelledby="surtirProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="surtirProductModalLabel">Surtir Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Producto: <strong id="surtirProductName"></strong></p>
                <p>Stock Actual: <strong id="currentStock"></strong> Kgs.</p>
                <form id="surtirProductForm" action="{{ route('products.surtir', $product->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="mb-3">
                        <label for="surtirQuantity" class="form-label">Cantidad a modificar</label>
                        <input type="number" class="form-control" id="surtirQuantity" name="surtirQuantity" min="-{{ $product->stock }}" value="0">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="toggleStatusModalLabel">DETALLE DE PRODUCTO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover align-middle text-center">
                    <tr>
                        <th>Descripcion</th>
                        <th>Medida Ingresada</th>
                        <th>Stock (Kgs.)</th>
                        <th>Precio Unitario Bs.</th>
                        <th>Categoria</th>
                    </tr>
                    <tr>
                        <td><span id="modalDescription"></span></td>
                        <td><span id="modalMeasurementUnit"></span></td>
                        <td><span id="modalStock"></span></td>
                        <td><span id="modalUnitPrice"></span></td>
                        <td><span id="modalCategoryId"></span></td>
                    </tr>
                </table>
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
    document.addEventListener('DOMContentLoaded', function () {
        var surtirProductModal = document.getElementById('surtirProductModal');

        surtirProductModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; 
            var productId = button.getAttribute('data-product-id'); 
            var productName = button.getAttribute('data-product-name'); 
            var productStock = parseInt(button.getAttribute('data-product-stock')); 

            // Cargar información en el modal
            document.getElementById('surtirProductName').textContent = productName;
            document.getElementById('currentStock').textContent = productStock;
            document.getElementById('surtirQuantity').value = 0; // Inicializar a 0

            // Agregar evento para el botón de confirmación
            document.getElementById('confirmSurtir').onclick = function() {
                var quantity = parseInt(document.getElementById('surtirQuantity').value);

                // Validar que se ingrese una cantidad
                if (isNaN(quantity)) {
                    alert('Por favor, ingrese una cantidad válida.');
                    return;
                }

                surtirProduct(productId, quantity); // Enviar la cantidad
            };
        });
    });

    function surtirProduct(productId, quantity) {
        fetch(`/products/${productId}/surtir`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recargar la página para ver los cambios
            } else {
                alert('Error al actualizar el stock: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var toggleStatusModal = document.getElementById('toggleStatusModal');

        toggleStatusModal.addEventListener('show.bs.modal', function (event) {
            // Botón que activó el modal
            var button = event.relatedTarget;

            // Extraer información de los atributos data-*
            var productDescription = button.getAttribute('data-product-description');
            var productStock = button.getAttribute('data-product-stock');
            var productUnitPrice = button.getAttribute('data-product-unitprice');
            var productCategoryId = button.getAttribute('data-product-categoryid');
            var productMeasurementUnit = button.getAttribute('data-product-measurementUnit');

            // Asignar los valores al modal
            document.getElementById('modalDescription').textContent = productDescription;
            document.getElementById('modalStock').textContent = productStock;
            document.getElementById('modalUnitPrice').textContent = productUnitPrice;
            document.getElementById('modalCategoryId').textContent = productCategoryId;
            document.getElementById('modalMeasurementUnit').textContent = productMeasurementUnit;
        });
    });
</script>

@endpush
@else
@endif
@endsection