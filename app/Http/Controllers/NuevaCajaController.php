<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
use App\Models\Auto;
use App\Models\Pensionado;
use App\Models\User;
use App\Models\VehicleOut;
use App\Models\precios;
use App\Models\Category;


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
        return view('caja.historial_venta', ['registros' => $registros]);
    }
    public function abrirModal()
    {
        $registros = NuevaCaja::all(); // Obtén todos los registros de la tabla

        return view('caja.welcome', ['registros' => $registros]);
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
            'cantidad_inicial' => $cantidad->cantidad_inicial,
            'Retiro' => 0
        ]);
        return redirect()->back()->with('success', 'Datos guardados correctamente.');
    }

    public function abrirparcial()
    {
        $registros = Corte::all();
        $suma = Corte::sum('Total');
        $cantidad = NuevaCaja::first();

        return view('caja.corte_parcial', [
            'registros' => $registros,
            'sumaTotal' => $suma,
            'cantidad' => $cantidad
        ]);
    }



    public function Venta()
    {
        $registros = NuevaCaja::all();


        return view('caja.caja', ['registros' => $registros]);
    }

    public function guardarVenta(Request $request)
    {

        $inputPlaca = $request->input('placa');
        $vehiculo = Vehicle::where('plat_number', $inputPlaca)->first();
        $user = User::where('id', $vehiculo->created_by)->first();

         // Crear una nueva instancia en VehicleOut con los datos seleccionados de VehicleIn
        $vehiculoOut = new VehicleOut();
        $vehiculoOut->registration_number = $vehiculo->registration_number;
        $vehiculoOut->plat_number = $vehiculo->plat_number;
        $vehiculoOut->name = $vehiculo->name;
        $vehiculoOut->created_by = $user->name; // Ejemplo de cambio de nombre de columna

        $vehiculoOut->save();
        $vehiculoIn = VehicleIn::where('vehicle_id', $vehiculo->id)->first();
        $vehiculoIn->delete();


        $datos = NuevaCaja::all();
        $total = $request->input('total');
        foreach ($datos as $datoOrigen) {
            Corte::create([
                'Cajero' => $datoOrigen->nombre,
                'Placa' => $inputPlaca,
                'Total' => $total,
                'cantidad_inicial' => $datoOrigen->cantidad_inicial,
                'Retiro' => 0
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
            'Placa' => 'NA',
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

    public function obtenerDatosPlaca($placa)
    {
        $vehiculo = Vehicle::where('plat_number', $placa)->first();
        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        $vehiculoin = VehicleIn::where('vehicle_id', $vehiculo->id)->first();
        if (!$vehiculoin) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        $categoria = Category::where('id', $vehiculo->category_id)->first();
        if (!$categoria) {
            return response()->json(['error' => 'Categoria no encontrada'], 404);
        }

        // Eliminar la parte de la zona horaria
        $fechaEntradaString = str_replace(['.0', ' America/Mexico_City (-06:00)'], '', $vehiculoin->created_at);

        // Convertir la fecha
        $fechaEntrada = Carbon::createFromFormat('Y-m-d H:i:s', $fechaEntradaString);
        $fechaSalida = Carbon::now();
        $diferenciaHoras = $fechaEntrada->diffInHours($fechaSalida);

        // Obteniendo la tarifa adecuada según las horas
        $tarifa = precios::where('horas', '>=', $diferenciaHoras)
                        ->orderBy('horas', 'asc')
                        ->first();

        // Si no se encontró una tarifa con horas mayores, usamos la última tarifa disponible
        if (!$tarifa) {
            $tarifa = precios::orderBy('horas', 'desc')->first();
        }

        // Asegurándose de que la categoría existe en la tarifa
        $category = $categoria->name;

        if (!isset($tarifa->$category)) {
            return response()->json(['error' => 'Categoría de tarifa no encontrada'], 404);
        }

        // Calculando el total a pagar basado en la categoría del vehículo
        $totalAPagar = $tarifa->$category;

        return response()->json([
            'vehiculo' => $vehiculo,
            'fechaEntrada' => $fechaEntrada,
            'fechaSalida' => $fechaSalida,
            'diferenciaHoras' => $diferenciaHoras,
            'totalAPagar' => $totalAPagar
        ]);
    }

    public function obtenerdatos($platNumber)
    {
        $sacarpensionado = Auto::where('placa', $platNumber)->orWhere('placa2', $platNumber)->first();
        if($sacarpensionado)
        {
            $vehiculo = Vehicle::where('plat_number', $platNumber)->first();

            if (!$vehiculo) {
                return response()->json(['error' => 'Vehículo no encontrado'], 404);
            }

            $vehiculoIn = VehicleIn::where('vehicle_id', $vehiculo->id)->first();

            if (!$vehiculoIn) {
                return response()->json(['error' => 'Entrada de vehículo no encontrada'], 404);
            }
            $pensionados =  Pensionado::where('id', $sacarpensionado-> pensionado_id)->first();
            $array = [
                'vehiculo' => $vehiculo->toArray(),
                'vehiculoIn' => $vehiculoIn->toArray(),
                'pensionados' => $pensionados->toArray(),
            ];
            return response()->json($array);
        }
        else
        {
        $vehiculo = Vehicle::where('plat_number', $platNumber)->first();

        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        $vehiculoIn = VehicleIn::where('vehicle_id', $vehiculo->id)->first();

        if (!$vehiculoIn) {
            return response()->json(['error' => 'Entrada de vehículo no encontrada'], 404);
        }



        $array = [
            'vehiculo' => $vehiculo->toArray(),
            'vehiculoIn' => $vehiculoIn->toArray(),
        ];




        return response()->json($array);
    }
    }

}
