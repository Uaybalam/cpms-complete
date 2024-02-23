<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .ticket {
            width: 400px;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .ticket-header h1 {
            font-size: 20px;
            margin: 0;
            padding: 0;
        }
        .ticket-body {
            padding: 10px;
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
                <p>Folio:{{$folio}}</p>
                <p>Placas:{{$platNumber}}</p>
                <p>Marca: {{$modelo}}</p>
                <p>Color: {{$Color}}</p>
            </div>
        </div>
        <div class="section">
            <div class="section-title"><b>Hora de Entrada y Salida</b></div>
            <div class="section-content">
                <p>Hora de Entrada: {{$fechaActual}}</p>
            </div>
        </div>
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
                <p style="text-align: justify;">El estacionamiento o pension se obliga a prestar el servicio en los terminos en materia haciendonos responsables por robo total no respondemos por robos parciales.
                Favor de revisar en su tiquet que la fecha estimada de arribo, sea la misma que usted dio, ya que de esto depende que su vehiculo este lavado a su regreso.
                RFC de quien expide comprobante: RUAM850614UG4
                <b>NO NOS HACEMOS RESPONSABLES POR DA&Ntilde;OS PARCIALES O TOTALES A SU VEHICULO.</b>
                <br>
                <br>
                <br>
                Costo por boleto extraviado: $50</p>


            </div>
        </div>
    </div>
</body>
</html>
