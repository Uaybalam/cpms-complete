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
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .ticket {
            width: 60mm; /*Ajusta tamaño del papel puedes probar con 80mm, pero depende de la impresora */
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
            width: 50%; /* Ancho del contenedor de detalles del vehículo */
            float: left; /* Hace que los detalles del vehículo floten a la izquierda */
        }
        /* @media print {
            body {
                margin: 0;
            }
            .ticket {
                margin: 5mm 0;  Ajusta según sea necesario 
            }
        } 
        */

    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <h1><b>Vehiculos estacionados</b></h1>
        </div>
        <div class="section">
            <div class="section-title"><b>Datos del Vehiculo</b></div>
            <div class="section-content">
                <div class="vehicle-info">

                    <div class="vehicle-details">
                        <p>Folio: {{$folio}}</p>
                        <p>Placas: {{$platNumber}}</p>
                        <p>Marca: {{$modelo}}</p>
                        <p>Color: {{$Color}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title"><b>Hora de Entrada y Salida</b></div>
            <div class="section-content">
                <p>Entrada: {{$fechaActual}}</p>
                <p>Salida: {{$fechaSalida}}</p>
            </div>
        </div>
        </div>
    </div>
</body>
</html>