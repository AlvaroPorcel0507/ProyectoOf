<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Detallado de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 0 auto;
        }
        h1, h3 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-weight: bold;
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
    <div class="container">
        <h1>Reporte Detallado de Ventas</h1>
        <h3>Del {{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}</h3>

        <table>
            <thead>
                <tr>
                    <th>Nro.</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario (Bs.)</th>
                    <th>Total por Producto (Bs.)</th>
                    <th>Fecha de Venta</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @foreach ($detailedSales as $sale)
                    <tr>
                        <td>{{ $counter }}</td>
                        <td>{{ $sale->product_name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ number_format($sale->unitPrice, 2) }}</td>
                        <td>{{ number_format($sale->total_price, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @php $counter++; @endphp
                @endforeach
            </tbody>
        </table>

        <p class="total">Total General: {{ number_format($totalRevenue, 2) }} Bs.</p>
    </div>

    <div class="footer">
        <p>Reporte generado automáticamente por el sistema Agro-Amigo.</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
