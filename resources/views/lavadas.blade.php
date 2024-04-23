<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Compra</title>
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
        .ticket {
            width: 65mm; /*Ajusta tamaño del papel puedes probar con 80mm, pero depende de la impresora */
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 8pt;
            margin: 5mm 0;
            padding: 0 3em;

        }
        .ticket-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .ticket-header h1 {
            font-size: 20px;
            margin: 0;
        }

        .ticket-item {
            margin-bottom: 5px;
        }
        .ticket-item span {
            display: inline-block;
            width: 50%;
        }
        .ticket-total {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
        }
        /* Agrega este CSS a tu estilo */
        .vehicle-info {
            display: flex;
        }
        .vehicle-details {
            flex: 1; /* Los detalles del vehículo ocupan todo el espacio restante */
            float: left; /* Hace que los detalles del vehículo floten a la izquierda */
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
            <p>Folio: {{ $folio }}</p>
            <table>
                <thead>
                    <tr>
                        <th>cliente</th>
                        <th>placa</th>
                        <th>modelo</th>
                        <th>entrada</th>
                        <th>salida</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $detalle)
                    <tr>
                        <td>{{ $detalle['cliente'] }}</td>
                        <td>{{ $detalle['placa'] }}</td>
                        <td>{{ $detalle['modelo'] }}</td>
                        <td>{{ $detalle['entrada'] }}</td>
                        <td>{{ $detalle['salida'] }}</td>


                    </tr>
                    @endforeach
                </tbody>
            </table>
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
