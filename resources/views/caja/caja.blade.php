@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
    <input type="text" id="inputPlaca" name="inputPlaca" class="form-control" placeholder="Ingresa la placa del vehículo">


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
        <label class="form-label">Total ($) <input type="text" class="form-control" id="precio_fijo" name="total" placeholder="total" aria-label="total" readonly></label>

</div>
<div class="alert alert-info bg-white p-4 rounded shadow">
    <h4 class="text-Secondary border-bottom pb-2 mb-3">Datos venta</h4>
    <label class="form-label">Cliente</label>
    <input type="text" class="form-control" name="cliente" id="cliente" placeholder="Nombre del cliente" aria-label="Nombre del cliente" readonly>
    <h4 class="text-Secondary border-bottom pb-2 mb-3"></h4>
    <label class="form-label">Folio</label>
    <input type="text" class="form-control" name="folio" id="folio" placeholder="Folio" aria-label="Folio" readonly>
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
        var totalInput = document.getElementById('total');
        checkbox.addEventListener('change', function() {
            console.log('Estado del checkbox:', checkbox.checked);

            if (checkbox.checked) {

            totalInput.value = (parseFloat(totalInput.value) + 50).toFixed(2);
        } else {

            totalInput.value = (parseFloat(totalInput.value) - 50).toFixed(2);
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
        var placa = $('input[name="inputPlaca"]').val();
        var datos = {
            total: total,
            placa: placa,
        };
        $.ajax({
            url: '/venta',
            method:'POST',
            data: datos,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.redirect) {
                    location.reload()
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
        var folio = $('input[name="folio"]').val();
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
    {{--  var folioInput = document.getElementById('folio');
    var fecha = new Date();
    var formatoFecha = fecha.toISOString().replace(/[-T:.]/g, '');
    folioInput.value = formatoFecha;  --}}

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
                        var vehiculo = data.vehiculo.category_id;
                        var ajusteEspecialAplicado = false;
                        var ajusteEspecialAplicadoP = false;
                        var entrada = fechaInicio.getHours();


                        if (data.pensionados && data.pensionados.ultimo_pago) {
                            var fechaUltimoPago = new Date(data.pensionados.ultimo_pago);
                            fechaLimiteConColchon = new Date(fechaUltimoPago.setMonth(fechaUltimoPago.getMonth() + 1));
                            fechaLimiteConColchon.setDate(fechaLimiteConColchon.getDate() + 6);
                        }

                        for (var hora = 0; fechaInicio <= fechaFin; hora++) {
                            // Obtener el estado del próximo pago
                            let fechaComparacion = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth(), fechaInicio.getDate());

                            if ( data.pensionados && fechaComparacion > fechaLimiteConColchon && !ajusteEspecialAplicadoP) {
                                subtotal = 210;
                                ajusteEspecialAplicadoP = true;
                            }
                            if(dias === 2 && vehiculo === 11 && !ajusteEspecialAplicado) {
                                subtotal = 420;
                                ajusteEspecialAplicado = true; // Marcar que el ajuste especial se ha aplicado
                            }


                            // Calcular el subtotal basado en múltiplos de 7 días
                            if (dias % 7 === 0 ) {
                                if(subtotal === 0)
                                {
                                subtotal = 0;
                                }
                                else if(vehiculo === 10){
                                    subtotal = Math.floor(dias / 7) * 900;
                                }
                                else{
                                    subtotal = Math.floor(dias / 7) * 1200; // Incrementar el valor base de 1200 por cada múltiplo de 7
                                }
                            }

                            // Generar las filas de la tabla
                            detallesHTML += '<tr><td>' + dias + ' DIAS</td><td>' + fechaInicio.toLocaleString()
                                + '</td><td>' + data.vehiculo.model + '</td><td>' + subtotal.toFixed(2) + '</td></tr>';

                            // Ajuste de subtotal después de cada hora
                            if (subtotal > 0) {
                                if(vehiculo === 11 && dias < 2){
                                    subtotal += 15;
                                }
                                else if(vehiculo === 10){
                                    subtotal +=20
                                }
                                else{

                                subtotal += 30;
                                }
                            }

                            // Incrementar la fecha en una hora
                            fechaInicio.setHours(fechaInicio.getHours() + 1);

                            // Mantener el precio constante después de la séptima hora y avanzar al día siguiente
                            if (hora % 24 === 6) {
                                hora = 23; // Reiniciar el contador de horas al inicio de cada día
                                fechaInicio.setDate(fechaInicio.getDate() + 1);
                                fechaInicio.setHours(entrada);

                                // Aplicar ajustes adicionales al inicio del día 8
                                if (dias % 7 === 0 ) {
                                    if(subtotal === 0)
                                    {
                                        subtotal = 0;
                                    }
                                    else if(vehiculo === 10){
                                        subtotal += 129;
                                    }
                                    else{
                                        subtotal += 180;
                                        descuento = 0;
                                    }
                                }
                            }

                            // Reiniciar el contador de horas al inicio de cada día
                            if (hora === 23) {
                                dias++;
                            }

                            // Ajustar el total del subtotal
                            if (subtotal > 0) {
                                if(vehiculo === 11 && dias < 2){
                                    totalSubtotal = subtotal - 15;
                                }
                                else if(vehiculo === 10){
                                    totalSubtotal = subtotal - 20;
                                }
                                else{
                                totalSubtotal = subtotal - 30; // Este ajuste parece corregir un incremento anterior, asegúrate de que es lo deseado
                                }
                            } else {
                                totalSubtotal = 0;
                            }
                        }

                        // Actualizar la tabla con las filas creadas
                        $('#registros-table tbody').html(detallesHTML);
                        $('input[name="total"]').val(totalSubtotal.toFixed(2));

                        // Llenar el campo de nombre de cliente con el nombre del propietario del vehículo
                        $('input[name="cliente"]').val(data.vehiculo.name);
                        $('input[name="folio"]').val(data.vehiculo.registration_number);
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
@endsection

