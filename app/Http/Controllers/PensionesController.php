<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Pensionado;
use App\Models\Auto;
use Illuminate\Http\Request;
use App\Models\NuevaCaja;
use App\Models\Corte;
use App\Models\HistorialP;

class PensionesController extends Controller
{
    public function index()
    {

        $pensionCategory = Category::where('name', 'T-Pension')->first();


        if ($pensionCategory) {

            $precio = $pensionCategory->costo;
            return view('Pensionados.NuevoPensionado', compact('precio'));
        } else {
            echo "No se encontró la categoría Pension.";
        }

    }

    public function cobro(Request $request)
    {

        // Obtener el ID del pensionado desde el request
        $pensionadoId = $request->input('pensionado_id');
        $nombre = $request->input('pensionadoNombre');
        $montoCobro = $request->input('montoCobro');
        $ultimo_pago = date('Y-m-d');

        // Encontrar el pensionado por ID
        $pensionado = Pensionado::find($pensionadoId);
        $pensionado->update([
                                'ultimo_pago' => date('Y-m-d')  // Esto establece la fecha y hora actual
                            ]);
        $pensionado = HistorialP::create([
                                            'nombre'=> $nombre,
                                            'ultimo_pago'=> $ultimo_pago,
                                            'cobro'=> $montoCobro,
                                            'pensionado_id'=> $pensionadoId
                                        ]);
    return redirect()->route('pensionados.pensionados')->with('success', 'Auto actualizado correctamente');
    }

    public function mostrar($id)
    {
        $pensionado = Pensionado::find($id);
        $auto = Auto::where('pensionado_id',$id)->first();
        $array = [
            'pensionado' => $pensionado->toArray(),
            'auto' => $auto->toArray(),
        ];
        return response()->json($array);

    }
    public function historial($id)
    {
        // Buscar todos los registros de HistorialP que corresponden al pensionado_id dado
        $historial = HistorialP::where('pensionado_id', $id)->get();

        // Preparar la respuesta
        if ($historial->isEmpty()) {
            return response()->json([
                'message' => 'No hay historial disponible para este pensionado',
                'pensionado' => []
            ]);
        }

        // Devolver los datos como JSON
        return response()->json([
            'pensionado' => $historial->toArray()
        ]);
    }

    public function pensionados()
    {
        $pensionados = Pensionado::all();
        return view('Pensionados.pensionados', compact('pensionados'));
    }

    public function store(Request $request)
    {

        $placa = $request->placa;
        $placa2 = $request->placa2;

        $autoExistente = Auto::where('placa', $placa)
                             ->orWhere('placa', $placa2)
                             ->first();

        if ($autoExistente) {

            // Si se encuentra un auto con la misma placa, mostrar un mensaje de error
            $mensajeError = "La placa $placa ya está registrada en el sistema y pertenece al pensionado: " . $autoExistente->pensionado->nombre;
            // Aquí puedes manejar cómo deseas mostrar este mensaje, ya sea redirigiendo de vuelta al formulario con el mensaje o mostrándolo en algún otro lugar de tu aplicación.
            return redirect()->back()->with('error', $mensajeError);
        } else {
            $request->validate([
                'nombre' => 'required|string',
                'telefono' => 'required|numeric',
                'precio_fijo' => 'required|numeric',
                'ultimo_pago' => 'required|date',
            ]);

            // Crear el pensionado
            $pensionado = Pensionado::create($request->only(['nombre', 'telefono' , 'precio_fijo', 'name' , 'ultimo_pago']));

            // Si la placa no está duplicada, continuar con la creación del auto y la asociación con el pensionado
            $auto = new Auto([
                'placa' => $placa,
                'placa2' => $placa2,
                'Modelo' => $request->modelo,
                'Color' => $request->color,
                'Modelo2' => $request->modelo2,
                'Color2' => $request->color2,
            ]);
            $pensionado->autos()->save($auto);


            HistorialP::create([
                'nombre' => $request->nombre,
                'ultimo_pago' => date('Y-m-d'),
                'cobro' => $request->Total,
                'pensionado_id' => $pensionado->id,
            ]);

            $datos = NuevaCaja::all();
            $total = $request->input('Total');
            foreach ($datos as $datoOrigen) {
                Corte::create([
                'Cajero' => $datoOrigen->nombre,
                'Total' => $total,
                'cantidad_inicial' => $datoOrigen->cantidad_inicial,
                'Retiro' => 0
                ]);
            }

        return redirect()->route('pensionados.pensionados')->with('success', 'Auto actualizado correctamente');
        }
      }

      public function edit(Pensionado $pensionado)
      {
          return view('pensionados.edit', compact('pensionado'));
      }

      public function update(Request $request, $id)
      {

        $auto = Auto::where('pensionado_id', $id)->first();
        $pensionados = Pensionado::where('id', $id)->first();

            $pensionados->update([
                'nombre' => $request->nombre
            ]);
              // Actualizar los datos del auto
              $auto->update([
                  'placa' => $request->placa1,
                  'Color' => $request->color1,
                  'Modelo' => $request->modelo1,
                  'placa2' => $request->placa2,
                  'Color2' => $request->color2,
                  'Modelo2' => $request->modelo2,

              ]);

              // Redireccionar con un mensaje de éxito
              return redirect()->route('pensionados.pensionados')->with('success', 'Auto actualizado correctamente');

      }


      public function destroy(Pensionado $pensionado)
      {
          $pensionado->delete();
          return redirect()->route('pensionados.pensionados')->with('success', 'Pensionado eliminado exitosamente.');
      }

}
