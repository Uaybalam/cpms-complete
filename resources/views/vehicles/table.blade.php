<table id="data_table" class="table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Reg #</th>
            <th>Categoria</th>
            <th>Cliente</th>
            <th>Num. Placa</th>
            <th>Creado el</th>
            <th class="nosort">Operacion</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($vehicles as $key => $vehicle)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $vehicle->registration_number }}</td>
            <td>{{ $vehicle->category->name }}</td>
            <td>{{ $vehicle->name }}</td>
            <td>{{ $vehicle->plat_number }}</td>
            <td>{{ $vehicle->created_at->format('Y/m/d') }}</td>


            <td>
                <div class="btn-group table-actions">
                    <a href="#" data-toggle="modal" data-target="#show{{ $key }}"><i class="ik ik-eye"></i></a>
                    <a href="{{ route('vehicles.edit', $vehicle->id) }}"><i class="ik ik-edit-2"></i></a>
                    @if(auth()->check() && auth()->user()->role == 'Administrador')
                        <a href="#"  data-toggle="modal" data-target="#delete{{ $key }}"><i class="ik ik-trash-2"></i></a>
                    @else

                            <div class="container mt-4">
                             <div class="alert alert-danger" role="alert">
                                 No tienes permiso de estar aqui
                            </div>
                                <button type="button" class="btn btn-danger" onclick="location.href='{{route('home')}}';">Regresar a home</button>
                            </div>

                    @endif
                </div>
            </td>

            <td></td>
        </tr>
        @include('vehicles.show')
        @include('vehicles.delete')
        @endforeach
    </tbody>
</table>
