<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Compra</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
            font-weight: bold;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: #fff;
            text-transform: uppercase;
            font-size: 14px;
        }
        td {
            font-size: 14px;
        }
        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <h1>Recibo de Compra - Venta {{ $saleDetails->first()->salesId }}</h1>

    <p><strong>Cliente:</strong> {{ Auth::User()->name.' '.Auth::User()->lastName.' '.Auth::User()->secondLastName }}</p>
    <p><strong>Fecha de la compra:</strong> {{ \Carbon\Carbon::parse($saleDetails->first()->sale_date)->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Nro.</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $cont = 1; @endphp
            @foreach ($saleDetails as $detail)
                <tr>
                    <td>{{ $cont }}</td>
                    <td>{{ $detail->product_name ?? 'Producto no encontrado' }}</td>
                    <td>{{ $detail->quantity }} {{ $detail->description }}</td>
                    <td>{{ number_format($detail->unitPrice, 2) }} Bs</td>
                    <td>{{ number_format($detail->totalProduct, 2) }} Bs</td>
                </tr>
                @php $cont++; @endphp
            @endforeach
        </tbody>
    </table>

    <p class="total">Total de la Compra: {{ number_format($totalSale, 2) }} Bs</p>

    <div class="footer">
        <p>Reporte generado automáticamente por el sistema Agro-Amigo.</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
