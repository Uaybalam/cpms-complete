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
        }
        .ticket {
            width: 65mm;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 8pt;
            padding: 0 3em;
            margin-left: 0; /* Alinea el ticket al lado izquierdo */
        }
        .ticket-header {
            text-align: left; /* Alinea el encabezado a la izquierda */
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

        .vehicle-info {
            display: flex;
            justify-content: flex-start; /* Alinea los elementos de vehículo al inicio */
        }

        .vehicle-image {
            background-image: url('http://dparking.com/codigo_qr.png');
            background-size: contain;
            background-repeat: no-repeat;
            width: 100px; /* Ajustar el tamaño según sea necesario */
            height: 100px;
            margin-right: 10px;
        }

        .vehicle-details {
            flex: 1;
        }

        .section {
            margin-bottom: 10px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .section-content {
            text-align: left; /* Alinea todo el contenido de las secciones a la izquierda */
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
                        <p>Total Cobrado: {{$Total}}</p>
                        <p>Cliente: {{$name}}S</p>
                        <p>Placas: {{$placa}}</p>
                        <p>Marca: {{$modelo}}</p>
                        <p>Color: {{$color}}</p>
                        <p>Placas: {{$placa2}}</p>
                        <p>Marca: {{$modelo2}}</p>
                        <p>Color: {{$color2}}</p>
                    </div>
                    <div class="vehicle-image"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
