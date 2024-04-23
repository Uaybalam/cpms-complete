@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Lista de Pensionados</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
                        </td>
                    </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializar la tabla con DataTables
            $('#pensionados-table').DataTable();

            // Agregar el filtro de búsqueda
            $('#pensionados-table_filter input').addClass('form-control');
        });
    </script>
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
