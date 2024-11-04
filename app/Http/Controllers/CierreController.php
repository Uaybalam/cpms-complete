<?php

namespace App\Http\Controllers;

use App\Models\historial;
use Illuminate\Http\Request;
use FPDF;

class CierreController extends Controller
{
    public function index(Request $request)
{
    $query = historial::orderBy('created_at', 'desc');

    // Filtrar por fecha si se proporciona
    if ($request->filled('fecha')) {
        $query->whereDate('created_at', $request->input('fecha'));
    }

    // Filtrar por turno
    if ($request->filled('turno')) {
        if ($request->input('turno') == 'matutino') {
            // Matutino: de 7:00 a 19:00
            $query->whereTime('created_at', '>=', '07:00:00')
                  ->whereTime('created_at', '<=', '19:00:00');
        } elseif ($request->input('turno') == 'vespertino') {
            // Vespertino: de 20:00 a 6:00 (split en dos rangos para el cambio de día)
            $query->where(function($query) {
                $query->whereTime('created_at', '>=', '20:00:00')
                      ->orWhereTime('created_at', '<=', '06:00:00');
            });
        }
    }

    $cierres = $query->paginate(10)->appends([
        'fecha' => $request->input('fecha'),
        'turno' => $request->input('turno')
    ]);

        return view('caja.historial_cierre', compact('cierres'));
    }



}
