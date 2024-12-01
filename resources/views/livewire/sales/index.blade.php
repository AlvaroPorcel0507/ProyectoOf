@extends('layouts.app')

@section('content')
@php
 use App\Models\User;
 use App\Models\Product;
 use App\Models\SaleDetail;
@endphp

@if(Auth::User()->role=='Cliente')
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Registro de Compras</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-success">Registrar Nueva Compra</a>
    </div>

    @if ($sales->isEmpty())
        <div class="alert alert-warning">No hay compras realizadas.</div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nro.</th>
                    <th>Cliente</th>
                    <th>Total Bs.</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php $cont = 1; @endphp

                @foreach ($sales as $sale)
                    @if ($sale->customerId == Auth::id()) <!-- Filtra por cliente autenticado -->
                        <tr>
                            <td>{{ $cont }}</td>
                            <td>{{ optional($sale->customer)->name.' '.optional($sale->customer)->lastName }}</td> <!-- Relación para obtener nombre del cliente -->
                            <td>{{ $sale->total }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td> <!-- Formatear fecha -->
                            <td>
                                <form action="{{ route('sales.show', $sale->id) }}" method="GET">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fa fa-info-circle"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @php $cont++; @endphp
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<!-- Modal para mostrar detalles de la venta -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1" role="dialog" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleDetailsModalLabel">Detalles de la Venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="saleDetailsBody">
                        <!-- Los detalles de la venta se llenarán aquí -->
                    </tbody>
                </table>
                <h5>Total de la Venta: <span id="totalSale"></span></h5>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
$(document).ready(function() {
    // Cuando se abre el modal
    $('#saleDetailsModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Botón que abrió el modal
        var saleId = button.data('sale-id');  // ID de la venta que viene del botón

        // Verifica que el ID de la venta está siendo pasado correctamente
        console.log('Sale ID:', saleId);

        // Realizar una solicitud AJAX para obtener los detalles de la venta
        $.ajax({
            url: '/sales/' + saleId,  // URL para obtener la venta
            type: 'GET',
            success: function(sale) {
                console.log('Datos recibidos del servidor:', sale);  // Verifica los datos recibidos

                var body = $('#saleDetailsBody');
                body.empty();  // Limpia el contenido del modal antes de agregar los detalles

                var totalSum = 0; // Variable para sumar los totales de los productos

                // Si hay detalles de venta
                if (sale.sale_details.length > 0) {
                    sale.sale_details.forEach(function(detail) {
                        var productTotal = detail.totalProduct;  // Obtiene el total por producto
                        totalSum += productTotal;  // Suma al total general

                        body.append(`
                            <tr>
                                <td>${detail.product.name}</td>
                                <td>${detail.quantity} kg</td>
                                <td>${detail.unitPrice.toFixed(2)}</td>
                                <td>${productTotal.toFixed(2)}</td>
                            </tr>
                        `);
                    });
                } else {
                    body.append('<tr><td colspan="4">No hay detalles disponibles para esta venta.</td></tr>');
                }

                // Mostrar la suma total de los productos
                $('#totalSale').text(totalSum.toFixed(2));
            },
            error: function() {
                // Si ocurre algún error, muestra un mensaje de error en el modal
                $('#saleDetailsBody').html('<tr><td colspan="4">No se pudieron cargar los detalles de la venta.</td></tr>');
                console.log('Error al cargar los detalles de la venta');
            }
        });
    });
});

</script>
@endpush
@else
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Registro de Ventas</h1>
    </div>

    <!-- Formulario para seleccionar rango de fechas -->
    <form method="GET" action="{{ route('sales.index') }}">
    <div class="row mb-4">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Fecha Inicio</label>
            <input type="date" id="start_date" name="start_date" class="form-control" 
                   value="{{ request('start_date', $startDate) }}" required>
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Fecha Fin</label>
            <input type="date" id="end_date" name="end_date" class="form-control" 
                   value="{{ request('end_date', $endDate) }}" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </div>
</form>


    @if ($saleDetails->isEmpty())
        <div class="alert alert-warning">No hay compras realizadas en el rango seleccionado.</div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nro.</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Costo Bs.</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @php $cont = 1; @endphp
                @foreach ($saleDetails as $detail)
                    <tr>
                        <td>{{ $cont }}</td>
                        <td>{{ optional($detail->product)->name }}</td> 
                        <td>{{ $detail->quantity.' '.$detail->description }}</td>
                        <td>{{ $detail->totalProduct }}</td>
                        <td>{{ optional($detail->sale)->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @php $cont++; @endphp
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('reports.generateforProducerPdf') }}" method="GET">
            <!-- Incluyendo las fechas seleccionadas -->
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <input type="hidden" name="producer_id" value="{{ Auth::id() }}">
            <button type="submit" class="btn btn-info">Generar PDF</button>
        </form>
    @endif
</div>

@endif

@endsection
