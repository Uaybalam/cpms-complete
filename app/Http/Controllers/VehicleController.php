<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleIn;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon; // Importar Carbon para manejo de fechas

class VehicleController extends Controller
{

    public function index(Request $request)
{
    $query = Vehicle::with(['customer:id,name', 'user:id,name', 'category:id,name']);

    // Aplicar filtro de búsqueda si existe
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->whereHas('customer', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('plat_number', 'like', '%' . $search . '%') // Filtrar por número de placa
            ->orWhereHas('category', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    $vehicles = $query->paginate(10); // Cambia el número de resultados por página si es necesario

    return view('vehicles.index', compact('vehicles'));
}



    public function create()
    {
          // Obtener el último folio registrado
          $lastFolio = Vehicle::max('registration_number');
          $nextFolio = $lastFolio ? $lastFolio + 1 : 1;

        return view('vehicles.create', ['categories' => Category::get(['id','name']),
        'customers' => Customer::get(['id','name']),
        'nextFolio' => $nextFolio]);
    }

    public function getCosto($category_id) {
        $category = Category::findOrFail($category_id);
        return response()->json(['costo' => $category->costo]);
    }
    public function store(StoreVehicleRequest $request)
{
    try {
        // Verificar si ya existe un vehículo con la misma placa en la tabla Vehicle
        $existingVehicle = Vehicle::where('plat_number', $request->plat_number)->first();

        if ($existingVehicle) {
            $latestVehicleId = $existingVehicle->id;

            // Verificar si ya existe un registro en VehicleIn asociado a ese vehículo
            $existingVehicleIn = VehicleIn::where('vehicle_id', $latestVehicleId)->first();

            // Si ya existe un registro en VehicleIn, retorna error
            if ($existingVehicleIn) {
                return redirect()->route('vehicles.create')->with('error', 'Ya existe un vehiculo registrado, por favor dale salida para poder registrar la entrada!!');
            } else {
                // Incrementa las visitas solo si no existe un registro en VehicleIn
                $existingVehicle->Visitas = ($existingVehicle->Visitas === 10) ? 1 : $existingVehicle->Visitas + 1;
                // Actualiza los datos del vehículo excluyendo los campos no necesarios
                $existingVehicle->update($request->except( 'vehicle_id', 'status'));

                // Crear un nuevo registro en VehicleIn
                VehicleIn::create(['vehicle_id' => $latestVehicleId] + $request->all());
            }
        } else {
            // Si no existe el vehículo, crea un nuevo cliente y un nuevo vehículo
            $customer = Customer::updateOrCreate(['id' => $request->customer_id], $request->except('customer_id'));
            $newVehicle = Vehicle::create($request->except('vehicle_id', 'status') + ['status' => 0, 'customer_id' => $customer->id, 'Visitas' => 1]);

            // Crear un nuevo registro en VehicleIn para el nuevo vehículo
            VehicleIn::create(['vehicle_id' => $newVehicle->id] + $request->all());
        }

        return redirect()->route('vehicles.index')->with('success', $request->vehicle_id ? 'Vehicle Updated Successfully!!' : 'Vehicle Created Successfully!!');

    } catch (\Throwable $th) {
        return redirect()->route('vehicles.create')->with('error', 'Por favor revisa que los campos estén llenos!!');
    }
}

    public function show(Vehicle $vehicle)
    {

    }

    public function edit(Vehicle $vehicle)
    {
        $now = Carbon::now(); // Obtener la hora y fecha actual
        $creationTime = Carbon::parse($vehicle->updated_at); // Obtener la hora y fecha de creación del vehículo
        $diffInMinutes = $creationTime->diffInMinutes($now); // Calcular la diferencia en minutos

        //Comprobar si han pasado menos de 15 minutos desde que se creó el vehículo

        if ($diffInMinutes > 15) {
            // Redirigir a donde quieras con un mensaje, por ejemplo a la lista de vehículos
            return redirect()->route('vehicles.index')->with('error', 'No puedes editar este vehículo después de 15 minutos desde su registro.');
         }

        $vehiculo = Vehicle::find($vehicle->id); // Obtener el vehículo por su ID
        $categories = Category::all(); // Obtener todas las categorías
        $salida = VehicleIn::where('vehicle_id', $vehicle->id)->first(); // Obtener la primera entrada de VehicleIn relacionada con el vehículo

        return view('vehicles.edit', [
            'vehiculo' => $vehiculo,
            'categories' => $categories,
            'salida' => $salida
        ]);
    }


    public function update(Request $request, Vehicle $vehicle)
    {
        // Obtener el vehículo por número de registro
        $vehiculo = Vehicle::where('registration_number', $request->input('registration_number'))->first();

        if ($vehiculo) {
            // Obtener la instancia de VehicleIn relacionada
            $vehiculoin = VehicleIn::where('vehicle_id', $vehiculo->id)->first();

            // Actualizar el vehículo
            $vehiculo->update($request->except(['_token', '_method', 'vehicle_id', 'status', 'updated_at']));

            // Actualizar la fecha de salida en VehicleIn
            if ($vehiculoin) {
                $vehiculoin->update(['salida' => $request->input('salida')]);
            }

            return redirect()->route('vehicles.index', $vehiculo->id)->with('success', 'Vehículo actualizado correctamente.');
        } else {
            return redirect()->route('vehicles.index', $vehicle->id)->with('error', 'Vehículo no existe.');
        }
    }




    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('    su    cces    s', 'Vehiculo Eliminado');

    }
}

