@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="bg-light">
    @if(Session::has('error'))
    <div class="alert alert-danger">
        {{ Session::get('error') }}
    </div>
    @endif
    <div class="container">
        <h1>Agregar Nuevo Pensionado</h1>
    </div>
    @if(auth()->check() && auth()->user()->role == 'Administrador')
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('pensionados.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Pensionado</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="placa" class="form-label">Placa del Primer Auto</label>
                                <input type="text" class="form-control" id="placa" name="placa" required>
                            </div>

                            <div class="mb-3">
                                <label for="modelo" class="form-label">Modelo del Primer Auto</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" required>
                            </div>
                            <div class="mb-3">
                                <label for="color" class="form-label">Color del Primer Auto</label>
                                <input type="text" class="form-control" id="color" name="color" required>
                            </div>
                            <div class="mb-3">
                                <label for="precio_fijo" class="form-label">Precio Fijo</label>
                                <input type="number" class="form-control" id="precio_fijo" value="{{$precio}}" name="precio_fijo" required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="Total" class="form-label">Total</label>
                                <input type="number" class="form-control" id="Total" value="{{$precio}}" name="Total" required readonly>
                            </div>
                            <div class="mb-3">
                                <input type="hidden"  class="form-control" id="name" value="T-Pension" name="name" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="ultimo_pago" class="form-label">Último Pago</label>
                                <input type="date" class="form-control" id="ultimo_pago"  name="ultimo_pago" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="placa2" class="form-label">Placa del segundo Auto</label>
                                <input type="text" class="form-control" id="placa2" name="placa2">
                            </div>
                            <div class="mb-3">
                                <label for="modelo2" class="form-label">Modelo del Segundo Auto</label>
                                <input type="text" class="form-control" id="modelo2" name="modelo2">
                            </div>
                            <div class="mb-3">
                                <label for="color2" class="form-label">Color del Segundo Auto</label>
                                <input type="text" class="form-control" id="color2" name="color2" >
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>
                            <div class="mb-3">
                                <label for="metodo_pago" class="form-label">Método de Pago</label>
                                <select class="form-control" id="metodo_pago" name="metodo_pago">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta de Crédito o Débito</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Agregar Pensionado</button>
                </form>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;


        document.getElementById("ultimo_pago").value = today;


        //Metodo para subir el precio si es pago con tarjeta
        document.getElementById('metodo_pago').addEventListener('change', function() {
            var precioFijoInput = document.getElementById('precio_fijo');
            var precioFijo = parseFloat(precioFijoInput.value);
            var totalInput = document.getElementById('Total');


            if (this.value === 'tarjeta') {

                precioFijo += 50;
            }
            else
            {
                if (precioFijo === 1350)
                {
                precioFijo -= 50;
                }
            }

            totalInput.value = precioFijo.toFixed(2);
        })
    </script>
</body>
@else
<div class="container mt-4">
    <div class="alert alert-danger" role="alert">
        No tienes permiso de estar aqui
</div>
<button type="button" class="btn btn-danger" onclick="location.href='{{route('home')}}';">Abrir Caja</button>
</div>
@endif

@endsection
