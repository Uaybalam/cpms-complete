@extends('layouts.app')
@section('content')
@include('flash')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3>Crear Usuario</h3></div>
            <div class="card-body">
              @include('auth.register')
            </div>
        </div>
    </div>

</div>
@endsection
