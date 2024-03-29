@extends('layouts.app')
@section('content')
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-10">
            <div class="page-header-title">
                <i class="ik ik-inbox bg-blue"></i>
                <div class="d-inline">
                    <h5>Vehiculos Salientes</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <a class="btn btn-theme" href="{{ route('vehiclesOut.create') }}"> Crear Salida Vehiculo</a>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->role == 'Administrador')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
              @include('vehicles_out.table')
            </div>
        </div>
    </div>
</div>
@else
<div class="container mt-4">
    <div class="alert alert-danger" role="alert">
        No tienes permiso de estar aqui
</div>
<button type="button" class="btn btn-danger" onclick="location.href='{{route('home')}}';">Abrir Caja</button>
</div>
@endif

@endsection
