@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Reporte General de Productos Vendidos</h1>
    

    <form id="filter-form" method="GET" action="{{ route('reports.saleProducer') }}">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="producer_id" class="form-label">Seleccione Productor</label>
                <select id="producer_id" name="producer_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($producers as $producer)
                        <option 
                            value="{{ $producer->id }}" 
                            {{ request('producer_id') == $producer->id ? 'selected' : '' }}
                        >
                            {{ $producer->name.' '.$producer->lastName.' '.$producer->secondLastName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">Fecha de Inicio</label>
                <input 
                    type="date" 
                    id="start_date" 
                    name="start_date" 
                    class="form-control" 
                    value="{{ request('start_date', $startDate->toDateString()) }}" 
                    required
                >
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Fecha de Fin</label>
                <input 
                    type="date" 
                    id="end_date" 
                    name="end_date" 
                    class="form-control" 
                    value="{{ request('end_date', $endDate->toDateString()) }}"
                >
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Aplicar</button>
            </div>
        </div>
    </form>

    <p>
        <strong>Rango de fechas:</strong> {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}<br>
        <strong>Productor seleccionado:</strong> 
        {{ $producers->firstWhere('id', request('producer_id'))?->name.' '.$producers->firstWhere('id', request('producer_id'))?->lastName.' '.$producers->firstWhere('id', request('producer_id'))?->secondLastName ?? 'Todos' }}
    </p>

    @if($productSales->isNotEmpty())
        <h2 class="mt-4">Resultados del Reporte</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad Vendida (Kgs.)</th>
                    <th>Ingresos Totales (Bs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productSales as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->total_sold }}</td>
                        <td>{{ number_format($product->total_revenue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('reports.generateSaleProducerPdf') }}" method="GET">
            <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
            <input type="hidden" name="end_date" value="{{ $endDate->toDateString() }}">
            <input type="hidden" name="producer_id" value="{{ request('producer_id') }}">
            <button type="submit" class="btn btn-info">Generar PDF</button>
        </form>
    @else
        <p>No se encontraron registros para el rango de fechas y productor seleccionado.</p>
    @endif
</div>
@endsection
