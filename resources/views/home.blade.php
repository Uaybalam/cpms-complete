@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
            @include('vehicles_in.table')
        </div>

    </div>

</div>
<button type="button" class="btn btn-success" onclick="generarPDF()">Imprimir</button>

@endsection

<script>
    function generarPDF() {
       // Crear un objeto con los datos a enviar
       var datos = {
           detalles: obtenerDetallesTabla()

       };
       console.log(datos);
       // Enviar los datos al controlador utilizando AJAX
       $.ajax({
           url: '/lavadas',
           method: 'POST',
           data: datos,
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
   function obtenerDetallesTabla() {
       // Obtener los datos de la tabla
       var detalles = [];
       $('#data_table tbody tr').each(function () {
           var fila = $(this);
           var cliente = fila.find('td:eq(0)').text();
           var placa = fila.find('td:eq(1)').text();
           var modelo = fila.find('td:eq(2)').text();
           var entrada = fila.find('td:eq(3)').text();
           var salida = fila.find('td:eq(4)').text();

           detalles.push({
               cliente: cliente,
               placa: placa,
               modelo: modelo,
               entrada: entrada,
               salida: salida
           });
       });

       return detalles;
   }
</script>
