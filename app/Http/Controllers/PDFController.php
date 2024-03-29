<?php
// app/Http/Controllers/PDFController.php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use FPDF;

class PDFController extends Controller
{
    public function generarQR(Request $request)
    {
        $texto = $request->input('plat_number');

        // Ejecuta el script Python para generar el código QR
        $process = proc_open('python3 ' . base_path('scripts/generar_qr.py') . ' ' . escapeshellarg($texto), [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (is_resource($process)) {
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $retorno = proc_close($process);

            if ($retorno === 0) {
                echo "El código QR se generó correctamente.";
                // Continuar con la generación del PDF después de que se haya generado el código QR
                $this->generarPDF($request);
            } else {
                echo "Hubo un error al generar el código QR.";
                // Manejar el error adecuadamente
            }
        } else {
            echo "Error al iniciar el proceso para generar el código QR.";
            // Manejar el error adecuadamente
        }
    }


    // public function generarpdf(Request $request)
    public function generarpdf(Request $request)
    {


      // Ruta del archivo HTML
    $html_file_path = base_path('resources/views/ticket_de_llegada.blade.php');

// Verifica si el archivo HTML existe
    if (file_exists($html_file_path)) {
 // Recibe los datos del formulario
    $Color = $request->input('Color');
    $name = $request->input('name');
    $modelo = $request->input('modelo');
    $platNumber = $request->input('plat_number');
    $fechaActual = date('Y-m-d');
    $folio = date('Ymdhms').'Z';

    $html_content = view('ticket_de_llegada', ['platNumber' => $platNumber, 'modelo' => $modelo, 'folio' => $folio, 'fechaActual' => $fechaActual, 'Color' => $Color])->render();

    // Resto del código para generar el PDF
    $output_path = base_path('public/entrada.pdf');

    $process = proc_open('python ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ], $pipes);

    if (is_resource($process)) {
        // Escribe la cadena HTML en la entrada estándar del proceso
        fwrite($pipes[0], $html_content);
        fclose($pipes[0]);

        // Espera a que el proceso termine
        $exit_code = proc_close($process);

        if ($exit_code === 0) {

        ob_start();
        $pdfContent = file_get_contents($output_path);
        $pdfContent = utf8_encode($pdfContent);

        Factura::create([
            'cliente' => $name,
            'folio' => $folio,
            'pdf_content' => $pdfContent,
        ]);

            return response()->download($output_path, 'nombre_del_archivo.pdf');
        } else {
            // Hubo un error en el proceso, manejarlo adecuadamente
            return response()->json(['message' => 'Error al generar el PDF'], 500);
        }
    } else {
        // No se pudo iniciar el proceso, manejar el error
        return response()->json(['message' => 'Error al iniciar el proceso'], 500);
    }
    //  // Envía el archivo PDF a la impresora
    //  $printerName = 'nombre-de-la-impresora';
    //  $command = "lpr -d $printerName $output_path";
    //  $output = shell_exec($command);
    //   // Verificar si la impresión fue exitosa
    //   if ($output === null) {
    //     // La impresión fue exitosa
    //     return response()->json(['message' => 'El archivo se ha enviado correctamente a la impresora']);
    // } else {
    //     // Hubo un error al enviar el archivo a la impresora
    //     return response()->json(['message' => "Error al enviar el archivo a la impresora: $output"], 500);
    // }
} else {
    // El archivo HTML no existe, manejar el error
    return response()->json(['message' => 'El archivo HTML no existe'], 404);
}

    }


    public function generarpdfSalida(Request $request)
    {

      // Ruta del archivo HTML
    $html_file_path = base_path('resources/views/ticket_de_salida.blade.php');

// Verifica si el archivo HTML existe
    if (file_exists($html_file_path)) {
 // Recibe los datos del formulario

    $cliente = $request->input('cliente');
    $folio = $request->input('folio');
    $total = $request->input('total');
    $cambio = $request->input('cambio');
    $cantidad = $request->input('cantidad');
    $detalles = $request->input('detalles');

    $html_content = view('ticket_de_salida', ['cliente' => $cliente, 'folio' => $folio, 'total' => $total, 'cambio' => $cambio, 'cantidad' => $cantidad, 'detalles' => $detalles])->render();

    // Resto del código para generar el PDF
    $output_path = base_path('public/salida.pdf');



    $process = proc_open('python ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ], $pipes);

    if (is_resource($process)) {
        // Escribe la cadena HTML en la entrada estándar del proceso
        fwrite($pipes[0], $html_content);
        fclose($pipes[0]);

        // Espera a que el proceso termine
        $exit_code = proc_close($process);

        if ($exit_code === 0) {
        ob_start();
        $pdfContent = file_get_contents($output_path);
        $pdfContent = utf8_encode($pdfContent);

        Factura::create([
            'cliente' => $cliente,
            'folio' => $folio,
            'pdf_content' => $pdfContent,
        ]);
               // Envía el archivo PDF a la impresora
     $printerName = 'nombre-de-la-impresora';
     $command = "lpr -d $printerName $output_path";
     $output = shell_exec($command);
      // Verificar si la impresión fue exitosa
      if ($output === null) {
        // La impresión fue exitosa
        return response()->json(['message' => 'El archivo se ha enviado correctamente a la impresora']);
    } else {
        // Hubo un error al enviar el archivo a la impresora
        return response()->json(['message' => "Error al enviar el archivo a la impresora: $output"], 500);
    }
            return response()->download($output_path, 'nombre_del_archivo.pdf');
        } else {
            // Hubo un error en el proceso, manejarlo adecuadamente
            return response()->json(['message' => 'Error al generar el PDF'], 500);
        }
    } else {
        // No se pudo iniciar el proceso, manejar el error
        return response()->json(['message' => 'Error al iniciar el proceso'], 500);
    }
} else {
    // El archivo HTML no existe, manejar el error
    return response()->json(['message' => 'El archivo HTML no existe'], 404);
}

    }

    public function generarpdfCierre(Request $request)
    {

      // Ruta del archivo HTML
    $html_file_path = base_path('resources/views/ticket_Cierre.blade.php');

// Verifica si el archivo HTML existe
    if (file_exists($html_file_path)) {
 // Recibe los datos del formulario


    $total = $request->input('total');
    $cantidad = $request->input('cantidad');
    $detalles = $request->input('detalles');

    $html_content = view('ticket_Cierre', ['total' => $total, 'cantidad' => $cantidad, 'detalles' => $detalles])->render();

    // Resto del código para generar el PDF
    $output_path = base_path('public/cierre.pdf');



    $process = proc_open('python ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ], $pipes);

    if (is_resource($process)) {
        // Escribe la cadena HTML en la entrada estándar del proceso
        fwrite($pipes[0], $html_content);
        fclose($pipes[0]);

        // Espera a que el proceso termine
        $exit_code = proc_close($process);

        if ($exit_code === 0) {
            // El proceso finalizó correctamente, leer el contenido del PDF y guardarlo en la base de datos

        // Convierte el contenido a binario
        // $pdfBinary = pack('H*', bin2hex($pdfContent));

        // // Crea una nueva instancia del modelo Factura
        // $pdfModel = new Factura();

        // // Establece los valores de los campos
        // $pdfModel->cliente = 'numero'; // Puedes ajustar esto según tu lógica
        // $pdfModel->folio = '14522z';
        // $pdfModel->pdf_content = $pdfBinary; // Guarda el contenido binario del PDF

        // // Guarda el modelo en la base de datos
        // $pdfModel->save();
        ob_start();
        $pdfContent = file_get_contents($output_path);
        $pdfContent = utf8_encode($pdfContent);

        Factura::create([
            'cliente' => 'Cierre'.date('Ymdhms'),
            'folio' => date('Ymdhms'),
            'pdf_content' => $pdfContent,
        ]);

            return response()->download($output_path, 'nombre_del_archivo.pdf');
        } else {
            // Hubo un error en el proceso, manejarlo adecuadamente
            return response()->json(['message' => 'Error al generar el PDF'], 500);
        }
    } else {
        // No se pudo iniciar el proceso, manejar el error
        return response()->json(['message' => 'Error al iniciar el proceso'], 500);
    }
    //  // Envía el archivo PDF a la impresora
    //  $printerName = 'nombre-de-la-impresora';
    //  $command = "lpr -d $printerName $output_path";
    //  $output = shell_exec($command);
    //   // Verificar si la impresión fue exitosa
    //   if ($output === null) {
    //     // La impresión fue exitosa
    //     return response()->json(['message' => 'El archivo se ha enviado correctamente a la impresora']);
    // } else {
    //     // Hubo un error al enviar el archivo a la impresora
    //     return response()->json(['message' => "Error al enviar el archivo a la impresora: $output"], 500);
    // }
} else {
    // El archivo HTML no existe, manejar el error
    return response()->json(['message' => 'El archivo HTML no existe'], 404);
}

    }
}

