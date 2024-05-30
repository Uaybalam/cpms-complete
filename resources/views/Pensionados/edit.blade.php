@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Pensionado</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <h1>Editar Pensionado</h1>
    </div>
    @if(auth()->check() && auth()->user()->role == 'Administrador')
    <div class="container mt-4">
        <form action="{{ route('pensionados.update', $pensionado->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Pensionado</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $pensionado->nombre }}" readonly>
            </div>
            @foreach($pensionado->autos as $index => $auto)
                <div class="mb-3">
                    <label for="placa1" class="form-label">Placa del Auto 1</label>
                    <input type="text" class="form-control" id="placa1" name="placa1" value="{{ $auto->placa }}">
                </div>
                <div class="mb-3">
                    <label for="color1" class="form-label">Color del Auto 1</label>
                    <input type="text" class="form-control" id="color1" name="color1" value="{{ $auto->Color }}">
                </div>
                <div class="mb-3">
                    <label for="modelo1" class="form-label">Modelo del Auto 1</label>
                    <input type="text" class="form-control" id="modelo1" name="modelo1" value="{{ $auto->Modelo }}">
                </div>
                <div class="mb-3">
                    <label for="placa2" class="form-label">Placa del Auto 2</label>
                    <input type="text" class="form-control" id="placa2" name="placa2" value="{{ $auto->placa2 }}">
                </div>
                <div class="mb-3">
                    <label for="color2" class="form-label">Color del Auto 2</label>
                    <input type="text" class="form-control" id="color2" name="color2" value="{{ $auto->Color2 }}" >
                </div>
                <div class="mb-3">
                    <label for="modelo2" class="form-label">Modelo del Auto 2</label>
                    <input type="text" class="form-control" id="modelo2" name="modelo2" value="{{ $auto->Modelo2 }}">
                </div>
            @endforeach
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
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
