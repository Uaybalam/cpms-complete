<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .container {
            width: 60mm;
            margin: 5mm 0;
            border: 1px solid #ccc;
            padding: 0 3em;
            border-radius: 5px;
            font-size: 8pt;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
        }
        .divider {
            margin-top: 5px;
            margin-bottom: 5px;
            text-align: center;
        }
        .divider hr {
            border: 0;
            border-top: 1px solid #ccc;
        }
        .details {
            margin-bottom: 10px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            border: 1px solid #ccc;
            padding: 5px;
        }
        .details th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>TICKET DE VENTA</h2>
        </div>
        <div class="divider">
            <hr>
        </div>
        <div class="details">
            <p>Cliente: {{ $cliente }}</p>
            <p>Folio: {{ $folio }}</p>
            <table>
                <thead>
                    <tr>
                        <th>Dias</th>
                        <th>Fecha</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $detalle)
                    <tr>
                        <td>{{ $detalle['dias'] }}</td>
                        <td>{{ $detalle['fecha'] }}</td>
                        <td>{{ $detalle['descuento'] }}</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Total: ${{ $total }}</p>
            <p>Cantidad: ${{ $cantidad }}.00</p>
            <p>Cambio: ${{ $cambio }}</p>
        </div>
        <div class="divider">
            <hr>
        </div>
        <div class="footer">
            <p>Gracias por su compra!</p>
        </div>
    </div>
</body>
</html>
