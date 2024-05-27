<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleOut;
use App\Http\Requests\StoreVehicleOutRequest;
use App\Http\Requests\UpdateVehicleOutRequest;
use App\Models\User;
use App\Models\VehicleIn;

class VehicleOutController extends Controller
{

    public function index()
    {
        $vehiclesOut = VehicleOut::all();
        $users = User::all();

        $userNames = $users->pluck('name', 'id')->toArray();

        $vehicleData = $vehiclesOut->map(function ($vehicleOut) use ($userNames) {
            return [
                'id' => $vehicleOut->id,
                'registration_number' => $vehicleOut->registration_number,
                'name' => $vehicleOut->name,
                'plat_number' => $vehicleOut->plat_number,
                'created_at' => $vehicleOut->created_at,
                'created_by' => $userNames[$vehicleOut->created_by] ?? 'Unknown',
            ];
        });

        return view('vehicles_out.index', compact('vehicleData'));
    }

    public function create()
    {
        return view('vehicles_out.create', ['vehiclesIn' =>
        VehicleIn::with('vehicle:id,name,registration_number')
            ->where('status', 0)->get(['id', 'vehicle_id'])]);
    }

    public function store(StoreVehicleOutRequest $request)
    {
        $inputPlaca = $request->input('inputPlaca');
        $vehiculo = Vehicle::where('plat_number', $inputPlaca)->first();
        $vehiculoIn = VehicleIn::where('vehicle_id', $vehiculo->id)->first();
        VehicleOut::updateOrCreate(['id' => $vehiculoIn], $request->all());
        VehicleIn::where('id', $request->vehicleIn_id)->update(['status' => 1]);
        return redirect()->route('vehiclesOut.index')->with('success', 'Vehicle Out Successfully!!');
    }

    public function show(VehicleOut $vehiclesOut)
    {
        return view('vehicles_out.show', compact('VehicleOut'), ['vehicles' => Vehicle::get(['id', 'name', 'registration_number'])]);
    }

    public function edit(VehicleOut $vehiclesOut)
    {
        return view('vehicles_out.edit', compact('vehiclesOut'), ['vehicles' => Vehicle::get(['id', 'name', 'registration_number'])]);
    }

    public function update(UpdateVehicleOutRequest $request, VehicleOut $vehiclesOut)
    {
        //
    }

    public function destroy(VehicleOut $vehiclesOut)
    {
        $vehiclesOut->delete();
        return redirect()->route('vehiclesOut.index')->with('success', 'Vehicle Out Deleted Successfully!!');
    }
}
