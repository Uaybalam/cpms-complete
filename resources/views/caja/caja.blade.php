@extends('layouts.app')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <body>
        @if ($registros->isEmpty())
            <div class="container mt-4">
                <div class="alert alert-danger" role="alert">
                    Aun no se abre ninguna caja!
                </div>
                <button type="button" class="btn btn-danger" onclick="location.href='{{ route('abrir.caja') }}';">Abrir
                    Caja</button>
            </div>
        @else
            <div class="container mt-4">
                <form id="ventaForm" action="generarVenta()">
                    <div class="alert alert-info bg-white p-4 rounded shadow">
                        <h4 class="text-Secondary border-bottom pb-2 mb-3">Cobrar</h4>
                        <label for="selectFiltrado" class="form-label" style="font-size:18px;">Ingresa Num. de Placa</label>
                        <input type="text" id="inputPlaca" name="inputPlaca" class="form-control"
                            placeholder="Ingresa la placa del vehículo" style="font-size:18px;">
                            <input type="hidden" name="visitas" id="visitas" readonly>
                            <input type="hidden" name="category_id" id="category_id" readonly>
                        <table id="registros-table" class="table table-striped" hidden>
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
                        <table id="registros-de-tabla" class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Dias</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td name="numero"></td>
                                    <td name="fechainicio"></td>
                                    <td name="subtotal"></td>
                                </tr>
                                <tr>
                                    <td name="numero2"></td>
                                    <td name="fechasalida"></td>
                                    <td name="total"></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-danger" onclick="cancelar()">Cancelar venta</button>
                        <label class="form-label">Total ($) <input type="text" class="form-control" id="precio_fijo"
                                name="total" placeholder="total" aria-label="total" readonly></label>

                    </div>
                    <div class="alert alert-info bg-white p-4 rounded shadow">
                        <h4 class="text-Secondary border-bottom pb-2 mb-3">Datos venta</h4>
                        <label class="form-label" style="font-size:18px;">Cliente</label>
                        <input type="text" class="form-control" name="cliente" id="cliente"
                            placeholder="Nombre del cliente" aria-label="Nombre del cliente" readonly>
                        <h4 class="text-Secondary border-bottom pb-2 mb-3"></h4>
                        <label class="form-label" style="font-size:18px;">Folio</label>
                        <input type="text" class="form-control" name="folio" id="folio" placeholder="Folio"
                            aria-label="Folio" readonly>
                    </div>
                    <div class="alert alert-info bg-white p-4 rounded shadow">
                        <h4 class="text-Secondary border-bottom pb-2 mb-3">Realizar venta</h4>
                        <input type="text" class="form-control" style="font-size:23px;" name="total" placeholder="Total" id="total"
                            aria-label="Total" readonly>
                        <h4 class="text-Secondary border-bottom pb-2 mb-3"></h4>
                        <label class="form-label" style="font-size:18px;">Cantidad Recibida</label>
                        <input type="text" class="form-control" style="font-size:23px;" name="Cantidad" id="Cantidad" placeholder="Cantidad Recibida"
                            aria-label="cantidad">
                        <label class="form-label" style="font-size:18px; margin-top:10px;">Cambio</label>
                        <input type="text" class="form-control" style="font-size:23px;" name="Cambio" placeholder="Cambio" id="cambio"
                            aria-label="Cambio" readonly>
                        <h4 class="text-Secondary border-bottom pb-2 mb-3"></h4>
                        <br>
                        <br>
                        <button type="button" class="btn btn-success" onclick="funcionesBoton()">Cobrar</button>
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



            function funcionesBoton() {
                generarPDF();

                generarVenta();
            }


            function generarVenta() {
                var total = $('input[name="total"]').val();
                var placa = $('input[name="inputPlaca"]').val();
                var subtotal = $('td[name="subtotal"]').text();

                var datos = {
                    total: total,
                    placa: placa,
                    subtotal: subtotal,
                };
                $.ajax({
                    url: '/venta',
                    method: 'POST',
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


            function generarPDF() {
                // Obtener los datos del formulario
                var cliente = $('input[name="cliente"]').val();
                var folio = $('input[name="folio"]').val();
                var total = $('input[name="total"]').val();
                var cantidad = $('input[name="Cantidad"]').val();
                var cambio = $('input[name="Cambio"]').val();
                var visitas = $('input[name="visitas"]').val();
                var category_id = $('input[name="category_id"]').val();
                var inputPlaca = $('input[name="inputPlaca"]').val();

                // Crear un objeto con los datos a enviar
                var datos = {
                    cliente: cliente,
                    folio: folio,
                    total: total,
                    cambio: cambio,
                    cantidad: cantidad,
                    visitas: visitas,
                    category_id: category_id,
                    inputPlaca: inputPlaca,
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
                $('#registros-de-tabla tbody tr').each(function() {
                    var fila = $(this);
                    var fecha = fila.find('td:eq(1)').text();
                    var subtotal = fila.find('td:eq(2)').text();

                    detalles.push({
                        fecha: fecha,
                        subtotal: subtotal
                    });
                });

                return detalles;
            }

            function cancelar() {
                location.reload()
            }

            //para poner la fecha como folio
            //         {{--  var folioInput = document.getElementById('folio');
    // var fecha = new Date();
    // var formatoFecha = fecha.toISOString().replace(/[-T:.]/g, '');
    // folioInput.value = formatoFecha;  --}}

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

            $(document).ready(function() {
    function obtenerDatosPlaca(placa) {
        if (placa.trim() !== '') {
            $('#registros-table tbody').html('<tr><td colspan="4">Cargando...</td></tr>');

            $.ajax({
                url: '/obtener-datos/' + placa,
                method: 'GET',
                success: function(data) {
                    var fechaEntrada = new Date(data.fechaEntrada);
                    var fechaSalida = new Date(data.fechaSalida);
                    // Calcula la diferencia en milisegundos
                    var diferenciaMilisegundos = fechaSalida - fechaEntrada;

                    // Convierte la diferencia a días
                    var diasDiferencia = diferenciaMilisegundos / (1000 * 60 * 60 * 24);
                    var diferenciaHoras = data.diferenciaHoras;
                    var totalAPagar = data.totalAPagar;
                    var subtotal = data.vehiculo.packing_charge;


                    var detallesHTML = `
                        <tr>
                            <td>1</td>
                            <td>${fechaEntrada.toLocaleString()}</td>
                            <td>${data.vehiculo.modelo}</td>
                            <td>$${totalAPagar.toFixed(2)}</td>
                        </tr>
                    `;

                    $('#registros-table tbody').html(detallesHTML);
                    $('input[name="total"]').val(totalAPagar.toFixed(2));
                    $('td[name="fechainicio"]').text(fechaEntrada.toLocaleString());

                    if(data.pensionados)
                    {
                        $('td[name="subtotal"]').text(data.vigencia);
                    }
                    else{
                    $('td[name="subtotal"]').text('$' + subtotal.toFixed(2));
                    }

                    $('td[name="numero"]').text('1');
                    $('td[name="numero2"]').text(diasDiferencia.toFixed(0));
                    $('td[name="fechasalida"]').text(fechaSalida.toLocaleString());
                    $('td[name="total"]').text('$' + totalAPagar.toFixed(2));
                    $('input[name="cliente"]').val(data.vehiculo.name);
                    $('input[name="folio"]').val(data.vehiculo.registration_number);
                    $('input[name="visitas"]').val(data.vehiculo.Visitas);
                    $('input[name="category_id"]').val(data.vehiculo.category_id);
                },
                error: function(error) {
                    console.error('Error al obtener detalles:', error);
                }
            });
        }
    }

    $('#inputPlaca').on('input', function() {
        var platNumber = $(this).val().trim();
        if (platNumber.length >= 5) {
            obtenerDatosPlaca(platNumber);
        }
    });
});

        </script>
    @endsection
