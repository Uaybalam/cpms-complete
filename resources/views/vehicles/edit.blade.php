@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h3>Editar Vehículo</h3></div>
            <div class="card-body">
              <!-- Incluye el formulario, pasando la instancia del vehículo si existe -->
              @include('vehicles.fields', ['vehicle' => $vehicle, 'customer' => $vehicle->customer])
            </div>
        </div>
    </div>
</div>
@endsection
