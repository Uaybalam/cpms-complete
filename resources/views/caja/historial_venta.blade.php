@extends('layouts.app')
@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Historial</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <h1>Corte Parcial</h1>
    </div>
<div class="container mt-4">
<div class="alert alert-info bg-white p-4 rounded shadow">
    <h4 class="text-Secondary border-bottom pb-2 mb-3">Historial</h4>
    <table id="registros-table" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Cliente</th>
                <th scope="col">Folio</th>
                <th scope="col">PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $registro)
            <tr>
                <td>{{ $registro->id }}</td>
                <td>{{ $registro->cliente }}</td>
                <td>{{ $registro->folio }}</td>
                <td><a href="{{ route('mostrar.Factura', $registro->id) }}" target="_blank"><img src="https://png.pngtree.com/png-vector/20220606/ourlarge/pngtree-pdf-file-icon-png-png-image_4899509.png" width="30" height="30"></td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

<script>
    $(document).ready(function() {
        // Inicializar la tabla con DataTables
        $('#registros-table').DataTable();

        // Agregar el filtro de búsqueda
        $('#registros-table_filter input').addClass('form-control');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
