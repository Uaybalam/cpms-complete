@extends('layouts.app')
@section('content')
@include('flash')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3>Crear Cliente</h3></div>
            @if(auth()->check() && auth()->user()->role == 'Administrador')
            <div class="card-body">
              @include('customers.fields')
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
