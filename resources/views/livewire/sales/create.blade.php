@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Venta</h1>

    <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
        @csrf

        <!-- Selector de producto con filtro -->
        <div class="form-group">
            <label for="productSearch">Selecciona Producto:</label>
            <input type="text" id="productSearch" class="form-control" placeholder="Buscar producto..." onkeyup="filterProducts()">
            <ul id="productList" class="list-group mt-2" style="display:none;">
                @foreach($products as $product)
                    <li class="list-group-item" data-id="{{ $product->id }}" onclick="selectProduct('{{ $product->id }}', '{{ $product->name }}')">
                        {{ $product->name }}
                    </li>
                @endforeach
            </ul>
            <input type="hidden" name="productId" id="productId" required>
        </div>

        <!-- Tabla para mostrar los productores -->
        <div class="form-group mt-3">
            <label>Productores Disponibles:</label>
            <table class="table table-bordered mt-2" id="producersTable" style="display: none;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody id="producersTableBody">
                    <!-- Los productores se cargarán aquí dinámicamente -->
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-success mt-4">Crear Venta</button>
    </form>
</div>

<script>
    function filterProducts() {
        const input = document.getElementById('productSearch');
        const filter = input.value.toLowerCase();
        const productList = document.getElementById('productList');
        const items = productList.getElementsByTagName('li');

        let hasVisibleItems = false;

        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            const textValue = item.textContent || item.innerText;

            if (textValue.toLowerCase().indexOf(filter) > -1) {
                item.style.display = '';
                hasVisibleItems = true;
            } else {
                item.style.display = 'none';
            }
        }

        productList.style.display = hasVisibleItems ? 'block' : 'none';
    }

    function selectProduct(id, name) {
        document.getElementById('productSearch').value = name;
        document.getElementById('productId').value = id;
        document.getElementById('productList').style.display = 'none';

        loadProducers(id);
    }

    function loadProducers(productId) {
        fetch(`/sales/producers/${productId}`)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('producersTableBody');
                tableBody.innerHTML = ''; // Limpiar tabla antes de cargar nuevos datos

                data.forEach(producer => {
                    const row = document.createElement('tr');

                    const idCell = document.createElement('td');
                    idCell.textContent = producer.id;
                    row.appendChild(idCell);

                    const nameCell = document.createElement('td');
                    nameCell.textContent = producer.name;
                    row.appendChild(nameCell);

                    tableBody.appendChild(row);
                });

                // Mostrar la tabla si hay productores, ocultarla si no
                document.getElementById('producersTable').style.display = data.length ? 'table' : 'none';
            })
            .catch(error => console.error('Error al cargar productores:', error));
    }
</script>
@endsection
