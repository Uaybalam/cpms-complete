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
        <h1><b>ONEPARK</b></h1>
            <h1><b>Ticket de Estacionamiento</b></h1>
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
                    <div class="vehicle-image"></div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title"><b>Hora de Entrada y Salida</b></div>
            <div class="section-content">
                <p>Hora de Entrada: {{$fechaActual}}</p>
                <p>Hora de Salida: {{$fechaSalida}}</p>
            </div>
        </div>

        @if ($vigencia === 1)
        <div class="section">
            <div class="section-title"><b>Pension Vigente</b></div>
            <div class="section-content">
                <p>Le informamos que su pension se encuentra vigente y la proxima fecha de pago sera el {{$fechacobro}}. Le solicitamos amablemente que realice el pago en esa fecha. Ademas, le recordamos que dispone de un periodo de gracia de 5 dias adicionales para completar el pago sin incurrir en cargos adicionales.</p>
                <p>Muchas gracias por su atencion.</p>
            </div>
        </div>
    @elseif ($vigencia === 0)
        <div class="section">
            <div class="section-title"><b>Pension No Vigente</b></div>
            <div class="section-content">
                <p>Le informamos que la fecha de pago de su pension correspondiente era el {{$fechacobro}}, y el periodo de gracia de 5 dias adicionales ya ha expirado. Por lo tanto, el pago esta actualmente retrasado.</p>
                <p>Agradecemos su atencion y cooperacion en este asunto.</p></div>
        </div>
    @endif

        @if ($visitas === 3 && $category_id !== 13)
        <div class="section">
            <div class="section-title"><b>No. de Visitas: 4</b></div>
            <div class="section-content">
                <p>Esta es tu visita numero 4. En tu siguiente visita se te entregara un obsequio</p>
            </div>
        </div>
    @elseif ($visitas === 8 && $category_id !== 13)
        <div class="section">
            <div class="section-title"><b>No. de Visitas: 9</b></div>
            <div class="section-content">
                <p>Esta es tu visita numero 9. En tu siguiente visita tu estadia sera gratis</p>
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
