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
            /* Eliminamos el display flex para evitar centrado */
        }
        .container {
            width: 65mm;
            margin: 5mm 0;
            border: 1px solid #ccc;
            padding: 0 3em;
            border-radius: 5px;
            font-size: 8pt;
            /* Aseguramos que esté alineado a la izquierda */
            margin-left: 0;
        }
        .header {
            text-align: left; /* Alinea el encabezado a la izquierda */
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
            text-align: left; /* Alinea la división a la izquierda */
        }
        .divider hr {
            border: 0;
            border-top: 1px solid #ccc;
        }
        .details {
            margin-bottom: 10px;
            text-align: left; /* Aseguramos que el texto esté a la izquierda */
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
            text-align: left; /* Alinea todo el contenido de la tabla a la izquierda */
        }
        .details th, .details td {
            border: 1px solid #ccc;
            padding: 5px;
        }
        .details th {
            background-color: #f2f2f2;
            text-align: left; /* Alinea las celdas de la tabla a la izquierda */
        }
        .footer {
            margin-top: 10px;
            text-align: left; /* Alinea el pie de página a la izquierda */
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="header">
          <h1><b>ONEPARK</b></h1> 
            <h2>Cierre de Ventas</h2>
        </div>
        <div class="divider">
            <hr>
        </div>
        <div class="details">
            @php
            $ultimoCajero = null;
            $fechaEntrada = null;
            $fechaSalida = null;
            $turno = null;
        @endphp

        @if (count($detalles) > 0)
            @php
                $ultimoCajero = $detalles[1]['Cajero'];
                $fechaEntrada = $detalles[0]['Fecha'];
                $fechaSalida = end($detalles)['Fecha'];

                // Determinar el turno según la hora de entrada
                $horaEntrada = \Carbon\Carbon::parse($fechaEntrada)->format('H');
                if ($horaEntrada >= 7 && $horaEntrada < 19) {
                    $turno = 'Matutino';
                } else {
                    $turno = 'Nocturno';
                }
            @endphp

            <p>Cajero : {{ $ultimoCajero }}</p>
            <p>Hora de Entrada : {{ \Carbon\Carbon::parse($fechaEntrada)->format('d-m-Y H:i:s') }}</p>
            <p>Hora de Salida : {{ \Carbon\Carbon::parse($fechaSalida)->format('d-m-Y H:i:s') }}</p>
            <p>Turno : {{ $turno }}</p>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Total</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalles as $detalle)
                    @if ($detalle['Total']) <!-- Condición para omitir los totales que estén en 0 -->
                    <tr>
                        <td>{{ $detalle['Placa'] }}</td>
                        <td>{{ $detalle['Total'] }}</td>
                        <td>{{ $detalle['Estatus'] }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>


            <p>Total: ${{ $total }}</p>
        </div>
        <br>
        <br>
        <br>
        <br>
        <div class="divider">
            <hr>
        </div>
        <div class="footer">
            <p>Firma del Cajero</p>
            <p>Excelente Turno</p>
        </div>
    </div>
</body>
</html>
