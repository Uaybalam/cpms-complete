<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
        }
        .container {
            width: 300px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
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
            <h2>Cierre de Ventas</h2>
        </div>
        <div class="divider">
            <hr>
        </div>
        <div class="details">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cajero</th>
                        <th>Total</th>
                        <th>Cantidad Inicial</th>
                        <th>Retiro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $detalle)
                    <tr>
                        <td>{{ $detalle['id'] }}</td>
                        <td>{{ $detalle['Cajero'] }}</td>
                        <td>{{ $detalle['Total'] }}</td>
                        <td>{{ $detalle['cantidad'] }}</td>
                        <td>{{ $detalle['Retiro'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Total: ${{ $total }}</p>
        </div>
        <div class="divider">
            <hr>
        </div>
        <div class="footer">
            <p>Excelente Turno</p>
        </div>
    </div>
</body>
</html>
