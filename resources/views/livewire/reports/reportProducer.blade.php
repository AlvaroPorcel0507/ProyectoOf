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
            background-image: url('{{ asset('storage/images/logo.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 30%;
            opacity: 0.9;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
            text-transform: uppercase;
        }
        p {
            font-size: 14px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
        }
        td {
            padding: 10px;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
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

    <div class="footer">
        <p>Reporte generado automáticamente por el sistema Agro-Amigo.</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>

