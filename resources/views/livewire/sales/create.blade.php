@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Venta</h1>

    <!-- Selector de categoría -->
    <div class="row">
        <div class="col-lg-4">
            <div class="form-group">
                <label for="categoryId">Selecciona Categoría:</label>
                <select name="categoryId" id="categoryId" class="form-control" onchange="loadProductsByCategory(this.value)">
                    <option value="">Seleccione una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="productId">Selecciona Producto:</label>
                <select name="productId" id="productId" class="form-control" required>
                    <option value="">Seleccione un producto</option>
                </select>
            </div>
        </div>
        <div class="col-lg-4"><br>
            <button type="button" class="btn btn-primary" onclick="loadStockTable()">Cargar Productos en Tabla</button>
        </div>
    </div>

    <!-- Tabla para mostrar el stock de cada productor -->
    <div class="form-group mt-3">
        <label>Stock por Productor:</label>
        <table class="table table-bordered mt-2" id="stockTable">
            <thead>
                <tr>
                    <th>Nro.</th>
                    <th>Productor</th>
                    <th>Precio Unitario Bs.</th>
                    <th>Stock Disponible</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="stockTableBody">
                <!-- Los datos de stock se cargarán aquí dinámicamente -->
            </tbody>
        </table>
    </div>
</div>

<script>
// Almacena todos los productos en un objeto
const productsByCategory = @json($products->groupBy('categoryId'));

// Función para cargar productos según la categoría seleccionada
function loadProductsByCategory(categoryId) {
    const productSelect = document.getElementById('productId');
    productSelect.innerHTML = '<option value="">Seleccione un producto</option>'; // Reiniciar opciones

    // Verifica si existen productos en la categoría seleccionada
    if (categoryId && productsByCategory[categoryId]) {
        productsByCategory[categoryId].forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.name;
            productSelect.appendChild(option);
        });
    } else {
        console.warn("No hay productos para la categoría seleccionada.");
    }
}

// Función para cargar el stock del producto seleccionado en la tabla
function loadStockTable() {
    const productId = document.getElementById('productId').value;
    const stockTableBody = document.getElementById('stockTableBody');
    stockTableBody.innerHTML = ''; // Limpiar tabla antes de cargar nuevos datos

    if (productId) {
        fetch(`/sales/stock/${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message); // Mostrar mensaje si no hay datos encontrados
                } else {
                    data.forEach((stock, index) => {
                        const row = document.createElement('tr');

                        const nroCell = document.createElement('td');
                        nroCell.textContent = index + 1;
                        row.appendChild(nroCell);

                        const producerCell = document.createElement('td');
                        producerCell.textContent = stock.producer_name || 'N/A';
                        row.appendChild(producerCell);

                        const priceCell = document.createElement('td');
                        priceCell.textContent = stock.unit_price || 'N/A';
                        row.appendChild(priceCell);

                        const stockCell = document.createElement('td');
                        stockCell.textContent = stock.stock || 'N/A';
                        row.appendChild(stockCell);

                        const actionCell = document.createElement('td');
                        const buyButton = document.createElement('button');
                        buyButton.className = 'btn btn-success';
                        buyButton.textContent = 'Comprar';
                        buyButton.onclick = () => alert(`Compra registrada para ${stock.producer_name}`);
                        actionCell.appendChild(buyButton);
                        row.appendChild(actionCell);

                        stockTableBody.appendChild(row);
                    });
                }
            })
            .catch(error => console.error('Error al cargar stock:', error));
    } else {
        alert("Seleccione un producto primero.");
    }
}
</script>
@endsection
