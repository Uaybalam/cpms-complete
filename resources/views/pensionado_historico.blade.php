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
            width: 65mm;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 8pt;
            margin: auto; /* Centra el ticket en la página */
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

        .vehicle-image {
            background-image: url('http://dparking.com/codigo_qr.png'); /* Ruta de la imagen del código QR en línea */
            background-size: contain; /* Ajusta el tamaño de la imagen para que se ajuste al contenedor */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            width: 200px; /* Ancho de la imagen */
            height: 200px; /* Altura de la imagen */
            margin-right: 10px; /* Espacio entre la imagen y los detalles del vehículo */
        }

        .vehicle-details {
            flex: 1;
            float: left;
        }

    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <h1><b>Ticket de Estacionamiento</b></h1>
        </div>
        <div class="section">
            <div class="section-title"><b>Datos del Vehiculo</b></div>
            <div class="section-content">
                <div class="vehicle-info">

                    <div class="vehicle-details">
                        <p>Folio: {{ $folio = date('Ymdhms').'Z'}}</p>
                        <p>Servicio: UN MES</p>
                        <p>Servicio: {{$name}}</p>
                        <p>Placas: {{$placa}}</p>

                        <p>Color: {{$color}}</p>
                        <p>Placas: {{$placa2}}</p>

                        <p>Color: {{$color2}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title"><b>Historico de pagos</b></div>
            <div class="section-content">
                <div class="vehicle-info">

                    <div class="vehicle-details">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Pago</th>
                                    <th>Nombre</th>
                                    <th>Monto</th>
                                    <th>Fecha de Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pensionados as $pensionado)
                                <tr>
                                    <td>{{ $pensionado->id }}</td>
                                    <td>{{ $pensionado->nombre }}</td>
                                    <td>{{ $pensionado->precio_fijo }}</td>
                                    <td>{{ $pensionado->ultimo_pago }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
