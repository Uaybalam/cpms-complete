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

    public function index()
    {
       return view('vehicles.index',
       ['vehicles' =>
       Vehicle::with(['customer:id,name', 'user:id,name', 'category:id,name'])->get()]);
    }

    public function create()
    {
        return view('vehicles.create', ['categories' => Category::get(['id','name']),
        'customers' => Customer::get(['id','name'])]);
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

        // Si existe un vehículo con la misma placa, actualiza sus datos en Vehicle
        if ($existingVehicle) {
            // Incrementa el valor de 'Visitas' en 1
            $existingVehicle->Visitas++;

            // Actualiza los datos del vehículo y excluye 'vehicle_id' y 'status'
            $existingVehicle->update($request->except('registration_number', 'vehicle_id', 'status'));
        } else {
            // Si no existe, crea un nuevo vehículo con 'Visitas' establecido en 1
            $customer = Customer::updateOrCreate(['id' => $request->customer_id], $request->except('customer_id'));
            $existingVehicle = Vehicle::create($request->except('vehicle_id', 'status') + ['status' => 0, 'customer_id' => $customer->id, 'Visitas' => 1]);

        }

        $latestVehicleId = $existingVehicle->id;

        // Verificar si ya existe un registro en VehicleIn asociado a ese vehículo
        $existingVehicleIn = VehicleIn::where('vehicle_id', $latestVehicleId)->first();

        // Si no existe un registro en VehicleIn asociado a ese vehículo, crea uno nuevo
        if (!$existingVehicleIn) {
        VehicleIn::create(['vehicle_id' => $latestVehicleId] + $request->all());

        }

        return redirect()->route('vehicles.index')->with('success', $request->vehicle_id ? 'Vehicle Updated Successfully!!' : 'Vehicle Created Successfully!!');

      } catch (\Throwable $th) {
        return redirect()->route('vehicles.create')->with('error', 'Vehicle Cannot be Create please check the inputs!!');
      }
    }

    public function show(Vehicle $vehicle)
    {

    }

    public function edit(Vehicle $vehicle)
    {

        $now = Carbon::now(); // Obtener la hora y fecha actual
        $creationTime = Carbon::parse($vehicle->created_at); // Obtener la hora y fecha de creación del vehículo
        $diffInMinutes = $creationTime->diffInMinutes($now); // Calcular la diferencia en minutos

        // Comprobar si han pasado menos de 15 minutos desde que se creó el vehículo
        if ($diffInMinutes > 15) {
            // Redirigir a donde quieras con un mensaje, por ejemplo a la lista de vehículos
            return redirect()->route('vehicles.index')->with('error', 'No puedes editar este vehículo despues 15 minutos desde su registro.');
            }

        
        $vehiculo = Vehicle::where('id', $vehicle->id)->first();
        $categories = Category::where('id', $vehiculo->category_id)->first();
        $salida = VehicleIn::where('vehicle_id', $vehicle->id)->first();
        

        return view('vehicles.edit', ['vehiculo' => $vehiculo, 'categories' => $categories , 'salida' => $salida]);
    }

    // public function update(Request $request, Vehicle $vehicle)
    //     {
    // // Valida la entrada
    // $validatedData = $request->validate([
    //     'name' => 'required|max:255',
    //     'registration_number' => 'required',
    //     'category_id' => 'required|exists:categories,id',
    //     // otros campos según sea necesario
    // ]);

    // // Actualiza el vehículo con los datos validados
    // $vehicle->update($validatedData);

    // // Redirecciona con un mensaje de éxito
    // return redirect()->route('vehicles.index')->with('sucess', 'Vehiculo Actualizado');
    // }
    public function update(Request $request, Vehicle $vehicle)
{
    // Asegurarse de que el usuario tiene permiso para actualizar el vehículo
    // if (!Gate::allows('update-vehicle', $vehicle)) {
    //     abort(403);
    // }

    // Valida la entrada
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'registration_number' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'salida' => 'required|date',
        'model' => 'required|string|max:255',
        'plat_number' => 'required|string|max:20',
        'color' => 'nullable|string|max:255',
    ]);

    try {
        // Actualiza el vehículo con los datos validados
        $vehicle->update($validatedData);

        // Redirecciona con un mensaje de éxito
        return redirect()->route('vehicles.index')->with('success', 'Vehículo actualizado correctamente.');

    } catch (\Exception $e) {
        // Manejo de la excepción en caso de error en la base de datos u otro problema
        return back()->withErrors('error', 'Error al actualizar el vehículo: ' . $e->getMessage());
    }
}


    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehiculo Eliminado');

    }
}
