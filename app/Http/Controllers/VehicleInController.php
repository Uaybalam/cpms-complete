<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleIn;
use App\Http\Requests\StoreVehicleInRequest;
use App\Http\Requests\UpdateVehicleInRequest;
use Illuminate\Http\Request;

class VehicleInController extends Controller
{

    public function index(Request $request)
    {
        $query = VehicleIn::with(['vehicle:id,name,model,registration_number,plat_number,Visitas', 'user:id,name'])
            ->where('status', 0);

        // Aplicar filtro de búsqueda si existe
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('vehicle', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('plat_number', 'like', '%' . $search . '%');
                })
                ->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $vehiclesIn = $query->paginate(10); // Paginación para los vehículos con status 0

        // Consulta para el historial (sin filtro de búsqueda)
        $vehiclesIn_History = VehicleIn::with(['vehicle:id,name,registration_number,plat_number', 'user:id,name'])
            ->where('status', 1)
            ->paginate(10);

        return view('vehicles_in.index', compact('vehiclesIn', 'vehiclesIn_History'));
    }


    public function create()
    {
        return view('vehicles_in.create',['vehicles' => Vehicle::get(['id','name', 'registration_number'])]);
    }

    public function store(StoreVehicleInRequest $request)
    {
        VehicleIn::updateOrCreate(['id' => $request->vehiclesIn_id], $request->all());

        return redirect()->route('vehiclesIn.index')->with('success', 'Vehicle Entered Successfully!!');
    }

    public function show(VehicleIn $vehiclesIn)
    {
        return view('vehicles_in.show',compact('vehicleIn'), ['vehicles' => Vehicle::get(['id','name', 'registration_number'])]);
    }

    public function edit(VehicleIn $vehiclesIn)
    {
        return view('vehicles_in.edit', compact('vehiclesIn'), ['vehicles' => Vehicle::get(['id','name', 'registration_number'])]);
    }

    public function update(UpdateVehicleInRequest $request, VehicleIn $vehiclesIn)
    {
        //
    }

    public function destroy(VehicleIn $vehiclesIn)
    {
        $vehiclesIn->delete();
        return redirect()->route('vehiclesIn.index')->with('success', 'Vehicle In Deleted Successfully!!');
    }
}
