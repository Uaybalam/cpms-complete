<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FPDF;

class CierrePDFController extends Controller
{
    public function generarpdf(Request $request)
    {
        $total = $request->input('total');
        $cantidad = $request->input('cantidad');
        $detalles = $request->input('detalles');

        // Crear una nueva instancia de FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Configurar fuente y tamaño para el ticket
        $pdf->SetFont('Arial', '', 5);

        // Agregar contenido al PDF
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');
        $pdf->Cell(0, 5, '        Cierre de ventas     ', 0, 1, 'C');
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(5, 5, 'ID' , 0, 0, 'C');
        $pdf->Cell(10, 5, 'Cajero' , 0, 0, 'C');
        $pdf->Cell(10, 5, 'Total' , 0, 0, 'C');
        $pdf->Cell(15, 5, 'cantidad Inicial' , 0, 0, 'C');
        $pdf->Cell(10, 5, 'Retiro' , 0, 1);


        foreach ($detalles as $detalle) {
            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(5, 5, utf8_decode($detalle['id']), 0, 0, 'C');
            $pdf->Cell(10, 5, utf8_decode($detalle['Cajero']), 0, 0, 'C');
            $pdf->Cell(10, 5, utf8_decode($detalle['Total']), 0, 0, 'C');
            $pdf->Cell(15, 5, utf8_decode($detalle['cantidad']), 0, 0, 'C');
            $pdf->Cell(10, 5, utf8_decode($detalle['Retiro']), 0, 1);


        }
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(0, 5,'Total:              ' . '$' . $total, 0, 1, 'C');

        // Fin del ticket
        $pdf->Ln(10);
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');
        $pdf->Cell(0, 5, '        Ecelente Turno       ', 0, 1, 'C');
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');

        // Ruta del archivo de salida
        $filePath = storage_path('app\public\Cierre.pdf');

        // Salvar el archivo PDF
        $pdf->Output('F', $filePath);

        // Devolver la ruta del archivo en la respuesta
        return response()->json(['pdf_path' => $filePath]);

    }
}
