@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<body class="bg-light">
    <div class="container">
        <h1>Corte Parcial</h1>
    </div>
<div class="container mt-4">
<div class="alert alert-info bg-white p-4 rounded shadow">
    <h4 class="text-Secondary border-bottom pb-2 mb-3">Registros</h4>
    <table id="registros-table" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Cajero</th>
                <th scope="col">Total</th>
                <th scope="col">cantidad Inicial</th>
                <th scope="col">Retiro</th>
                <th scope="col">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $registro)
            <tr>
                <td>{{ $registro->id }}</td>
                <td>{{ $registro->Cajero }}</td>
                <td>{{ $registro->Total }}</td>
                <td>{{ $registro->cantidad_inicial }}</td>
                <td>{{ $registro->Retiro }}</td>
                <td>{{ $registro->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <label class="form-label">Cantidad total en caja $:</label>

    @if ($cantidad !== null)
    <input type="text" class="form-control" name="total" id="total" placeholder="total" aria-label="total" value="{{$sumaTotal + $cantidad->cantidad_inicial}}" readonly>
    @else
    <input type="text" class="form-control" name="total" id="total" placeholder="total" aria-label="total" value="0.00" readonly>
    @endif
    <label class="form-label">Cantidad Inicial $:</label>
    @if ($cantidad !== null)
    <input type="text" class="form-control" name="Cantidad" id="Cantidad" placeholder="Cantidad" aria-label="cantidad" value="{{$cantidad->cantidad_inicial}}" readonly>
    @else
    <input type="text" class="form-control" name="Cantidad" id="Cantidad" placeholder="Cantidad" aria-label="cantidad" value="0.00" readonly>
    @endif
    <br>
    <button type="submit" class="btn btn-primary" onclick="retirarInicial()">Retiro Cantidad Inicial</button>
    <button type="submit" class="btn btn-primary" onclick="cierre()">Cerrar Caja</button>
    <button type="submit" class="btn btn-primary" onclick="venta()">Imprimir</button>
</div>
</div>

<script>
    $(document).ready(function() {
        // Inicializar la tabla con DataTables
        $('#registros-table').DataTable();

        // Agregar el filtro de búsqueda
        $('#registros-table_filter input').addClass('form-control');
    });


    function venta()
    {
        obtenerpdfcierre();
    }

    function retirarInicial()
        {
            $.ajax({
                url: '/retiro-parcial',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);
                    location.reload()
                },
                error: function(error) {
                    console.error('Error en la solicitud AJAX:', error);
                }
            });

        }

        function cierre()
        {
            cierreCaja();
            obtenerpdfcierre();
        }

    function cierreCaja()
    {
        $.ajax({
            url: '/cierre-Caja',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                location.reload()
            },
            error: function(error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });

    }


    function obtenerpdfcierre()
    {
        var total = $('input[name="total"]').val();
        var cantidad = $('input[name="Cantidad"]').val();
        // Crear un objeto con los datos a enviar
        var datos = {
            total: total - cantidad,
            cantidad: cantidad,
            detalles: obtenerDetallesTabla()

        };

        // Realizar la solicitud AJAX POST
        $.ajax({
            url: '/generar-Cpdf',
            method: 'POST',
            data: datos,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                window.open(response.pdf_path, '_blank');
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
            var id = fila.find('td:eq(0)').text();
            var Cajero = fila.find('td:eq(1)').text();
            var Total = fila.find('td:eq(2)').text();
            var cantidad = fila.find('td:eq(3)').text();
            var Retiro = fila.find('td:eq(4)').text();
            var Fecha = fila.find('td:eq(5)').text();

            detalles.push({
                id: id,
                Cajero: Cajero,
                Total: Total,
                cantidad: cantidad,
                Retiro: Retiro,
                Fecha: Fecha
            });
        });

        return detalles;
    }

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
