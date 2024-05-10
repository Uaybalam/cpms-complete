@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Lista de Pensionados</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container">
        <h1>Lista de Pensionados</h1>
    </div>
    @if(auth()->check() && auth()->user()->role == 'Administrador')
    <div class="container mt-4">
        <div class="alert alert-info bg-white p-4 rounded shadow">
            <h4 class="text-Secondary border-bottom pb-2 mb-3">Pensionados</h4>
            <table id="pensionados-table" class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Precio Fijo</th>
                        <th scope="col">Último Pago</th>
                        <th scope="col">Próximo Pago</th>
                        <th scope="col">Placas del Primer Auto</th>
                        <th scope="col">Placas del Segundo Auto</th>
                        <th scope="col">Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pensionados as $pensionado)
                    <tr style="background-color: {{ $pensionado->proximoPagoEstado() === 'rojo' ? '#FF9B9B' : ($pensionado->proximoPagoEstado() === 'naranja' ? '#FFD6A5' : '#CBFFA9') }}">
                        <td>{{ $pensionado->nombre }}</td>
                        <td>{{ $pensionado->precio_fijo }}</td>
                        <td>{{ $pensionado->ultimo_pago }}</td>
                        <td>{{ $pensionado->proximoPago() }}</td>
                        <td>
                            <ul>
                                @foreach ($pensionado->autos as $auto)
                                    <li>{{ $auto->placa }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul>
                                @foreach ($pensionado->autos as $auto)
                                    <li>{{ $auto->placa2 }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <!-- Icono de editar -->
                            <a href="{{ route('pensionados.edit', $pensionado->id) }}" class="btn btn-primary btn-sm"><i class="ik ik-edit-2"></i></a>
                            <!-- Icono de eliminar -->
                            <form action="{{ route('pensionados.destroy', $pensionado->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?')"><i class="ik ik-trash-2"></i></button>
                            </form>

                            <!-- Icono de editar -->
                            <a  class="btn btn-success btn-sm openModal" data-id="{{ $pensionado->id }}"><i class="bi bi-currency-dollar"></i></a>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalLabel">Datos del Pensionado</h5>
              <button type="button" id="closex" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pensionados.cobrar') }}" method="POST">
                    @csrf
                    <!-- Aquí se mostrarán los datos del pensionado en inputs de solo lectura -->
                    <label>ID</label>
                    <input class="form-control mb-2" type="text" name="pensionado_id" id="pensionado_id" placeholder="ID" readonly>
                    <label>Nombre</label>
                    <input class="form-control mb-2" type="text" name="pensionadoNombre" id="pensionadoNombre" placeholder="Nombre" readonly>
                    <label>Teléfono</label>
                    <input class="form-control mb-2" type="text" name="pensionadoTelefono" id="pensionadoTelefono" placeholder="Teléfono" readonly>
                    <label>Último Pago</label>
                    <input class="form-control mb-2" type="text" name="ultimo_pago" id="ultimo_pago" placeholder="Último Pago" readonly>
                    <label>Placa del primer auto</label>
                    <input class="form-control mb-2" type="text" name="placa1" id="placa1" placeholder="Placa del primer auto" readonly>
                    <label>Color del primer auto</label>
                    <input class="form-control mb-2" type="text" name="color1" id="color1" placeholder="Color del primer auto" readonly>
                    <label>Placa del segundo auto</label>
                    <input class="form-control mb-2" type="text" name="placa2" id="placa2" placeholder="Placa del segundo auto" readonly>
                    <label>Color del segundo auto</label>
                    <input class="form-control mb-2" type="text" name="color2" id="color2" placeholder="Color del segundo auto" readonly>
                    <!-- Formulario con select para las opciones de cobro -->

                    <div class="form-group">
                        <label for="montoCobro">Monto a Cobrar:</label>
                        <select class="form-control" name="montoCobro" id="montoCobro">
                            <option value="1300">1300</option>
                            <option value="1350">1350</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Cobrar</button>
                </form>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Pago</th>
                            <th>Nombre</th>
                            <th>Monto</th>
                            <th>Fecha de Pago</th>
                        </tr>
                    </thead>
                    <tbody id="historialBody">
                        <!-- Los datos del historial se cargarán aquí dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
              <button type="button" id="closeBtn" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>



    <script>
        $('#closeBtn').click(function() {
            location.reload();
        });
        $('#closex').click(function() {
            location.reload();
        });

        $(document).ready(function() {
            // Inicializar la tabla con DataTables
            $('#pensionados-table').DataTable();

            // Agregar el filtro de búsqueda
            $('#pensionados-table_filter input').addClass('form-control');
        });

        $(document).ready(function(){
            $(".openModal").click(function(){
              var pensionadoId = $(this).data('id');
              $.ajax({
                url: '/obtener-pensionados/' + pensionadoId,
                method: 'GET',
                success: function(data) {
                  // Suponiendo que 'data' es un objeto con los datos del pensionado
                  $('#pensionadoNombre').val(data.pensionado.nombre);
                  $('#pensionadoTelefono').val(data.pensionado.Telefono);
                  $('#ultimo_pago').val(data.pensionado.ultimo_pago);
                  $('#placa1').val(data.auto.placa);
                  $('#color1').val(data.auto.Color);
                  $('#placa2').val(data.auto.placa2);
                  $('#color2').val(data.auto.Color2);
                  $('#pensionado_id').val(data.auto.pensionado_id);
                  // Actualiza más campos según los datos recibidos
                  $('#miModal').modal('show');
                },
                error: function(error) {
                  console.log('Error al cargar los datos del pensionado:', error);
                }
              });

               // Obtiene el historial de pagos
            $.ajax({
                url: '/obtener-historial/' + pensionadoId,
                method: 'GET',
                success: function(data) {
                    data.pensionado.forEach(function(pago) {
                        var row = '<tr>' +
                              '<td>' + pago.pensionado_id + '</td>' +
                              '<td>' + pago.nombre + '</td>' +
                              '<td>' + pago.cobro + '</td>' +
                              '<td>' + pago.ultimo_pago + '</td>' +
                              '</tr>';
                        $('#historialBody').append(row);
                    });
                },
                error: function(error) {
                    console.log('Error al cargar el historial de pagos:', error);
                }
            });
            });
          });
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@else
<div class="container mt-4">
    <div class="alert alert-danger" role="alert">
        No tienes permiso de estar aqui
</div>
<button type="button" class="btn btn-danger" onclick="location.href='{{route('home')}}';">Abrir Caja</button>
</div>
@endif
@endsection
