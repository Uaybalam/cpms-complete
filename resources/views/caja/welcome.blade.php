@extends('layouts.app')
@section('content')

<body class="bg-light">
<br>
<br>
<br>
<br>
<br>
<br>
<div class="container mt-4">
    <div class="alert alert-success bg-white p-4 rounded shadow mb-4">
        <h4 class="text-Secondary border-bottom pb-2 mb-3">Caja</h4>
        <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#myModal">Apertura nueva</button>
    </div>

    <div class="alert alert-info bg-white p-4 rounded shadow">
        <h4 class="text-Secondary border-bottom pb-2 mb-3">Registros</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Hora</th>
                    <th scope="col">Fondo Inicial</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $registro)
                <tr>
                    <td>{{ $registro->nombre }}</td>
                    <td>{{ $registro->created_at }}</td>
                    <td>{{ $registro->cantidad_inicial }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{ route('guardar-datos') }}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Mi Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Nombre</span>
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre de usuario" aria-label="Nombre de usuario" aria-describedby="basic-addon1">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon2">Cantidad inicial ($)</span>
                        <input type="text" class="form-control" name="cantidad_inicial" placeholder="0.00" aria-label="Cantidad inicial" aria-describedby="basic-addon2">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
