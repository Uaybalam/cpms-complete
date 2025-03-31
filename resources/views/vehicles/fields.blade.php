<form id="vehicleForm" action="{{ route('vehicles.store') }}" method="POST" class="forms-sample" class="forms-sample" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputEmail3">Registration Number</label>
                <input type="text" name="registration_number" value="{{ $nextFolio }}" class="form-control"
                    id="folio" readonly placeholder="Registration Number Auto">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleInputName1">Nombre Cliente</label>
                <input type="text" name="name" value="{{ isset($customer) ? $customer->name : '' }}"
                    class="form-control" id="name" placeholder="Name">
                @if (isset($customer))
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                @endif

            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="fechaSalida">Fecha y hora de salida</label>
                <input type="datetime-local" name="salida" id="salida"
                    value="{{ isset($customer) ? $customer->fechaSalida : '' }}" class="form-control" id="salida"
                    placeholder="Fecha y hora de salida">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="phone">Telefono</label>
                <input type="tel" name="phone" value="{{ isset($customer) ? $customer->phone : '' }}"
                    class="form-control" id="phone" placeholder="Phone">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="category_id">Categoría</label>
                <select name="category_id" id="category_id" class="form-control" onchange="getCosto()" required>
                    <option value="">Seleccionar</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            @if (isset($vehicle)) {{ $vehicle->category_id == $category->id ? 'selected' : '' }} @endif>
                            {{ $category->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id ?? '' }}">
            </div>
            <input type="hidden" name="packing_charge" id="packing_charge" value="{{ $vehicle->costo ?? '' }}" required>
            <input type="hidden" name="visitas" id="visitas">
            <input type="hidden" name="vigencia" id="vigencia">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">

                <label for="exampleInputEmail3">Modelo del Vehiculo</label>
                <input type="text" name="model" class="form-control" id="model" placeholder="Vehicle model" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="plat_number">Numero de Placa del Vehiculo</label>
                <input id="plat_number" type="text" name="plat_number"
                    value="{{ isset($plate_number) ? $plate_number : '' }}" class="form-control"
                    placeholder="Vehicle Plat Number" onkeyup="buscarPlaca()" readonly required>
                <a onclick="ActivarInput()" class="btn btn-primary btn-lg active" role="button">Ingresar Manual</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail3">Color</label>
                <input name="color" class="form-control" id="Color" placeholder="Color" required>
            </div>
        </div>
    </div>
    <button type="submit" id="click"  class="btn btn-primary mr-2">Crear</button>
    <button class="btn btn-light">Cancelar</button>
</form>
<script>

let puedeHacerClic = true;

// Controlador para el formulario
document.getElementById("vehicleForm").addEventListener("submit", function(e) {
    if (!puedeHacerClic) {
        e.preventDefault(); // Detiene el envío si hay clics rápidos
        return;
    }

    const boton = document.getElementById("click");
    boton.disabled = true;
    boton.innerHTML = "Procesando...";
    puedeHacerClic = false;

    // Generar PDF antes de enviar el formulario (si es necesario)
    generarPDF();

    // Re-habilitar después de 3 segundos (incluso si falla el envío)
    setTimeout(() => {
        puedeHacerClic = true;
        boton.disabled = false;
        boton.innerHTML = "Crear";
    }, 5000);
});
    function buscarPlaca() {
        var platNumber = document.getElementById('plat_number').value;

        // Realizar solicitud AJAX solo si la longitud de la placa es mayor a cierta longitud (por ejemplo, 3 caracteres)
        if (platNumber.length >= 5) {
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

                        // Seleccionar la categoría automáticamente
                        var categoryId = response.vehiculo
                        .category_id; // Asegúrate de que la propiedad sea correcta
                        if (categoryId) {
                            var select = document.getElementById('category_id');
                            for (var i = 0; i < select.options.length; i++) {
                                if (select.options[i].value == categoryId) {
                                    select.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                        if (response.vehiculo.Visitas === 4 && response.vehiculo.category_id !== 13) {
                            document.getElementById('packing_charge').value = response.category.costo;
                            alert('Esta es la visita numero 5 del cliente, Hacer entrega de su obsequio');
                        }
                        if (response.vehiculo.Visitas === 9 && response.vehiculo.category_id !== 13) {
                            document.getElementById('packing_charge').value = 0;
                            alert('Esta es la visita numero 10 del cliente, su estadia sera Gratis');
                        } else {
                            document.getElementById('packing_charge').value = response.category.costo;
                        }


                        document.getElementById('model').value = response.vehiculo.model;
                        document.getElementById('Color').value = response.vehiculo.color;
                    } else if (response.pensionados) {

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
                        if(response.vigencia === 1)
                        {
                            alert('Pension vigente');
                        }
                        else if(response.vigencia === 0){
                            alert('Pension Pendiente de pago, favor de realizar el pago de la pension o se debera cobrar como T-REGULAR');
                        }
                        document.getElementById('model').value = response.auto ? response.auto.Modelo :
                            response.auto2.Modelo2;
                        document.getElementById('Color').value = response.auto ? response.auto.Color :
                            response.auto2.Color2;
                        document.getElementById('vigencia').value = response.vigencia;

                    }



                },
                // error: function(response) {
                //     alert('No se pudo encontrar la placa');
                // }
            });

        }
    }

    function ActivarInput() {
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
        var name = document.getElementById('name').value;
        var visitas = parseInt(document.getElementById('visitas').value, 10);
        var salida = document.getElementById('salida').value;
        var categoryId = document.getElementById('category_id').value; // Obtener el valor del select
        var vigencia = document.getElementById('vigencia').value;

        // Validar que los campos requeridos no estén vacíos
        if (!folio || !modelo || !platNumber || !categoryId) {
            alert("Por favor, complete todos los campos obligatorios.");
            return; // No procede a generar el PDF
        }

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
                salida: salida,
                category_id: categoryId,
                vigencia: vigencia,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success === true) {
                    // Manejar la respuesta del controlador y abrir el PDF
                    window.open(response.pdf_url, '_blank'); // Asumiendo que se devuelve la URL del PDF
                } else {
                    // Muestra el mensaje de error devuelto por el backend
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                // Manejar cualquier error que ocurra durante la solicitud AJAX
                console.error(error);
                alert("Hubo un error al generar el PDF. Inténtalo de nuevo.", error);
            }
        });
    }


</script>
