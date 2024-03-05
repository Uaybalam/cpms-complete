<form action="{{ route('vehicles.store') }}"  class="forms-sample" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputEmail3">Registration Number</label>
                <input type="text" name="registration_number"
                    value="{{ isset($vehicle) ? $vehicle->registration_number : '' }}" class="form-control"
                    id="folio" readonly placeholder="Registration Number Auto">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputName1">Nombre Cliente</label>
                <input type="text" name="name" value="{{ isset($customer) ? $customer->name : '' }}" class="form-control" id="name" placeholder="Name">
                @if (isset($customer))
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                @endif

            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{-- <label for="exampleInputName1">Nombre del Cliente</label>
                <select name="customer_id" class="form-control">
                    <option value="">Select</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @if (isset($vehicle))
                            {{ $vehicle->customer_id == $customer->id ? 'selected' : '' }}
                    @endif>
                    {{ $customer->name }}</option>
                    @endforeach
                </select> --}}
                <label for="email">Email</label>
                <input type="email" name="email" value="{{ isset($customer) ? $customer->email : '' }}" class="form-control" id="email" placeholder="Email">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="phone">Telefono</label>
                <input type="tel" name="phone" value="{{ isset($customer) ? $customer->phone : '' }}" class="form-control" id="phone" placeholder="Phone">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="category_id">Categoría</label>
                <select name="category_id" id="category_id" class="form-control" onchange="getCosto()">
                    <option value="">Seleccionar</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @if (isset($vehicle))
                            {{ $vehicle->category_id == $category->id ? 'selected' : '' }}
                        @endif>
                        {{ $category->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id ?? '' }}">
            </div>
                <input type="hidden" name="packing_charge" id="packing_charge" value="{{ $vehicle->costo ?? '' }}">

            </div>
        </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{-- <label for="exampleInputEmail3">Duracion de Estacionamiento (Dias)</label>
                <input type="number" name="duration" value="{{ isset($vehicle) ? $vehicle->duration : '' }}"
                    class="form-control" id="duracion" placeholder="Parking Duration"> --}}
                <label for="exampleInputEmail3">Modelo del Vehiculo</label>
                <input  type="text" name="model" class="form-control" id="model" placeholder="Vehicle model">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{-- <label for="exampleInputEmail3">Costo</label>
                <input type="number" min="1" name="packing_charge" value="{{ isset($vehicle) ? $vehicle->packing_charge : '' }}"
                    class="form-control" id="exampleInputEmail3" placeholder="Parking Charges"> --}}
                <label for="exampleInputEmail3">Numero de Placa del Vehiculo</label>
                <input id="plat_number" type="text" name="plat_number" value="{{ isset($plate_number) ? $plate_number : '' }}"
                    class="form-control" placeholder="Vehicle Plat Number"  readonly>
                <a onclick="ActivarInput()" class="btn btn-primary btn-lg active" role="button">Enlace principal</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail3">Color</label>
                <input  name="color"
                class="form-control" id="Color" placeholder="Color">
            </div>
        </div>
    </div>
    <button type="submit" onclick="generarPDF()" class="btn btn-primary mr-2">Crear</button>
    <button class="btn btn-light">Cancelar</button>
</form>
<script>

    function ActivarInput()
    {
        document.getElementById('plat_number').readOnly = false;

    }
    function getCosto() {
        var category_id = document.getElementById('category_id').value;
        if (category_id !== '') {
            // Realizar una solicitud AJAX para obtener el costo asociado a la categoría seleccionada
            $.ajax({
                url: '/get-costo/' + category_id,
                type: 'GET',
                success: function(response) {
                    // Actualizar el valor del campo de costo en el formulario
                    document.getElementById('packing_charge').value = response.costo;
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    }
    function generarPDF() {
        // Obtener los valores de los campos del formulario
        var Color = document.getElementById('Color').value;
        var folio = document.getElementById('folio').value;
        var modelo = document.getElementById('model').value;
        var platNumber = document.getElementById('plat_number').value;
        var name =  document.getElementById('name').value;


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
</script>
