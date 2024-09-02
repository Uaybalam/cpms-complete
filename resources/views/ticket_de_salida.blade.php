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
            width: 65mm;
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
        <h1>ONEPARK</h1>
            <h2>TICKET DE VENTA</h2>
        </div>
        <div class="divider">
            <hr>
        </div>
        <div class="details">
            <p>Cliente: {{ $cliente }}</p>
            <p>Folio: {{ $folio }}</p>
            <p>Placa: {{ $inputPlaca }}</p>

            <table>
                <thead>
                    <tr>

                        <th>Fecha</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $detalle)
                    <tr>
                        <td>{{ $detalle['fecha'] }}</td>
                        <td>{{ $detalle['subtotal'] }}</td>

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
        @if ($visitas === 5 && $category_id !== 13)
        <div class="section">
            <div class="section-title"><b>No. de Visitas: 5</b></div>
            <div class="section-content">
                <p>Esta es tu visita numero 5.Solicita tu obsequio por esta visita</p>
            </div>
        </div>
    @elseif ($visitas === 10 && $category_id !== 13)
        <div class="section">
            <div class="section-title"><b>No. de Visitas: 10</b></div>
            <div class="section-content">
                <p>Esta es tu visita numero 10.Tu estadia sera gratis</p>
            </div>
        </div>
    @endif
        <div class="section">
            <div class="section-title"><b>Ubicacion</b></div>
            <div class="section-content">
                <p>ONE PARK GDL</p>
                <p>Av. Solidaridad Iberoamericana No 7822</p>
                <p>Telefonos: 3323928579 / 3335989730</p>
            </div>
        </div>
        <div class="section">
            <div class="section-title"><b>Responsabilidades</b></div>
            <div class="section-content">
                <p style="text-align: justify;">El estacionamiento o pension se obliga a prestar el servicio en los terminos en materia haciendonos responsables por robo totaL.
                Favor de revisar en su ticket que la fecha estimada de arribo, sea la misma que usted dio, ya que de esto depende que su vehiculo este lavado a su regreso.
                RFC de quien expide comprobante: RUAM850614UG4
                <b>NO NOS HACEMOS RESPONSABLES POR ROBOS PARCIALES NI DA&Ntilde;OS PARCIALES O TOTALES A SU VEHICULO.</b>
                <br>
                <br>
                <br>
                Costo por boleto extraviado: $150</p>
            </div>
        </div>
                <!-- {{-- <div class="section">
            <div class="section-title"><b>Ubicacion</b></div>
            <div class="section-content">
                <p>EASY PARK GDL</p>
                <p>Av. Solidaridad Iberoamericana No 7822</p>
                <p>Telefonos: 3334579196 / 3337430001</p>
            </div>
        </div>
        <div class="section">
            <div class="section-title"><b>Responsabilidades</b></div>
            <div class="section-content">
                <p style="text-align: justify;">El estacionamiento o pension se obliga a prestar el servicio en los terminos en materia haciendonos responsables por robo totaL.
                Favor de revisar en su ticket que la fecha estimada de arribo, sea la misma que usted dio, ya que de esto depende que su vehiculo este lavado a su regreso.
                RFC de quien expide comprobante: MAMJ861217S70
                <b>NO NOS HACEMOS RESPONSABLES POR ROBOS PARCIALES NI DA&Ntilde;OS PARCIALES O TOTALES A SU VEHICULO.</b>
                <br>
                <br>
                <br>
                Costo por boleto extraviado: $150</p>
            </div>
        </div> --}} -->
    </div>
</body>
</html>
