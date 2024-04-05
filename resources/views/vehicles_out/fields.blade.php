<form action="{{ route('vehiclesOut.store') }}" class="forms-sample" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputName1">Select Vehicle</label>
                <select name="vehicleIn_id" class="form-control">
                <option value="">Seleccionar Vehiculo</option>
                    @foreach ($vehiclesIn as $vehicleIn)
                        <option value="{{ $vehicleIn->id }}" @if (isset($vehiclesOut))
                            {{ $vehiclesOut->vehicle_id == $vehicleIn->vehicle->id ? 'selected' : '' }}
                    @endif>
                    {{ $vehicleIn->vehicle->name .' - '. $vehicleIn->vehicle->registration_number }}</option>
                    @endforeach
                </select>
                @if (isset($vehiclesOut))
                    <input type="hidden" name="vehiclesOut_id" value="{{ $vehiclesOut->id }}">
                @endif
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mr-2">Crear</button>
    <button class="btn btn-light">Cancelar</button>
</form>
