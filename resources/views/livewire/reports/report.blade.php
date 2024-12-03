<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Productos Más Vendidos</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            width: 90%;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #388e3c;
            font-size: 1.8em;
            margin-bottom: 10px;
        }
        p {
            text-align: center;
            font-size: 1em;
            color: #555;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        table th {
            background-color: #388e3c;
            color: #fff;
            font-weight: bold;
            padding: 10px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            font-size: 0.95em;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e8f5e9;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85em;
            color: #777;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Reporte de Productos Más Vendidos</h1>
    <p><strong>Fecha de Inicio:</strong> {{ $startDate->format('d/m/Y') }}</p>
    <p><strong>Fecha de Fin:</strong> {{ $endDate->format('d/m/Y') }}</p>
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
