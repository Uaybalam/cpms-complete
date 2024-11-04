@extends('layouts.app')
@section('content')
@if(auth()->check() && auth()->user()->role == 'Administrador')
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-10">
            <div class="page-header-title">
                <i class="ik ik-inbox bg-blue"></i>
                <div class="d-inline">
                    <h5>Vehiculos Entrantes</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <!-- Formulario de filtro -->
        <form method="GET" action="{{ route('vehiclesIn.index') }}" class="form-inline">
            <div class="form-group mr-2">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o placa" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="{{ route('vehiclesIn.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Vehiculos Actuales</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active mt-3" id="home" role="tabpanel" aria-labelledby="home-tab">
                       @if ($vehiclesIn)
                           @include('vehicles_in.table')
                       @endif
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $vehiclesIn->links() }}
                    </div>
                </div>
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
