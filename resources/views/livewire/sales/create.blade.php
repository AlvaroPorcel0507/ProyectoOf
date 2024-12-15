@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Venta</h1>

    <!-- Mensajes de éxito o error -->
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

    <!-- Filtros de Categoría y Producto -->
    <form method="GET" action="{{ route('sales.create') }}">
        <div class="row">
            <!-- Selección de categoría -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="categoryId">Selecciona Categoría:</label>
                    <select name="categoryId" id="categoryId" class="form-control" onchange="this.form.submit()">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('categoryId') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Selección de Producto -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="product_name">Selecciona Producto:</label>
                    <input list="product-list" id="product_name" class="form-control" placeholder="Selecciona un producto">
                    <datalist id="product-list">
                        @foreach($products as $product)
                            <option value="{{ $product->name }}" data-id="{{ $product->id }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </datalist>
                    <input type="hidden" id="productId" name="productId" value="{{ request('productId') }}">
                </div>
            </div>
        </div>
    </form>

    <!-- Tabla dinámica de inventarios -->
    @if (isset($inventory) && count($inventory) > 0)
        <div class="form-group mt-4">
            <h4>Inventarios del Producto Seleccionado</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Productor</th>
                        <th>Stock Disponible</th>
                        <th>Precio Por Kilo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td>{{ $item['producer_name'] ?? 'Desconocido' }}</td>
                            <td>{{ $item['stock'] }} Kg</td>
                            <td>{{ number_format($item['unitPrice'], 2) }} Bs</td>
                            <td>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#productModal" 
                                        data-product-name="{{ $item['product_name'] }}"
                                        data-producer-name="{{ $item['producer_name'] ?? 'Desconocido' }}"
                                        data-stock="{{ $item['stock'] }}"
                                        data-unit-price="{{ $item['unitPrice'] }}">
                                    Agregar al Carrito
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="mt-4">No se encontraron inventarios para el producto seleccionado.</p>
    @endif

     <!-- Modal para para la conversion antes de agregar al carrito-->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Seleccionar Unidad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Producto:</strong> <span id="modal-product-name"></span></p>
                    <p><strong>Productor:</strong> <span id="modal-producer-name"></span></p>
                    <p><strong>Stock Disponible (Kg):</strong> <span id="modal-stock-kg"></span></p>
                    <p><strong>Precio Por Kilo:</strong> <span id="modal-price-kg"></span></p>

                    <!-- Stock en la unidad seleccionada -->
                    <p><strong>Stock Disponible (Unidad Seleccionada):</strong> <span id="modal-stock"></span></p>
                    <p><strong>Precio Por Unidad Seleccionada:</strong> <span id="modal-price-unit"></span></p>

                    <!-- Selección de unidad -->
                    <div class="form-group">
                        <label for="unit">Seleccionar Unidad:</label>
                        <select class="form-control" id="unit">
                            <option value="kg">Kilogramos</option>
                            <option value="lb">Libras</option>
                            <option value="ar">Arroba</option>
                            <option value="cuartilla">Cuartilla</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Cantidad:</label>
                        <input type="number" class="form-control" id="quantity" min="0" step="any">
                    </div>

                    <p><strong>Precio Total:</strong> <span id="modal-total-price">0.00</span> Bs</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="add-to-cart-btn">Agregar al Carrito</button>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#cartModal">
        Ver Carrito
    </button>

    <!-- Modal para mostrar el contenido del carrito -->
    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Carrito de Compras</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se mostrarán los productos en el carrito -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Productor</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('cart', []) as $index => $item)
                                <tr> 
                                    <td>{{ $item['product_name'] }}</td>
                                    <td>{{ $item['producer_name'] }}</td>
                                    <td>{{ $item['quantity'] }} {{ $item['unit'] }}</td>
                                    <td>{{ number_format($item['unitPrice'], 2) }} Bs</td>
                                    <td>{{ number_format($item['totalPrice'], 2) }} Bs</td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove-item" data-index="{{ $index }}">Eliminar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="finalize-purchase">Finalizar Compra</button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    #cartModal .modal-dialog {
        max-width: 90%; /* Ajusta el ancho a un 90% de la pantalla */
        width: 90%; /* Aplica el ancho personalizado */
    }

    #cartModal .modal-body {
        max-height: 80vh; /* Limita la altura máxima al 80% del viewport */
        overflow-y: auto; /* Habilita el scroll si el contenido es demasiado largo */
    }

</style>
<script>
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
    // Llenar los campos del modal con la información del producto
    $('#productModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Botón que abrió el modal
        var productName = button.data('product-name');
        var producerName = button.data('producer-name');
        var stock = parseFloat(button.data('stock')); // Stock en Kg
        var unitPrice = parseFloat(button.data('unit-price')); // Precio por Kg

        // Actualiza los campos del modal
        $('#modal-product-name').text(productName);
        $('#modal-producer-name').text(producerName);
        $('#modal-stock-kg').text(stock + ' Kg');
        $('#modal-price-kg').text(unitPrice + ' Bs');

        // Inicializa el stock y el precio por unidad (en Kg por defecto)
        $('#modal-stock').text(stock + ' Kg');
        $('#modal-price-unit').text(unitPrice + ' Bs');

        // Establecer el valor máximo de la cantidad a seleccionar (en Kg)
        $('#quantity').attr('max', stock);

        // Conversión según la unidad seleccionada para el stock
        var stockConversionRates = {
            'kg': 1,
            'lb': 2.20,        // 1 kg = 2.20 lb
            'ar': 0.086,       // 1 arroba = 0.086 kg
            'cuartilla': 0.34, // 1 cuartilla = 0.34 kg
            'quintal': 0.05,  // 1 quintal = 0.05 kg
        };

        // Conversión según la unidad seleccionada para el precio
        var priceConversionRates = {
            'kg': 1,
            'lb': 0.45,        // 1 lb = 0.45 kg (por lo tanto, precio se multiplica por 0.45)
            'ar': 11.5,        // 1 arroba = 11.5 kg (por lo tanto, precio se multiplica por 11.5)
            'cuartilla': 2.875, // 1 cuartilla = 2.875 kg (por lo tanto, precio se multiplica por 2.875)
            'quintal': 50      // 1 quintal = 50 kg (por lo tanto, precio se multiplica por 50)
        };

        // Calcular y actualizar el precio y el stock cuando cambie la unidad o la cantidad
        $('#unit, #quantity').on('change input', function() {
            var unit = $('#unit').val();
            var quantity = parseFloat($('#quantity').val());
            
            // Convertir el stock según la unidad seleccionada
            var convertedStock = stock * stockConversionRates[unit];

            // Convertir el precio por unidad según la unidad seleccionada
            var unitPriceConverted = unitPrice * priceConversionRates[unit];

            // Actualizar el stock y el precio por unidad según la unidad seleccionada
            $('#modal-stock').text(convertedStock.toFixed(2) + ' ' + unit);
            $('#modal-price-unit').text(unitPriceConverted.toFixed(2) + ' Bs');

            // Actualizar el valor máximo del campo de cantidad según el stock convertido
            $('#quantity').attr('max', convertedStock.toFixed(2));

            // Si la cantidad excede el stock disponible, ajustarla automáticamente
            if (quantity > convertedStock) {
                $('#quantity').val(convertedStock.toFixed(2)); // Ajustar cantidad si excede el stock disponible
                Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'La cantidad no puede exceder el stock disponible para la unidad seleccionada.',
                        confirmButtonText: 'Aceptar'
                    });
            }

            // Calcular el precio total
            var totalPrice = quantity * unitPriceConverted;

            // Redondeo hacia arriba a dos decimales (ajustado a 0.10 Bs)
            totalPrice = Math.ceil(totalPrice * 10) / 10;

            // Mostrar el precio total en el modal
            $('#modal-total-price').text(totalPrice.toFixed(2) + ' Bs');
        });
    });


    // Funcionalidad del botón "Agregar al Carrito"
    $('#add-to-cart-btn').click(function() {
        var productName = $('#modal-product-name').text();
        var producerName = $('#modal-producer-name').text();
        var quantity = $('#quantity').val();
        var unit = $('#unit').val();
        var unitPrice = parseFloat($('#modal-price-unit').text().replace(' Bs', ''));
        var productId = $('#productId').val();

        if (quantity > 0) {
            // Realizar solicitud AJAX para agregar el producto al carrito
            $.ajax({
                url: '{{ route('cart.add') }}', // Ruta para agregar al carrito
                type: 'POST',
                data: {
                    product_name: productName,
                    producer_name: producerName,
                    quantity: quantity,
                    unit: unit,
                    unitPrice: unitPrice,
                    product_id: productId,  // Enviar el id del producto
                    _token: '{{ csrf_token() }}' // Asegúrate de incluir el token CSRF
                },
                success: function(response) {
                    // Mostrar mensaje de éxito y actualizar el carrito en la vista
                    Swal.fire({
                        icon: 'success',
                        title: '¡Producto agregado!',
                        text: 'El producto se ha añadido al carrito correctamente.',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Opcional: Redirigir después de cerrar el SweetAlert
                            window.location.href = '{{ route("sales.create") }}'; // Cambia "cart.view" por la ruta a la que quieras redirigir
                        }
                    });
                    // Aquí puedes actualizar la vista del carrito si lo deseas
                    console.log(response.cart);
                    // Actualizar el stock disponible en el modal (restar el stock)
                    var newStock = response.newStock;
                    $('#modal-stock').text(newStock + ' Kg');
                    $('#quantity').attr('max', newStock);

                },
                error: function(xhr, status, error) {
                    console.log("Error al agregar el producto al carrito:", error);

                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'No se pudo agregar el producto al carrito. Por favor, inténtalo nuevamente.',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Opcional: Redirigir después de cerrar el SweetAlert
                            window.location.href = '{{ route("sales.create") }}'; // Cambia "cart.view" por la ruta a la que quieras redirigir
                        }
                    });
                }
            });

            // Cerrar el modal
            $('#productModal').modal('hide');
        } else {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Ingrese una cantidad valida.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
    // Eliminar producto del carrito
$(document).on('click', '.remove-item', function() {
    var index = $(this).data('index'); // Obtener el índice del producto en el carrito

    // Realizar la solicitud AJAX para eliminar el producto del carrito
    $.ajax({
        url: '/remove-from-cart',  // Ruta para eliminar el producto
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: index  // Pasar el índice del carrito
        },
        success: function(response) {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'No se pudo Eliminar el Item.',
                confirmButtonText: 'Aceptar'
            });
        },
        error: function() {
            Swal.fire({
                icon: 'success',
                title: '¡Producto Eliminado!',
                text: 'El producto se ha elimina del carrito correctamente.',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                            // Opcional: Redirigir después de cerrar el SweetAlert
                    window.location.href = '{{ route("sales.create") }}'; // Cambia "cart.view" por la ruta a la que quieras redirigir
                }
            });
        }
    });
});
$(document).on('click', '#finalize-purchase', function () {
    $.ajax({
        url: '/finalize-purchase', // Ruta al método del controlador
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // Token CSRF
        },
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: '¡Compra Finalizada!',
                text: 'La compra se realizo correctamente.',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Opcional: Redirigir después de cerrar el SweetAlert
                    window.location.href = '{{ route("sales.index") }}'; // Cambia "cart.view" por la ruta a la que quieras redirigir
                }
            });
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'No se pudo realizar la compra.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
});
</script>

@endsection
