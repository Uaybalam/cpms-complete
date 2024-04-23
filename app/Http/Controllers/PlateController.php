<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Auto;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Pensionado;

class PlateController extends Controller
{
    public function buscarPlaca($platNumber)
    {
        $vehiculo = Vehicle::where('plat_number', $platNumber)->first();

        if($vehiculo)
        {
            $customer = Customer::where('id', $vehiculo->customer_id)->first();

            $category = Category::where('id', $vehiculo->category_id)->first();
            $array = [
                'vehiculo' => $vehiculo ? $vehiculo->toArray() : null,
                'customer' => $customer ? $customer->toArray() : null,
                'category' => $category ? $category->toArray() : null,
            ];
            return response()->json($array);
        }

        else
        {
            $sacarpensionado = Auto::where('placa', $platNumber)->orWhere('placa2', $platNumber)->first();


            $auto = Auto::where('placa', $platNumber)->select('Modelo', 'Color')->first();

            $auto2 = Auto::where('placa2', $platNumber)->select('Modelo2', 'Color2')->first();

            $pensionados =  Pensionado::where('id', $sacarpensionado-> pensionado_id)->first();
            $array = [
                'auto' => $auto ? $auto->toArray() : null,
                'auto2' => $auto2 ? $auto2->toArray() : null,
                'pensionados' => $pensionados->toArray(),
            ];
            return response()->json($array);
        }





    }
}
