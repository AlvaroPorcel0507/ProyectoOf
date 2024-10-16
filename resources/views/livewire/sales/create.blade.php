@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Venta</h1>

    <!-- Carrito de compras -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-shopping-cart"></i> Carrito de Compras
        </div>
        <div class="card-body">
            <table class="table table-striped" id="cartTable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se agregarán los productos al carrito -->
                </tbody>
            </table>
            <p><strong>Total del carrito: $<span id="cartTotal">0.00</span></strong></p>
        </div>
    </div>

    <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
        @csrf

        <input type="hidden" name="customerId" value="{{ auth()->user()->id }}"> <!-- Customer ID del usuario autenticado -->

        <div class="form-group">
            <label for="userId">Selecciona Productor:</label>
            <select name="userId" id="userId" class="form-control" required>
                <option value="">-- Seleccionar Productor --</option>
                @foreach($producers as $producer)
                    <option value="{{ $producer->id }}">{{ $producer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-4">
            <h4>Productos del Productor Seleccionado</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio Unitario</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="productsTable">
                    <!-- Los productos se cargarán aquí -->
                </tbody>
            </table>
        </div>

        <input type="hidden" name="cart" id="cartData" value=""> <!-- Para guardar datos del carrito como JSON -->

        <button type="submit" class="btn btn-success mt-4">Crear Venta</button>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Detalles del Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <span id="modalProductName"></span></p>
                <p><strong>Descripción:</strong> <span id="modalProductDescription"></span></p>
                <p><strong>Precio Unitario:</strong> $<span id="modalProductPrice"></span></p>
                <p><strong>Stock disponible:</strong> <span id="modalProductStock"></span></p>
                <div class="form-group">
                    <label for="productQuantity">Cantidad:</label>
                    <input type="number" id="productQuantity" class="form-control" min="1" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="addToCartButton">Agregar al Carrito</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    document.addEventListener('DOMContentLoaded', function () {
        const userIdSelect = document.getElementById('userId');
        const productsTable = document.getElementById('productsTable');
        const cartTable = document.getElementById('cartTable').querySelector('tbody');
        const cartTotal = document.getElementById('cartTotal');
        let selectedProduct = {};

        // Actualizar el total del carrito
        function updateCartTotal() {
            let total = 0;
            cart.forEach(item => {
                total += item.quantity * item.price;
            });
            cartTotal.textContent = total.toFixed(2);
        }

        // Actualizar la tabla del carrito
        function updateCartTable() {
            cartTable.innerHTML = '';
            cart.forEach((item, index) => {
                cartTable.innerHTML += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.quantity}</td>
                        <td>$${item.price.toFixed(2)}</td>
                        <td>$${(item.quantity * item.price).toFixed(2)}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            updateCartTotal();

            // Convertir el carrito a JSON y guardarlo en el campo oculto
            document.getElementById('cartData').value = JSON.stringify(cart);
        }

        // Eliminar producto del carrito
        window.removeFromCart = function(index) {
            cart.splice(index, 1);
            updateCartTable();
        }

        userIdSelect.addEventListener('change', function () {
            const userId = this.value;

            if (userId) {
                // Realizar solicitud AJAX para obtener los productos
                fetch(`/sales/get-products/${userId}`)
                    .then(response => response.json())
                    .then(products => {
                        productsTable.innerHTML = ''; // Limpiar la tabla antes de agregar los productos

                        if (products.length > 0) {
                            products.forEach(product => {
                                let actionContent;
                                if (product.stock === 0) {
                                    actionContent = '<span class="text-danger">Sin unidades disponibles</span>';
                                } else {
                                    actionContent = ` 
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#productModal" 
                                            data-product-id="${product.id}" 
                                            data-product-name="${product.name}" 
                                            data-product-description="${product.description}" 
                                            data-product-price="${product.unitPrice}" 
                                            data-product-stock="${product.stock}">
                                            <i class="fas fa-shopping-cart"></i> Agregar al carrito
                                        </button>
                                    `;
                                }

                                productsTable.innerHTML += `
                                    <tr>
                                        <td>${product.name}</td>
                                        <td>${product.description}</td>
                                        <td>${product.unitPrice.toFixed(2)}</td>
                                        <td>${product.stock}</td>
                                        <td>${actionContent}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            productsTable.innerHTML = '<tr><td colspan="5">No hay productos disponibles para este productor.</td></tr>';
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar los productos:', error);
                    });
            } else {
                productsTable.innerHTML = '<tr><td colspan="5">Selecciona un productor para ver sus productos.</td></tr>';
            }
        });

        // Cargar datos del producto en el modal
        $('#productModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Botón que activó el modal
            selectedProduct = {
                id: button.data('product-id'),
                name: button.data('product-name'),
                description: button.data('product-description'),
                price: button.data('product-price'),
                stock: button.data('product-stock')
            };
            const modal = $(this);
            modal.find('#modalProductName').text(selectedProduct.name);
            modal.find('#modalProductDescription').text(selectedProduct.description);
            modal.find('#modalProductPrice').text(selectedProduct.price.toFixed(2));
            modal.find('#modalProductStock').text(selectedProduct.stock);
        });

        // Agregar producto al carrito
        document.getElementById('addToCartButton').addEventListener('click', function () {
            const quantity = parseInt(document.getElementById('productQuantity').value);
            if (quantity > 0 && quantity <= selectedProduct.stock) {
                cart.push({
                    id: selectedProduct.id,
                    name: selectedProduct.name,
                    quantity: quantity,
                    price: selectedProduct.price
                });
                updateCartTable();
                $('#productModal').modal('hide'); // Cerrar el modal
            } else {
                alert('Cantidad no válida');
            }
        });
    });
</script>

<!-- Incluir FontAwesome para íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

@endsection
