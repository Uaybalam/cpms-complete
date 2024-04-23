<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pensionado;
use Illuminate\Http\Request;

class AutoController extends Controller
{
    public function store(Request $request, Pensionado $pensionado)
    {
        // Verifica si el pensionado puede agregar otro auto
        if (!$pensionado->puedeAgregarAuto()) {
            return redirect()->back()->with('error', 'El pensionado ya tiene el máximo de autos permitidos.');
        }

        // Verifica si el usuario actual es el pensionado
        if ($request->user()->id !== $pensionado->id) {
            abort(403, 'No tiene permiso para agregar autos a este pensionado.');
        }

        $request->validate([
            'placa' => 'required|string|max:255|unique:autos,placa,NULL,id,pensionado_id,' . $pensionado->id,
        ]);

        $pensionado->autos()->create($request->all());

        return redirect()->route('Pensionados.pensionados');
    }
}
