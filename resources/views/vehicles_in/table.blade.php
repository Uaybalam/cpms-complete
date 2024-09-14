<table id="show_table" class="table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Reg #</th>
            <th>Cliente</th>
            <th>Placa</th>
            <th>Modelo</th>
            <th>Visitas</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Creado Por</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vehiclesIn as $key => $vehicleIn)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $vehicleIn->vehicle->registration_number }}</td>
            <td>{{ $vehicleIn->vehicle->name }}</td>
            <td>{{ $vehicleIn->vehicle->plat_number }}</td>
            <td>{{ $vehicleIn->vehicle->model }}</td>
            <td>{{ $vehicleIn->vehicle->Visitas }}</td>
            <td>{{ $vehicleIn->created_at->format('Y-m-d H:i A') }}</td>
            <td>{{ $vehicleIn->salida }}</td>
            <td>{{ $vehicleIn->user->name }}</td>
            @if (auth()->check() && auth()->user()->role == 'Administrador')
            <td>
                <div class="table-actions">
                    <a href="#" onclick=" confirm('Are you sure you want to delete this?');
                    document.getElementById('delete-data').submit();"><i class="ik ik-trash-2"></i></a>
                     <form id="delete-data" action="{{ route('vehiclesIn.destroy', $vehicleIn->id) }}" method="POST" class="d-none">
                        @method('Delete')
                        @csrf
                    </form>
                </div>
            </td>
            @endif
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>


