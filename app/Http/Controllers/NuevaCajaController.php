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
       
        $registros = Factura::where('folio', $folio)->get();
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
        // Validar los datos de entrada
        $datos = $request->validate([
            'nombre' => 'required|string',
            'cantidad_inicial' => 'required|numeric',
        ]);

        try {
            // Verificar si ya existe algún dato en la tabla NuevaCaja
            if (NuevaCaja::count() > 0) {
                // Si ya existen registros, redirigir con mensaje de error
                return redirect()->back()->with('error', 'Ya existe una caja abierta. No se pueden agregar más registros.');
            }

            // Crear una nueva caja
            NuevaCaja::create($datos);

            // Crear un registro en Corte
            Corte::create([
                'Cajero' => 'Fondo',
                'Total' => 0,
                'cantidad_inicial' => $datos['cantidad_inicial'],
                'Retiro' => 0
            ]);

            // Redirigir con mensaje de éxito
            return redirect()->back()->with('success', 'Datos guardados correctamente.');
        } catch (\Exception $e) {
            // Manejar cualquier excepción
            return redirect()->back()->with('error', 'Ocurrió un error al guardar los datos.');
        }
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

        $subtotal = $request->post('subtotal');

        if($vehiculo->category_id === 13){
            if($subtotal === 'No Vigente'){
            foreach ($datos as $datoOrigen) {
                Corte::create([
                    'Cajero' => $datoOrigen->nombre,
                    'Placa' => $inputPlaca,
                    'Total' => $total,
                    'Estatus' => $subtotal,
                    'cantidad_inicial' => $datoOrigen->cantidad_inicial,
                    'Retiro' => 0
                ]);
            }
        }
        else if($subtotal === 'Vigente'){
            foreach ($datos as $datoOrigen) {
                Corte::create([
                    'Cajero' => $datoOrigen->nombre,
                    'Placa' => $inputPlaca,
                    'Total' => $total,
                    'Estatus' => $subtotal,
                    'cantidad_inicial' => $datoOrigen->cantidad_inicial,
                    'Retiro' => 0
                ]);
            }
        }
    }
        else if($vehiculo->category_id === 9 || $vehiculo->category_id === 10 || $vehiculo->category_id === 11 || $vehiculo->category_id === 12){
            if($total === '0.00'){
                $condonacion = 'Condonacion';
                foreach ($datos as $datoOrigen) {
                    Corte::create([
                        'Cajero' => $datoOrigen->nombre,
                        'Placa' => $inputPlaca,
                        'Total' => $total,
                        'Estatus' => $condonacion,
                        'cantidad_inicial' => $datoOrigen->cantidad_inicial,
                        'Retiro' => 0
                    ]);
                }
            }
            else if($vehiculo->Visitas === 5){
                $condonacion = 'Obsequio';
                foreach ($datos as $datoOrigen) {
                    Corte::create([
                        'Cajero' => $datoOrigen->nombre,
                        'Placa' => $inputPlaca,
                        'Total' => $total,
                        'Estatus' => $condonacion,
                        'cantidad_inicial' => $datoOrigen->cantidad_inicial,
                        'Retiro' => 0
                    ]);
                }
            }
            else{
                foreach ($datos as $datoOrigen) {
                    Corte::create([
                        'Cajero' => $datoOrigen->nombre,
                        'Placa' => $inputPlaca,
                        'Total' => $total,
                        'Estatus' => '',
                        'cantidad_inicial' => $datoOrigen->cantidad_inicial,
                        'Retiro' => 0
                    ]);
                }
            }

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
            'Estatus' => '' ,
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
            'Estatus' => '',
            'cantidad_inicial' => $cantidad->cantidad_inicial,
            'Retiro'=>$suma,
        ]);

        $primera = Corte::all();
        foreach ($primera as $registro) {
            historial::create([
                'Cajero' => $registro->Cajero,
                'Placa' => $registro->Placa,
                'Total' => $registro->Total,
                'Estatus' => $registro->Estatus,
                'cantidad_inicial' => $registro->cantidad_inicial,
                'Retiro' => $registro->Retiro,
                'created_at' => $registro->created_at,
            ]);
        }


        Corte::truncate();
        NuevaCaja::truncate();
        return response()->json(['success' => true, 'redirect' => url('/Ventas')]);
    }

    public function obtenerDatosPlaca($placa)
    {
        $sacarpensionado = Auto::where('placa', $placa)->orWhere('placa2', $placa)->first();
        if($sacarpensionado)
         {
            $pensionados =  Pensionado::where('id', $sacarpensionado-> pensionado_id)->first();
            $fechaActual = Carbon::now();

            // Obtener la fecha del ultimo_pago
            $fechaUltimoPago = Carbon::parse($pensionados->ultimo_pago);

            $fechacobro = $fechaUltimoPago->copy()->addDays(35);

            // Calcular la diferencia en días
            $diasDiferencia = $fechaUltimoPago->diffInDays($fechaActual);
            if($diasDiferencia <= 35)
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
                 $totalAPagar = 0;
                 $vigencia = 'Vigente';
                return response()->json([
                    'vehiculo' => $vehiculo,
                    'fechaEntrada' => $fechaEntrada,
                    'fechaSalida' => $fechaSalida,
                    'diferenciaHoras' => $diferenciaHoras,
                    'totalAPagar' => $totalAPagar,
                    'vehiculoIn' => $vehiculoin,
                    'pensionados' => $pensionados,
                    'vigencia' => $vigencia
                ]);
            }
            else
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


                if($vehiculoin->created_at >= $fechacobro)
                {
                // Eliminar la parte de la zona horaria
                $fechaEntradaString = str_replace(['.0', ' America/Mexico_City (-06:00)'], '', $vehiculoin->created_at);
                }
                else if($vehiculoin->created_at <= $fechacobro){

                    $fechaEntradaString = str_replace(['.0', ' America/Mexico_City (-06:00)'], '', $fechacobro);
                }
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
                $category = 'T-REGULAR';

                if (!isset($tarifa->$category)) {
                    return response()->json(['error' => 'Categoría de tarifa no encontrada'], 404);
                }

                // Calculando el total a pagar basado en la categoría del vehículo
                $totalAPagar = $tarifa->$category;
                $vigencia = 'No Vigente';

                return response()->json([
                    'vehiculo' => $vehiculo,
                    'fechaEntrada' => $fechaEntrada,
                    'fechaSalida' => $fechaSalida,
                    'diferenciaHoras' => $diferenciaHoras,
                    'totalAPagar' => $totalAPagar,
                    'vehiculoIn' => $vehiculoin,
                    'pensionados' => $pensionados,
                    'vigencia' => $vigencia
                ]);

            }
         }

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

        if ($diferenciaHoras == 0) {
            $tarifa = precios::where('horas', '>', 0)
                            ->orderBy('horas', 'asc')
                            ->first();
        } else {
            if ($categoria->id === 11) {
                if ($diferenciaHoras == 24) {
                    // Para taxista y 24 horas, usar >=
                    $tarifa = precios::where('horas', '>=', $diferenciaHoras)
                                    ->orderBy('horas', 'asc')
                                    ->first();
                } else {
                    // Para taxista y diferente de 24 horas, usar <=
                    $tarifa = precios::where('horas', '<=', $diferenciaHoras)
                                    ->orderBy('horas', 'desc')
                                    ->first();
                }
            } else {
                // Para otras categorías, usar >=
                $tarifa = precios::where('horas', '>=', $diferenciaHoras)
                                ->orderBy('horas', 'asc')
                                ->first();
            }

            // Si no se encontró una tarifa con horas mayores, usamos la última tarifa disponible
            if (!$tarifa) {
                $tarifa = precios::orderBy('horas', 'desc')->first();
            }
        }

        // Asegurándose de que la categoría existe en la tarifa
        $category = $categoria->name;

        if (!isset($tarifa->$category)) {
            return response()->json(['error' => 'Categoría de tarifa no encontrada'], 404);
        }

        if($vehiculo->Visitas === 10){
            $totalAPagar = 0;
        }
        else{
            // Calculando el total a pagar basado en la categoría del vehículo
            $totalAPagar = $tarifa->$category;
        }


        return response()->json([
            'vehiculo' => $vehiculo,
            'fechaEntrada' => $fechaEntrada,
            'fechaSalida' => $fechaSalida,
            'diferenciaHoras' => $diferenciaHoras,
            'totalAPagar' => $totalAPagar
        ]);


    }

    // public function obtenerdatos($platNumber)
    // {
    //     $sacarpensionado = Auto::where('placa', $platNumber)->orWhere('placa2', $platNumber)->first();
    //     if($sacarpensionado)
    //     {
    //         $vehiculo = Vehicle::where('plat_number', $platNumber)->first();

    //         if (!$vehiculo) {
    //             return response()->json(['error' => 'Vehículo no encontrado'], 404);
    //         }

    //         $vehiculoIn = VehicleIn::where('vehicle_id', $vehiculo->id)->first();

    //         if (!$vehiculoIn) {
    //             return response()->json(['error' => 'Entrada de vehículo no encontrada'], 404);
    //         }
    //         $pensionados =  Pensionado::where('id', $sacarpensionado-> pensionado_id)->first();
    //         $array = [
    //             'vehiculo' => $vehiculo->toArray(),
    //             'vehiculoIn' => $vehiculoIn->toArray(),
    //             'pensionados' => $pensionados->toArray(),
    //         ];
    //         return response()->json($array);
    //     }
    //     else
    //     {
    //     $vehiculo = Vehicle::where('plat_number', $platNumber)->first();

    //     if (!$vehiculo) {
    //         return response()->json(['error' => 'Vehículo no encontrado'], 404);
    //     }

    //     $vehiculoIn = VehicleIn::where('vehicle_id', $vehiculo->id)->first();

    //     if (!$vehiculoIn) {
    //         return response()->json(['error' => 'Entrada de vehículo no encontrada'], 404);
    //     }



    //     $array = [
    //         'vehiculo' => $vehiculo->toArray(),
    //         'vehiculoIn' => $vehiculoIn->toArray(),
    //     ];




    //     return response()->json($array);
    // }
    // }

}
