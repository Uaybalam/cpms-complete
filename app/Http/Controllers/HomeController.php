<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleIn;


class HomeController extends Controller
{

    public function index()
{
    $vehicles = new Vehicle();
    $vehicles_in = new VehicleIn();

    $duration = [];
    foreach ($vehicles->get() as $vehicle) {
        $duration[] = $vehicle->duration * $vehicle->packing_charge;
    }

    $total_amount = array_sum($duration);
    $total_vehicles = $vehicles->count();
    $total_vehicle_in = $vehicles_in->where('status', 0)->orWhere('status', 1)->whereDate('created_at', now()->format('Y-m-d'))->count();
    $total_vehicle_out = $vehicles_in->where('status', 1)->whereDate('created_at', now()->format('Y-m-d'))->count();

    // Paginar los resultados de vehiclesIn con 10 elementos por página (puedes ajustar este valor)
    $vehiclesIn = VehicleIn::with(['vehicle:id,name,registration_number,plat_number,model,Visitas', 'user:id,name'])
        ->where('status', 0)
        ->paginate(10);

    return view('home', [
        'vehicles' => $vehicles->get(),
        'vehiclesIn' => $vehiclesIn,
        'total_amount' => $total_amount,
        'total_vehicle_in' => $total_vehicle_in,
        'total_vehicle_out' => $total_vehicle_out,
        'total_vehicles' => $total_vehicles
    ]);
}



}

