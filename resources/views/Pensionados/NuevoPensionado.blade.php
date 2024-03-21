@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Agregar Nuevo Pensionado</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <h1>Agregar Nuevo Pensionado</h1>
    </div>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('pensionados.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio_fijo" class="form-label">Precio Fijo</label>
                        <input type="number" class="form-control" id="precio_fijo" name="precio_fijo" required>
                    </div>
                    <div class="mb-3">
                        <label for="ultimo_pago" class="form-label">Último Pago</label>
                        <input type="date" class="form-control" id="ultimo_pago" name="ultimo_pago" required>
                    </div>
                    <div class="mb-3">
                        <label for="placa" class="form-label">Placa de Auto</label>
                        <input type="text" class="form-control" id="placa" name="placa">
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Pensionado</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection
