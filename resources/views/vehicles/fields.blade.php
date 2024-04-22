<form action="{{ route('vehicles.store') }}" method="POST" class="forms-sample"  class="forms-sample" method="POST">
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
                <label for="fechaSalida">Fecha y hora de salida</label>
                <input type="datetime-local" name="salida" value="{{ isset($customer) ? $customer->fechaSalida : '' }}" class="form-control" id="salida" placeholder="Fecha y hora de salida">
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
                <input type="hidden" name="visitas" id="visitas">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">

                <label for="exampleInputEmail3">Modelo del Vehiculo</label>
                <input  type="text" name="model" class="form-control" id="model" placeholder="Vehicle model">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="plat_number">Numero de Placa del Vehiculo</label>
                <input id="plat_number" type="text" name="plat_number" value="{{ isset($plate_number) ? $plate_number : '' }}"
                    class="form-control" placeholder="Vehicle Plat Number" onkeyup="buscarPlaca()" readonly>
                <a onclick="ActivarInput()" class="btn btn-primary btn-lg active" role="button">Ingresar Manual</a>
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
    function buscarPlaca() {
        var platNumber = document.getElementById('plat_number').value;

        // Realizar solicitud AJAX solo si la longitud de la placa es mayor a cierta longitud (por ejemplo, 3 caracteres)
        if (platNumber.length >= 7) {
            // Realizar solicitud AJAX
            $.ajax({
                type: 'GET',
                url: '/buscar-placa/' + platNumber,
                success: function(response) {
            // Llenar los campos del formulario con los datos obtenidos
            if (response.vehiculo) {
                document.getElementById('name').value = response.customer.name;
                document.getElementById('phone').value = response.customer.phone;
                document.getElementById('visitas').value = response.vehiculo.Visitas;

                if(response.vehiculo.Visitas === 4)
                {
                    document.getElementById('packing_charge').value = response.category.costo;
                    alert('Esta es la visita numero 5 del cliente, Hacer entrega de su obsequio');
                }
                if(response.vehiculo.Visitas === 9)
                {
                    document.getElementById('packing_charge').value = 0;
                    alert('Esta es la visita numero 10 del cliente, su estadia sera Gratis');
                }
                else
                {
                document.getElementById('packing_charge').value = response.category.costo;
                }
                // Seleccionar la categoría automáticamente
                var categoryId = response.vehiculo.category_id; // Asegúrate de que la propiedad sea correcta
                if (categoryId) {
                 var select = document.getElementById('category_id');
                    for (var i = 0; i < select.options.length; i++) {
                        if (select.options[i].value == categoryId) {
                            select.selectedIndex = i;
                            break;
                        }
                    }
                }

                document.getElementById('model').value = response.vehiculo.model;
                document.getElementById('Color').value = response.vehiculo.color;
            } else if (response.pensionados)
            {

                document.getElementById('name').value = response.pensionados.nombre;
                document.getElementById('phone').value = response.pensionados.Telefono;
                document.getElementById('packing_charge').value = 0;
                // Seleccionar la categoría automáticamente
                var categoryId = 13; // Asegúrate de que la propiedad sea correcta
                if (categoryId) {
                    var select = document.getElementById('category_id');

                    for (var i = 0; i < select.options.length; i++) {
                        if (select.options[i].value == categoryId) {
                            select.selectedIndex = i;

                            break;
                        }
                    }
                }

                document.getElementById('model').value = response.auto ? response.auto.Modelo : response.auto2.Modelo2;
                document.getElementById('Color').value = response.auto ? response.auto.Color : response.auto2.Color2;

            }



                },
                error: function(response) {
                    alert('No se pudo encontrar la placa');
                }
            });

        }
    }

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
        var visitas = parseInt(document.getElementById('visitas').value, 10);



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
                visitas: visitas,
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
