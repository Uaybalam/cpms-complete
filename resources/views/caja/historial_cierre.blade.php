@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Historial de Cierres de Caja</h1>

        <!-- Formulario de búsqueda -->
        <form method="GET" action="{{ route('cierre_caja.index') }}" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <input type="date" name="fecha" class="form-control" placeholder="Fecha de Cierre" value="{{ request('fecha') }}">
                </div>
                <div class="col-md-3">
                    <select name="turno" class="form-control">
                        <option value="">Seleccione Turno</option>
                        <option value="matutino" {{ request('turno') == 'matutino' ? 'selected' : '' }}>Matutino (7:00 - 19:00)</option>
                        <option value="vespertino" {{ request('turno') == 'vespertino' ? 'selected' : '' }}>Vespertino (20:00 - 6:00)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-secondary" onclick="obtenerpdfcierre()">Imprimir</button>
                </div>
            </div>
        </form>

        <!-- Tabla de resultados -->
        <table class="table table-striped" id="registros-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cajero</th>
                    <th>Placa</th>
                    <th>Total</th>
                    <th>Estatus</th>
                    <th>Fecha de Cierre</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cierres as $cierre)
                    <tr>
                        <td>{{ $cierre->id }}</td>
                        <td>{{ $cierre->Cajero }}</td>
                        <td>{{ $cierre->Placa }}</td>
                        <td>{{ $cierre->Total }}</td>
                        <td>{{ $cierre->Estatus }}</td>

                        <td>{{ $cierre->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron cierres de caja para los filtros seleccionados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Paginación -->
        {{ $cierres->links() }}
    </div>
@endsection

<!-- Script directamente en la vista -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function obtenerpdfcierre() {
        var fecha = $('input[name="fecha"]').val();
        var turno = $('select[name="turno"]').val();

        var datos = {
            fecha: fecha,
            turno: turno
        };

        // Realizar la solicitud AJAX POST
        $.ajax({
            url: '/generar-HCpdf',
            method: 'POST',
            data: datos,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Asegúrate de obtener el token correctamente
            },
            success: function(response) {
                console.log(response);
                window.open(response.pdf_path, '_blank'); // Abre el PDF generado
            },
            error: function(error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    }
</script>
