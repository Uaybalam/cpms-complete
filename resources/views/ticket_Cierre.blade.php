<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Ventas</title>
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        .container {
            width: 75mm;
            /* margin: 5mm 0; */
            border: 1px solid #ccc;
            padding: 0 3em;
            border-radius: 5px;
            font-size: 6pt;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 10pt;
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
            table-layout: fixed;
        }
        .details th, .details td {
            border: 1px solid #ccc;
            padding: 2px 5px; /* Reduce el padding si es necesario */
            overflow: hidden; /* Agrega esto para evitar el desbordamiento del texto */
            text-overflow: ellipsis; /* Agrega esto para poner puntos suspensivos si el texto es muy largo */
            white-space: nowrap;
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
                        <th>Placa</th>
                        <th>Total</th>
                        <th>Cantidad</th>
                        <th>Retiro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $detalle)
                    <tr>
                        <td>{{ $detalle['id'] }}</td>
                        <td>{{ $detalle['Cajero'] }}</td>
                        <td>{{ $detalle['Placa'] }}</td>
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
