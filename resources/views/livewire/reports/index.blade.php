@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Reporte de Productos Más Vendidos</h1>

    <form id="filter-form" method="GET" action="{{ route('reports.index') }}">
        <div class="row mb-3">
            <div class="col-md-4">
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
            <div class="col-md-4">
                <label for="end_date" class="form-label">Fecha de Fin</label>
                <input 
                    type="date" 
                    id="end_date" 
                    name="end_date" 
                    class="form-control" 
                    value="{{ request('end_date', $endDate->toDateString()) }}"
                >
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Aplicar</button>
            </div>
        </div>
    </form>

    <p>
        <strong>Rango de fechas:</strong> 
        @if($startDate && $endDate)
            {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
        @elseif($startDate)
            Desde {{ $startDate->format('d/m/Y') }}
        @endif
    </p>

    @if($topProducts->isNotEmpty())
        <h2 class="mt-4">Resultados del Reporte</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad Vendida</th>
                    <th>Total Recaudado (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->total_sold }}</td>
                        <td>{{ number_format($product->total_revenue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('reports.generatePdf') }}">
            <button type="submit" class="btn btn-info">Generar PDF</button>
        </form>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesData = @json($topProducts);

            const labels = salesData.map(product => product.name);
            const quantities = salesData.map(product => product.total_sold);

            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de Productos Vendidos',
                        data: quantities,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Porcentaje de Productos Vendidos' }
                    }
                }
            });
        </script>
    @else
        <p>No se encontraron registros para el rango de fechas seleccionado.</p>
    @endif
</div>
@endsection
