@extends('layouts.app')
@section('content')
@include('flash')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h3>Crear Entrada Vehiculo </h3></div>
            <div class="card-body">
                <form action="/imagen" method="get">
                    <button class="btn btn-primary mr-2" type="submit">Capturar Foto</button>
                </form>
              @include('vehicles.fields')
            </div>
        </div>
    </div>

</div>
@endsection
