<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleIn;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Category;
use App\Models\Customer;

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
            $existingVehicle = Vehicle::create($request->except('registration_number', 'vehicle_id', 'status') + ['status' => 0, 'customer_id' => $customer->id, 'Visitas' => 1]);

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
        return view('vehicles.edit', compact('vehicle'), ['categories' => Category::get(['id','name']),
        'customers' => Customer::get(['id','name'])]);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        //
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehicle Deleted Successfully!!');

    }
}
