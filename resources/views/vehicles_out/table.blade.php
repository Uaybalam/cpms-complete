<table id="data_table" class="table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Reg #</th>
            <th>Cliente</th>
            <th>Placa</th>
            <th>Creado El</th>
            <th>Creado Por</th>
            <th class="nosort">Operation</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vehicleData as $key => $vehicleOut)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $vehicleOut['registration_number'] }}</td>
            <td>{{ $vehicleOut['name'] }}</td>
            <td>{{ $vehicleOut['plat_number'] }}</td>
            <td>{{ $vehicleOut['created_at']->format('Y/m/d H:i A') }}</td>
            <td>{{ $vehicleOut['created_by'] }}</td>
            <td>
                <div class="table-actions">
                    <a href="#" onclick=" confirm('Are you sure you want to delete this?');
                    document.getElementById('delete-data').submit();"><i class="ik ik-trash-2"></i></a>
                     <form id="delete-data" action="{{ route('vehiclesOut.destroy', $vehicleOut['id']) }}" method="POST" class="d-none">
                        @method('Delete')
                        @csrf
                    </form>
                </div>
            </td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
