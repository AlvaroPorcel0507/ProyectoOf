<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Productos Más Vendidos</title>
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
        table th {
            background-color: #f2f2f2;
        }
        th, td {
            padding: 8px;
            text-align: center;
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
    <h1>Reporte de Productos Más Vendidos</h1>
    <p><strong>Rango de Fechas:</strong> {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    <table>
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
</div>
<div class="footer">
        <p>Reporte generado automáticamente por el sistema Agro-Amigo.</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
