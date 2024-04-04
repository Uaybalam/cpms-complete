@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="widget">
                <div class="widget-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="state">
                            <h6>Vehiculos - Entradas</h6>
                            <h2>{{ $total_vehicle_in }}</h2>
                        </div>
                        <div class="icon">
                            <i class="ik ik-truck"></i>
                        </div>
                    </div>
                    <small class="text-small mt-10 d-block">6% + que el mes anterior</small>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100" style="width: 62%;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="widget">
                <div class="widget-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="state">
                            <h6>Vehiculos - Salidas</h6>
                            <h2>{{ $total_vehicle_out }}</h2>
                        </div>
                        <div class="icon">
                            <i class="ik ik-truck"></i>
                        </div>
                    </div>
                    <small class="text-small mt-10 d-block">61% + que el mes anterior</small>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100" style="width: 78%;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="widget">
                <div class="widget-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="state">
                            <h6>Total Vehiculos</h6>
                            <h2>{{ $total_vehicles }}</h2>
                        </div>
                        <div class="icon">
                            <i class="ik ik-truck"></i>
                        </div>
                    </div>
                    <small class="text-small mt-10 d-block">Total Vehiculos</small>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="31" aria-valuemin="0" aria-valuemax="100" style="width: 31%;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="widget">
                <div class="widget-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="state">
                            <h6>Ingresos</h6>
                            <h2>{{  $total_amount  }}</h2>
                        </div>
                        <div class="icon">
                            <i class="ik ik-credit-card"></i>
                        </div>
                    </div>
                    <small class="text-small mt-10 d-block">Total Ingresos</small>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">

        <div class="card-body">
            @include('vehicles.table')
        </div>

    </div>
    <a href="{{ route('lavadas.pdf') }}" class="btn btn-primary">Imprimir</a>
</div>

@endsection
<!-- <script>
     function generarPDF() {
        // Obtener los valores de los campos del formulario
        var Color = document.getElementById('Color').value;
        var folio = document.getElementById('folio').value;
        var modelo = document.getElementById('model').value;
        var platNumber = document.getElementById('plat_number').value;
        var name =  document.getElementById('name').value;
        var Salida = document.getElementByid('')


        // Enviar los datos al controlador utilizando AJAX
        $.ajax({
            url: '/generar-pdf',
            method: 'POST',
            data: {
                Color: Color,
                folio: folio,
                modelo: modelo,
                plat_number: platNumber,
                name: name,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Manejar la respuesta del controlador (si es necesario)
                console.log(response);

            },
            error: function(xhr, status, error) {
                // Manejar cualquier error que ocurra durante la solicitud AJAX
                console.error(error);

            }
        });
    }
</script> -->
