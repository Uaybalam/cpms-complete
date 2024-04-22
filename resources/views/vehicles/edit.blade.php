@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h3>Editar Vehículo</h3></div>
            <div class="card-body">
            <form action="{{ route('vehicles.update', $vehiculo->id) }}" method="POST" class="forms-sample">
    @csrf
    
    @method('PUT')

    
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputEmail3">Registration Number</label>
                <input type="text" name="registration_number"
                    value="{{ $vehiculo -> registration_number}}" class="form-control"
                    id="folio" readonly placeholder="Registration Number Auto">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputName1">Nombre Cliente</label>
                <input type="text" name="name" value="{{ $vehiculo -> name }}" class="form-control" id="name" placeholder="Name">
                @if (isset($customer))
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                @endif

            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="fechaSalida">Fecha y hora de salida</label>
                <input type="datetime-local" name="salida" value="{{ $salida -> salida}}" class="form-control" id="salida" placeholder="Fecha y hora de salida">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="phone">Telefono</label>
                <input type="tel" name="phone" value="{{ $vehiculo -> phone }}" class="form-control" id="phone" placeholder="Phone">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="category_id">Categoría</label>
                <select name="category_id" id="category_id" class="form-control" onchange="getCosto()">
                    <option value="">Seleccionar</option>
                    @foreach ($categories as $category)
                        <option value="{{ $vehiculo->category_id }}" @if (isset($vehicle))
                            {{ $vehiculo->category_id == $category->id ? 'selected' : '' }}
                        @endif>
                        {{ $categories->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id ?? '' }}">
            </div>
                <input type="hidden" name="packing_charge" id="packing_charge" value="{{ $vehicle->costo ?? '' }}">
                <input type="hidden" name="visitas" id="visitas">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">

                <label for="exampleInputEmail3">Modelo del Vehiculo</label>
                <input  type="text" name="model" class="form-control" id="model" placeholder="Vehicle model" value="{{$vehiculo -> model}}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="plat_number">Numero de Placa del Vehiculo</label>
                <input id="plat_number" type="text" name="plat_number" value="{{ $vehiculo -> plat_number }}"
                    class="form-control" placeholder="Vehicle Plat Number" onkeyup="buscarPlaca()" readonly>
                <a onclick="ActivarInput()" class="btn btn-primary btn-lg active" role="button">Ingresar Manual</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail3">Color</label>
                <input  name="color"
                class="form-control" id="Color" placeholder="Color" value="{{ $vehiculo -> color}}">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mr-2">Editar</button>
    <button class="btn btn-light">Cancelar</button>
</form>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    function ActivarInput()
    {
        document.getElementById('plat_number').readOnly = false;

    }
    function getCosto() {
        var category_id = document.getElementById('category_id').value;
        if (category_id !== '') {
            // Realizar una solicitud AJAX para obtener el costo asociado a la categoría seleccionada
            $.ajax({
                url: '/get-costo/' + category_id,
                type: 'GET',
                success: function(response) {
                    // Actualizar el valor del campo de costo en el formulario
                    document.getElementById('packing_charge').value = response.costo;
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    }
</script>