<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pensionado;
use App\Models\Auto;
use Illuminate\Http\Request;

class PensionesController extends Controller
{
    public function index()
    {
        $pensionados = Pensionado::all();
        return view('Pensionados.NuevoPensionado', compact('pensionados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'precio_fijo' => 'required|numeric',
            'ultimo_pago' => 'required|date',
        ]);

        // Crear el pensionado
        $pensionado = Pensionado::create($request->only(['nombre', 'precio_fijo', 'ultimo_pago']));

        // Asociar un auto si se proporcionó la placa
        if ($request->has('placa')) {
            $auto = new Auto(['placa' => $request->placa]);
            $pensionado->autos()->save($auto);
        }


        return redirect()->route('Pensionados.pensionados');
    }

    public function verificarPago(Pensionado $pensionado)
    {
        // Lógica para verificar si el pensionado ha pagado dentro del período de colchón
    }

    public function agregarAuto(Request $request, Pensionado $pensionado)
    {
        // Verifica si el pensionado puede agregar otro auto
        if (!$pensionado->puedeAgregarAuto()) {
            return redirect()->back()->with('error', 'El pensionado ya tiene el máximo de autos permitidos.');
        }

        $request->validate([
            'placa' => 'required|string|max:255|unique:autos,placa,NULL,id,pensionado_id,' . $pensionado->id,
        ]);

        $pensionado->autos()->create($request->all());

        return redirect()->route('Pensionados.pensionados');
    }
}
