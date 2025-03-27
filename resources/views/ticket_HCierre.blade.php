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
        <h1>OnePark - Historico de Cierre de Ventas</h1>
        @php
        $sumaTotal = 0; // Variable para acumular el total
        $cajero = 'Desconocido';

        // Verificar si existen registros en $cierres y omitir ciertos valores de cajero
        if ($cierres->isNotEmpty()) {
            foreach ($cierres as $cierre) {
                // Verificar si el valor de cajero no es "Cierre" o "Fondo"
                if (!in_array($cierre->Cajero, ['Cierre', 'Fondo', 'Retiro'])) {
                    $cajero = $cierre->Cajero;
                    break; // Salir del bucle una vez encontrado el primer cajero válido
                }
            }
        }

        // Determinar el turno en función de la hora de created_at del primer registro
        $turno = 'Desconocido';
        if ($cierres->isNotEmpty()) {
            $horaEntrada = \Carbon\Carbon::parse($cierres->first()->created_at)->format('H');
            if ($horaEntrada >= 7 && $horaEntrada < 19) {
                $turno = 'Matutino';
            } else {
                $turno = 'Vespertino';
            }
        }
    @endphp

    <!-- Mostrar el nombre del cajero y el turno arriba de la tabla -->
    @if($cierres->isNotEmpty())
        <h3>Cajero: {{ $cajero }}</h3>
        <h4>Turno: {{ $turno }}</h4>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Placa</th>
                <th>Total</th>
                <th>Estatus</th>
                <th>Fecha de Cierre</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cierres as $cierre)
                <tr>
                    <td>{{ $cierre->id }}</td>
                    <td>{{ $cierre->Placa }}</td>
                    <td>{{ $cierre->Total }}</td>
                    <td>{{ $cierre->Estatus }}</td>
                    <td>{{ $cierre->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @php
                    // Verificar que $cierre->Total sea numérico antes de sumarlo
                    $sumaTotal += is_numeric($cierre->Total) ? $cierre->Total : 0;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <br>
            <br>
            <tr>
                <td colspan="2"><strong>Total General</strong></td>
                <td><strong>{{ $sumaTotal }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>


    </div>
</body>
</html>
