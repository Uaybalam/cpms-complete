@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h3>Editar Vehiculo Entrante</h3></div>
            <div class="card-body">
              @include('vehicles_in.fields')
            </div>
        </div>
    </div>

</div>
@endsection
