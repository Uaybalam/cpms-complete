@extends('layouts.app')
@section('content')
@include('flash')

<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <i class="ik ik-inbox bg-blue"></i>
                <div class="d-inline">
                    <h5>Lista de Vehiculos</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <nav class="breadcrumb-container" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="../index.html"><i class="ik ik-home"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Tablas</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tabla de Datos</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <!-- Formulario de filtro -->
        <form method="GET" action="{{ route('vehicles.index') }}" class="form-inline">
            <div class="form-group mr-2">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o placa" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('vehicles.index') }}" class="btn btn-secondary ml-2">Limpiar</a>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                @include('vehicles.table')
            </div>
            <div class="d-flex justify-content-center">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
