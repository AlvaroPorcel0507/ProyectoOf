<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Productos Vendidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Reporte General de Productos Vendidos</h1>
    <p><strong>Rango de Fechas:</strong> {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    @if($producerName)
        <p><strong>Productor:</strong> {{ $producerName }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad Vendida (Kg)</th>
                <th>Monto Total (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @if($productSales->isNotEmpty())
                @foreach($productSales as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->total_sold }}</td>
                        <td>{{ number_format($product->total_revenue, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3">No se encontraron registros para el rango de fechas seleccionado.</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($productSales->isNotEmpty())
        <p class="total">Monto Total Vendido: {{ number_format($totalRevenue, 2, '.', ',') }} Bs</p>
    @endif
</body>
</html>
