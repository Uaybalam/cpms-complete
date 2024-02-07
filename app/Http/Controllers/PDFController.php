<?php
// app/Http/Controllers/PDFController.php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use FPDF;

class PDFController extends Controller
{
    public function generarpdf(Request $request)
    {
        $cliente = $request->input('cliente');
        $folio = $request->input('folio');
        $total = $request->input('total');
        $cambio = $request->input('cambio');
        $cantidad = $request->input('cantidad');
        $detalles = $request->input('detalles');

        // Crear una nueva instancia de FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Configurar fuente y tamaño para el ticket
        $pdf->SetFont('Arial', '', 5);

        // Agregar contenido al PDF
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');
        $pdf->Cell(0, 5, '        TICKET DE VENTA        ', 0, 1, 'C');
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->Cell(0, 5, 'Cliente: ' . $cliente, 0, 1, 'C');
        $pdf->Cell(0, 5, 'Folio: ' . $folio, 0, 1, 'C');
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->Cell(0, 5, 'Detalle de Venta', 0, 1, 'C');
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(15, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(10, 5, 'Dias' , 0, 0, 'C');
        $pdf->Cell(25, 5, 'Fecha' , 0, 0, 'C');
        $pdf->Cell(15, 5, 'Descuento' , 0, 0, 'C');
        $pdf->Cell(20, 5, 'Subtotal' , 0, 1);

        foreach ($detalles as $detalle) {
            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(15, 5, '' , 0, 0, 'C');
            $pdf->Cell(7, 5, '' , 0, 0, 'C');
            $pdf->Cell(10, 5, utf8_decode($detalle['dias']), 0, 0, 'C');
            $pdf->Cell(25, 5, utf8_decode($detalle['fecha']), 0, 0, 'C');
            $pdf->Cell(15, 5, utf8_decode($detalle['descuento']), 0, 0, 'C');
            $pdf->Cell(20, 5, utf8_decode($detalle['subtotal']), 0, 1);

        }
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(0, 5,'Total:              ' . '$' . $total, 0, 1, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(0, 5,'Cantidad:              ' . '$' . $cantidad . '.00', 0, 1, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(7, 5, '' , 0, 0, 'C');
        $pdf->Cell(0, 5, 'Cambio:              ' . '$' . $cambio, 0, 1, 'C');

        // Fin del ticket
        $pdf->Ln(10);
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');
        $pdf->Cell(0, 5, '    Gracias por su compra!    ', 0, 1, 'C');
        $pdf->Cell(0, 5, '-----------------------------', 0, 1, 'C');

        // Ruta del archivo de salida
        $filePath = storage_path('app\public\factura.pdf');

        // Salvar el archivo PDF
        $pdf->Output('F', $filePath);
        ob_start();
        $pdfContent = file_get_contents($filePath);
        $pdfContent = utf8_encode($pdfContent);

        Factura::create([
            'cliente' => $cliente,
            'folio' => $folio,
            'pdf_content' => $pdfContent,
        ]);

        // Devolver la ruta del archivo en la respuesta
        return response()->json(['pdf_path' => $pdfContent]);


    }
}

