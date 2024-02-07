<?php

namespace App\Http\Controllers;

use App\Models\Corte;
use App\Models\Factura;
use App\Models\historial;
use Illuminate\Support\Facades\DB;
use App\Models\NuevaCaja;
use App\Models\Price;
use App\Models\SegundaTabla;
use App\Models\Vehicle;
use App\Models\VehicleIn;
use Illuminate\Http\Request;

class NuevaCajaController extends Controller
{
    public function mostrarFactura($id)
    {
            $factura = Factura::find($id);

        // Crear un nuevo archivo PDF desde el contenido almacenado en la base de datos
            $newPdfPath = storage_path('app\public\nuevo_factura.pdf');
            file_put_contents($newPdfPath, $factura->pdf_content);
        // Decodificar el contenido del PDF
        $decodedPdfContent = utf8_decode($factura->pdf_content);

        // Devuelve el contenido del PDF decodificado como respuesta HTTP
        return response($decodedPdfContent)
            ->header('Content-Type', 'application/pdf');
    }

    public function historial()
    {
        $registros = Factura::all();
        return view('historial_venta', ['registros' => $registros]);
    }
    public function abrirModal()
    {
        $registros = NuevaCaja::all(); // Obtén todos los registros de la tabla

        return view('welcome', ['registros' => $registros]);
    }

    // Puedes agregar un método para guardar los datos en la base de datos
    public function guardarDatos(Request $request)
    {

        $datos = $request->validate([
            'nombre' => 'required|string',
            'cantidad_inicial' => 'required|numeric',
        ]);

        NuevaCaja::create($datos);

        $retiro = 'Fondo';
        $cantidad = NuevaCaja::first();
        Corte::create([
            'Cajero' => $retiro,
            'Total' => 0,
            'cantidad_inicial' => $cantidad->cantidad_inicial
        ]);
        return redirect()->back()->with('success', 'Datos guardados correctamente.');
    }

    public function abrirparcial()
    {
        $registros = Corte::all();
        $suma = Corte::sum('Total');
        $cantidad = NuevaCaja::first();

        return view('corte_parcial', [
            'registros' => $registros,
            'sumaTotal' => $suma,
            'cantidad' => $cantidad
        ]);
    }



    public function Venta()
    {
        $registros = NuevaCaja::all();
        $resultados = DB::table('vehicle_ins')
        ->join('vehicles', 'vehicle_ins.vehicle_id', '=', 'vehicles.id')
        ->select('vehicle_ins.*', 'vehicles.plat_number')
        ->get();

        return view('caja', ['registros' => $registros] , ['Vehicle' => $resultados]);
    }

    public function guardarVenta(Request $request)
    {
        $datos = NuevaCaja::all();
        $total = $request->input('total');
        foreach ($datos as $datoOrigen) {
            Corte::create([
                'Cajero' => $datoOrigen->nombre,
                'Total' => $total,
                'cantidad_inicial' => $datoOrigen->cantidad_inicial,
            ]);
        }

        return response()->json(['success' => true, 'redirect' => url('/Caja')]);

    }
    public function retiroParcial()
    {
        $retiro = 'retiro';
        $cantidad = NuevaCaja::first();
        Corte::create([
            'Cajero' => $retiro,
            'Total' => 0,
            'cantidad_inicial' => 0,
            'Retiro'=>$cantidad->cantidad_inicial,
        ]);


        $cantidad->cantidad_inicial = 0;
        $cantidad->save();
        return response()->json(['success' => true, 'redirect' => url('/Ventas')]);
    }

    public function cierreCaja()
    {
        $retiro = 'Cierre';
        $suma = Corte::sum('Total');
        $cantidad = NuevaCaja::first();
        Corte::create([
            'Cajero' => $retiro,
            'Total' => 0,
            'cantidad_inicial' => $cantidad->cantidad_inicial,
            'Retiro'=>$suma,
        ]);

        $primera = Corte::all();
        foreach ($primera as $registro) {
            historial::create([
                'Cajero' => $registro->Cajero,
                'Total' => $registro->Total,
                'cantidad_inicial' => $registro->cantidad_inicial,
                'Retiro' => $registro->Retiro,
            ]);
        }


        Corte::truncate();
        NuevaCaja::truncate();
        return response()->json(['success' => true, 'redirect' => url('/Ventas')]);
    }

    public function obtenerdatos($id)
    {

        $vehiculo = Vehicle::find($id);
        $vehiculoIn = VehicleIn::where('vehicle_id', $id)->first();


        // Si el vehículo no se encuentra
        if (!$vehiculo || !$vehiculoIn) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }
        $array = [
            'vehiculo' => $vehiculo->toArray(),
            'vehiculoIn' => $vehiculoIn->toArray(),
        ];
        // Devuelve los datos en formato JSON
        return response()->json($array);

    }
}
