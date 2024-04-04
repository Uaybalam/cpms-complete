@extends('layouts.app')
@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Caja</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
</head>
<br>
<br>
<br>
<br>
<br>
<body>
@if ($registros->isEmpty())
<div class="container mt-4">
    <div class="alert alert-danger" role="alert">
        Aun no se abre ninguna caja!
</div>
<button type="button" class="btn btn-danger" onclick="location.href='{{route('abrir.caja')}}';">Abrir Caja</button>
</div>

@else
<div class="container mt-4">
<form id="ventaForm" action="generarVenta()">
<div class="alert alert-info bg-white p-4 rounded shadow">
    <h4 class="text-Secondary border-bottom pb-2 mb-3">Registros</h4>
    <label for="selectFiltrado" class="form-label">Selecciona un vehiculo:</label>
    <input type="text" id="inputPlaca" class="form-control" placeholder="Ingresa la placa del vehículo">


        <table id="registros-table" class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Vehiculo</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <button type="button" class="btn btn-danger" onclick="cancelar()">Cancelar venta</button>
        <label class="form-label">Total ($) <input type="text" class="form-control" name="total" placeholder="total" aria-label="total" readonly></label>

</div>
<div class="alert alert-info bg-white p-4 rounded shadow">
    <h4 class="text-Secondary border-bottom pb-2 mb-3">Dtaos venta</h4>
    <label class="form-label">Cliente</label>
    <input type="text" class="form-control" name="cliente" id="cliente" placeholder="Nombre del cliente" aria-label="Nombre del cliente" readonly>
    <h4 class="text-Secondary border-bottom pb-2 mb-3"></h4>
    <label class="form-label">Folio</label>
    <input type="text" class="form-control" name="Folio" id="folio" placeholder="Folio" aria-label="Folio" readonly>
</div>
<div class="alert alert-info bg-white p-4 rounded shadow">
    <h4 class="text-Secondary border-bottom pb-2 mb-3">Realizar venta</h4>
    <input type="text" class="form-control" name="total" placeholder="Total" id="total" aria-label="Total" readonly>
    <h4 class="text-Secondary border-bottom pb-2 mb-3"></h4>
    <label class="form-label">Cantidad</label>
    <input type="text" class="form-control" name="Cantidad" id="Cantidad" placeholder="Cantidad" aria-label="cantidad">
    <label class="form-label">Cambio</label>
    <input type="text" class="form-control" name="Cambio" placeholder="Cambio" id="cambio" aria-label="Cambio" readonly>
    <h4 class="text-Secondary border-bottom pb-2 mb-3"></h4>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="apagado" id="checkboxPagoTarjeta">
        <label class="form-check-label" for="checkboxPagoTarjeta">Pago con Tarjeta</label>
    </div>
    <br>
    <br>
    <button type="button" class="btn btn-success" onclick="funcionesBoton()">Aceptar</button>
</div>
</form>
</div>
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inicializar la tabla con DataTables
          // Inicializar la tabla con DataTables
    $('#registros-table').DataTable({
        "pageLength": 12 // Mostrar solo 12 registros por página
    });

        // Agregar el filtro de búsqueda
        $('#registros-table_filter input').addClass('form-control');
    });

    document.addEventListener('DOMContentLoaded', function() {
        var checkbox = document.getElementById('checkboxPagoTarjeta');
        var cantidad = document.getElementById('Cantidad');
        var total = $('input[name="total"]').val();
        checkbox.addEventListener('change', function() {
            console.log('Estado del checkbox:', checkbox.checked);

            if (checkbox.checked) {
                cantidad.value = total;
                console.log(cantidad.valueui);
            } else {
                console.log('El checkbox no está marcado');
            }
        });
    });



    function funcionesBoton()
    {
        generarPDF();

        generarVenta();
    }


    function generarVenta()
    {
        var total = $('input[name="total"]').val();
        var datos = {
            total: total,
        };
        console.log(datos);
        $.ajax({
            url: '/venta',
            method:'POST',
            data: datos,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.redirect) {
                    //location.reload()
                } else {
                    console.log("No se agrego nada")
                }

            },
            error: function(error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    }


    function generarPDF()
    {
        // Obtener los datos del formulario
        var cliente = $('input[name="cliente"]').val();
        var folio = $('input[name="Folio"]').val();
        var total = $('input[name="total"]').val();
        var cantidad = $('input[name="Cantidad"]').val();
        var cambio = $('input[name="Cambio"]').val();
        // Crear un objeto con los datos a enviar
        var datos = {
            cliente: cliente,
            folio: folio,
            total: total,
            cambio: cambio,
            cantidad: cantidad,
            detalles: obtenerDetallesTabla()

        };

        // Realizar la solicitud AJAX POST
        $.ajax({
            url: '/generar-pdf-salida',
            method: 'POST',
            data: datos,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
            },
            error: function(error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    }
    function obtenerDetallesTabla() {
        // Obtener los datos de la tabla
        var detalles = [];
        $('#registros-table tbody tr').each(function () {
            var fila = $(this);
            var dias = fila.find('td:eq(0)').text();
            var fecha = fila.find('td:eq(1)').text();
            var vehiculo = fila.find('td:eq(2)').text();
            var descuento = fila.find('td:eq(3)').text();
            var subtotal = fila.find('td:eq(4)').text();

            detalles.push({
                dias: dias,
                fecha: fecha,
                vehiculo: vehiculo,
                descuento: descuento,
                subtotal: subtotal
            });
        });

        return detalles;
    }

    function cancelar()
    {
        location.reload()
    }

    //para poner la fecha como folio
    var folioInput = document.getElementById('folio');
    var fecha = new Date();
    var formatoFecha = fecha.toISOString().replace(/[-T:.]/g, '');
    folioInput.value = formatoFecha;

    //logica para el cambio
    var cantidadInput = document.getElementById('Cantidad');
    var totalInput = document.getElementById('total');
    var cambioInput = document.getElementById('cambio');
    cantidadInput.addEventListener('input', actualizarCambio);
    totalInput.addEventListener('input', actualizarCambio);

    function actualizarCambio() {
        var cantidad = parseFloat(cantidadInput.value) || 0;
        var total = parseFloat(totalInput.value) || 0;
        var resultado = cantidad - total;
        cambioInput.value = resultado.toFixed(2);

    }

    $(document).ready(function(){
        // Función para manejar la solicitud AJAX y llenar la tabla
        function obtenerDatosPlaca(placa) {
            // Validar que se haya ingresado una placa
            if (placa.trim() !== '') {
                // Actualizar la tabla con los detalles
                $('#registros-table tbody').html('<tr><td colspan="4">Cargando...</td></tr>');

                $.ajax({
                    url: '/obtener-datos/' + placa,
                    method: 'GET',
                    success: function(data) {
                        // Llenar la tabla con los detalles del vehículo
                        var fechaInicio = new Date(data.vehiculoIn.created_at);
                        var fechaFin = new Date();
                        var detallesHTML = '';
                        var totalSubtotal = 0;
                        var subtotal = parseFloat(data.vehiculo.packing_charge);
                        var dias = 1;
                        var descuento = 0;

                        for (var hora = 0; fechaInicio <= fechaFin; hora++) {
                            // Generar las filas de la tabla
                            if (dias % 7 === 0) {
                                if(subtotal === 0)
                                {
                                subtotal = 0;
                                }
                                else
                                {
                                    // Calcular el subtotal basado en el múltiplo de 7 que representa el día actual
                                    subtotal = Math.floor(dias / 7) * 1200; // Incrementar el valor base de 1200 por cada múltiplo de 7
                                }

                            }

                            detallesHTML += '<tr><td>' + dias + ' DIAS</td><td>' + fechaInicio.toLocaleString()
                                + '</td><td>' + data.vehiculo.model + '</td><td>' + (subtotal).toFixed(2) + '</td></tr>';
                            if(subtotal === 0)
                            {
                                subtotal = 0;
                            }
                            else
                            {
                                subtotal += 30;
                            }
                            // Incrementar la fecha en una hora
                            fechaInicio.setHours(fechaInicio.getHours() + 1);

                            // Mantener el precio constante después de la séptima hora
                            if (hora % 24 === 6) {
                                subtotal = subtotal;
                                hora = 23;
                                // Avanzar al siguiente día
                                fechaInicio.setDate(fechaInicio.getDate() + 1);
                                fechaInicio.setHours(15);

                                //se continual en el dia 8
                                if (dias % 7 === 0) {
                                    if(subtotal === 0)
                                    {
                                        subtotal = 0;
                                    }
                                    else
                                    {
                                        subtotal += 180;
                                        descuento = 0;
                                     }
                                }
                            }

                            // Reiniciar el contador de horas al inicio de cada día
                            if (hora === 23) {
                                dias++;
                            }
                            if(subtotal === 0)
                            {
                                totalSubtotal = 0;
                            }

                            else
                            {
                                totalSubtotal = subtotal - 30;
                            }

                        }

                        // Actualizar la tabla con las filas creadas
                        $('#registros-table tbody').html(detallesHTML);
                        $('input[name="total"]').val(totalSubtotal.toFixed(2));

                        // Llenar el campo de nombre de cliente con el nombre del propietario del vehículo
                        $('input[name="cliente"]').val(data.vehiculo.name);
                        // Inicializar la tabla con DataTables después de que se hayan llenado los datos



                    },
                    error: function(error) {
                        console.error('Error al obtener detalles:', error);
                    }
                });
            }
        }

        $('#inputPlaca').on('input', function() {
            var platNumber = $(this).val().trim();
            if (platNumber.length >= 7) {
                obtenerDatosPlaca(platNumber);
            }
        });

    });

</script>
</body>
</html>
