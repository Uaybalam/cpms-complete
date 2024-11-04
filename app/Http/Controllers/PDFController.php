<?php
// app/Http/Controllers/PDFController.php

namespace App\Http\Controllers;

use App\Models\Auto;
use App\Models\Pensionado;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\VehiclesExport;
use App\Models\historial;
use App\Models\HistorialP;
use App\Models\VehicleIn;
use FPDF;

class PDFController extends Controller{
    public function generarQR(Request $request)
    {
        $texto = $request->input('plat_number');

        $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
        $scriptPath = base_path('scripts/generar_qr.py');
        $command = $pythonPath . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($texto);

        $process = proc_open($command, [
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
                $this->generarPDF($request); // Llama a la función de generar PDF
            } else {
                echo "Hubo un error al generar el código QR: " ;
            }
        } else {
            echo "Error al iniciar el proceso para generar el código QR.";
        }

    }


    // public function generarpdf(Request $request)
    public function generarpdf(Request $request)
    {

        set_time_limit(300);
    // Ruta del archivo HTML
    $html_file_path = base_path('resources/views/ticket_de_llegada.blade.php');

    // Verifica si el archivo HTML existe
    if (file_exists($html_file_path)) {
    // Recibe los datos del formulario
    $vigencia = $request->input('vigencia');
    $Color = $request->input('Color');
    $name = $request->input('name');
    $visitas = (int) $request->input('visitas');
    $modelo = $request->input('modelo');
    $platNumber = $request->input('plat_number');
    $category_id = $request->input('category_id');
    $fechaActual = date('d-m-Y H:i');
    $fechaSalida = $request->input('salida');
    $fechaSalida = str_replace('T', ' ', $fechaSalida);
    $folio = $request->input('folio');
    $sacarpensionado = Auto::where('placa', $platNumber)->orWhere('placa2', $platNumber)->first();
       // Define $fechacobro por defecto
       $fechacobro = 'No disponible';

    if($sacarpensionado){
    $pensionados =  Pensionado::where('id', $sacarpensionado-> pensionado_id)->first();

    // Obtener la fecha del ultimo_pago
    if($pensionados)
    {

        $fechaUltimoPago = $pensionados->ultimo_pago; // Fecha original
        $fechacobro = date('d-m-Y', strtotime($fechaUltimoPago . ' + 30 days')); // Suma 30 días a la fecha

        $data = [
            'category_id' => $category_id,
            'platNumber' => $platNumber,
            'fechaSalida' => $fechaSalida,
            'modelo' => $modelo,
            'visitas' => $visitas,
            'folio' => $folio,
            'fechaActual' => $fechaActual,
            'Color' => $Color,
            'vigencia' => $vigencia,
            'fechacobro' => $fechacobro // Pasar la nueva fecha
        ];
        // dd($data);

        $html_content = view('ticket_de_llegada', $data)->render();

    }

    }
    $data = [
        'category_id' => $category_id,
        'platNumber' => $platNumber,
        'fechaSalida' => $fechaSalida,
        'modelo' => $modelo,
        'visitas' => $visitas,
        'folio' => $folio,
        'fechaActual' => $fechaActual,
        'Color' => $Color,
        'vigencia' => $vigencia,
        'fechacobro' => $fechacobro
    ];

    $html_content = view('ticket_de_llegada', $data)->render();


    // Resto del código para generar el PDF
    $output_path = base_path('public/entrada.pdf');

    $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';

    $process = proc_open($pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ], $pipes);

    if (is_resource($process)) {
        // Escribe la cadena HTML en la entrada estándar del proceso
        fwrite($pipes[0], $html_content);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // Espera a que el proceso termine
        $exit_code = proc_close($process);
        // Impresion automatica
        if ($exit_code === 0) {
            ob_start();
            $pdfContent = file_get_contents($output_path);
            $pdfContent = utf8_encode($pdfContent);

            Factura::create([
                'cliente' => $name,
                'folio' => $folio,
                'pdf_content' => $pdfContent,
            ]);

            $printerName = "EPSON TM-T(203dpi) Receipt6";  // Nombre de la impresora
            $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\entrada.pdf';
            $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";  // Ruta a SumatraPDF

            // Construir el comando completo
            $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

            // Ejecuta SumatraPDF con opciones para imprimir el PDF en tamaño real
            exec($command, $output, $returnVar);

            // Depuración del comando
            echo "Comando ejecutado: " . $command . "<br>";

            // Verificación del resultado de la impresión
            if ($returnVar == 0) {
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // Capturar y mostrar cualquier error en la salida
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                echo "Salida del comando de impresión (detalle): " . implode("\n", $output);
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


    public function generarpdfSalida(Request $request)
    {

      // Ruta del archivo HTML
    $html_file_path = base_path('resources/views/ticket_de_salida.blade.php');

    // Verifica si el archivo HTML existe
    if (file_exists($html_file_path)) {
    // Recibe los datos del formulario
    $visitas = (int) $request->input('visitas');
    $category_id = $request->input('category_id');
    $cliente = $request->input('cliente');
    $folio = $request->input('folio');
    $total = $request->input('total');
    $cambio = $request->input('cambio');
    $cantidad = $request->input('cantidad');
    $detalles = $request->input('detalles');
    $inputPlaca = $request->input('inputPlaca');

    $html_content = view('ticket_de_salida', ['cliente' => $cliente,'category_id' => $category_id,  'visitas'=>$visitas, 'folio' => $folio, 'total' => $total, 'cambio' => $cambio, 'cantidad' => $cantidad, 'inputPlaca' => $inputPlaca , 'detalles' => $detalles])->render();

    // Resto del código para generar el PDF
    $output_path = base_path('public/salida.pdf');


    $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';

    $process = proc_open($pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
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

            //Impresion automatica
             $printerName = "EPSON TM-T(203dpi) Receipt6";  // Nombre de la impresora
            $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\salida.pdf';
            $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";  // Ruta a SumatraPDF

            // Construir el comando completo
            $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

            // Ejecuta SumatraPDF con opciones para imprimir el PDF en tamaño real
            exec($command, $output, $returnVar);

            // Depuración del comando
            echo "Comando ejecutado: " . $command . "<br>";

            // Verificación del resultado de la impresión
            if ($returnVar == 0) {
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // Capturar y mostrar cualquier error en la salida
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                echo "Salida del comando de impresión (detalle): " . implode("\n", $output);
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


    $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';

    $process = proc_open($pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
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
            'cliente' => 'Cierre'.date('Ymdhms'),
            'folio' => date('Ymdhms'),
            'pdf_content' => $pdfContent,
        ]);

            //Impresion automatica
            $printerName = "EPSON TM-T(203dpi) Receipt6";  // Nombre de la impresora
            $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\cierre.pdf';
            $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";  // Ruta a SumatraPDF

            // Construir el comando completo
            $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

            // Ejecuta SumatraPDF con opciones para imprimir el PDF en tamaño real
            exec($command, $output, $returnVar);

            // Depuración del comando
            echo "Comando ejecutado: " . $command . "<br>";

            // Verificación del resultado de la impresión
            if ($returnVar == 0) {
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // Capturar y mostrar cualquier error en la salida
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                echo "Salida del comando de impresión (detalle): " . implode("\n", $output);
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

    public function generarpdfHCierre(Request $request)
{
     // Recibir filtros desde el frontend
     $fecha = $request->input('fecha');
     $turno = $request->input('turno');

     // Aplicar filtros a la consulta
     $query = historial::query();

     if ($fecha) {
         $query->whereDate('created_at', $fecha);
     }

     if ($turno) {
         if ($turno === 'matutino') {
             $query->whereTime('created_at', '>=', '07:00:00')
                   ->whereTime('created_at', '<=', '19:00:00');
         } elseif ($turno === 'vespertino') {
             $query->where(function($query) {
                 $query->whereTime('created_at', '>=', '20:00:00')
                       ->orWhereTime('created_at', '<=', '06:00:00');
             });
         }
     }

     // Obtener todos los registros filtrados (sin paginación)
     $cierres = $query->get();

     // Verifica si hay resultados para generar el PDF
     if ($cierres->isEmpty()) {
         return response()->json(['message' => 'No se encontraron registros para los filtros seleccionados.'], 404);
     }

     // Generar el contenido HTML para el PDF usando una vista
     $html_content = view('ticket_HCierre', compact('cierres'))->render();

     // Ruta para guardar el PDF
     $output_path = public_path('Hcierre.pdf');

     // Generar el PDF usando la librería que ya tienes configurada
     // Aquí asumimos que tienes una función en Python o algún otro método que procesa el HTML y genera el PDF
     $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
     $scriptPath = base_path('scripts/generar_pdf.py');

     // Ejecutar el script de Python para generar el PDF
     $process = proc_open("$pythonPath $scriptPath " . escapeshellarg($output_path), [
         0 => ['pipe', 'r'], // stdin
         1 => ['pipe', 'w'], // stdout
         2 => ['pipe', 'w'], // stderr
     ], $pipes);

     if (!is_resource($process)) {
         return response()->json(['message' => 'Error al iniciar el proceso de generación de PDF'], 500);
     }

     // Escribir el contenido HTML en la entrada estándar del proceso
     fwrite($pipes[0], $html_content);
     fclose($pipes[0]);
     $output = stream_get_contents($pipes[2]);

     // Verifica el estado de cierre del proceso
     $exit_code = proc_close($process);
     if ($exit_code !== 0) {
         return response()->json(['message' => 'Error al generar el PDF'], 500);
     }

     // Retornar la ruta del PDF generado
     return response()->json(['pdf_path' => asset('Hcierre.pdf')]);
}

public function generarPdfLavadas(Request $request)
{
    // Obtén todos los registros de `VehicleIn` con las relaciones necesarias
    $vehiclesIn = VehicleIn::with(['vehicle:id,name,registration_number,plat_number,model', 'user:id,name'])
    ->orderBy('salida', 'desc') // Ordena por fecha en orden descendente
    ->get();

    // Convierte los registros a JSON para pasarlos al script de Python
    $vehiclesInData = $vehiclesIn->map(function($vehicleIn) {
        return [
            'Vehicle Name' => $vehicleIn->vehicle->name ,  // Nombre del vehículo
            'Plate Number' => $vehicleIn->vehicle->plat_number,
            'Model' => $vehicleIn->vehicle->model,
            'Salida' => $vehicleIn->salida,
        ];
    });

    $json_data = json_encode($vehiclesInData);
    $fileName = 'lavadas.xlsx';
    $output_path = public_path($fileName);

    $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
    $command = escapeshellcmd($pythonPath . ' ' . base_path('scripts/generar_exel.py') . ' ' . escapeshellarg($output_path));

    $process = proc_open($command, [
        0 => ['pipe', 'r'],  // stdin
        1 => ['pipe', 'w'],  // stdout
        2 => ['pipe', 'w'],  // stderr
    ], $pipes);

    if (is_resource($process)) {
        fwrite($pipes[0], $json_data);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error_output = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exit_code = proc_close($process);

        if ($exit_code === 0) {
            if (file_exists($output_path)) {
                return response()->json(['url' => url($fileName)]);
            } else {
                return response()->json(['message' => 'El archivo no se generó correctamente'], 500);
            }
        } else {
            return response()->json(['message' => 'Error al generar el archivo Excel', 'error' => $error_output], 500);
        }
    } else {
        return response()->json(['message' => 'No se pudo iniciar el proceso de Python'], 500);
    }
}

    public function generarpdfPensiones(Request $request)
    {

        set_time_limit(300);
    // Ruta del archivo HTML
    $html_file_path = base_path('resources/views/ticket_de_pensionado.blade.php');

    // Verifica si el archivo HTML existe
    if (file_exists($html_file_path)) {
    // Recibe los datos del formulario
    $Total = $request->input('Total');
    $Color = $request->input('color');
    $name = $request->input('nombre');
    $modelo = $request->input('modelo');
    $placa = $request->input('placa');
    $Color2 = $request->input('color2');
    $modelo2 = $request->input('modelo2');
    $placa2 = $request->input('placa2');
    $folio = date('Ymdhms').'Z';

    $html_content = view('ticket_de_pensionado', ['name' => $name,'placa' => $placa, 'color' => $Color, 'modelo' => $modelo,'placa2' => $placa2, 'color2' => $Color2, 'modelo2' => $modelo2, 'Total' => $Total ])->render();

    // Resto del código para generar el PDF
    $output_path = base_path('public/pensionado.pdf');

    $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
    $process = proc_open($pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
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
        // Impresion automatica
        if ($exit_code === 0) {
            ob_start();
            $pdfContent = file_get_contents($output_path);
            $pdfContent = utf8_encode($pdfContent);

            Factura::create([
                'cliente' => $name,
                'folio' => $folio,
                'pdf_content' => $pdfContent,
            ]);

            //Impresion automatica
            $printerName = "EPSON TM-T(203dpi) Receipt6";  // Nombre de la impresora
            $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\pensionado.pdf';
            $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";  // Ruta a SumatraPDF

            // Construir el comando completo
            $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

            // Ejecuta SumatraPDF con opciones para imprimir el PDF en tamaño real
            exec($command, $output, $returnVar);

            // Depuración del comando
            echo "Comando ejecutado: " . $command . "<br>";

            // Verificación del resultado de la impresión
            if ($returnVar == 0) {
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // Capturar y mostrar cualquier error en la salida
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                echo "Salida del comando de impresión (detalle): " . implode("\n", $output);
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

    public function pdfhistorico(Request $request)
    {

        set_time_limit(300);
    // Ruta del archivo HTML
    $html_file_path = base_path('resources/views/pensionado_historico.blade.php');

    // Verifica si el archivo HTML existe
    if (file_exists($html_file_path)) {
    // Recibe los datos del formulario
    $pensionado = HistorialP::where('pensionado_id', $request->input('pensionado_id'))->get();
    $pensionados = Pensionado::where('id', $request->input('pensionado_id'))->first();
    $fechaUltimoPago = Carbon::parse($pensionados->ultimo_pago);
    $fechaTermino = $fechaUltimoPago->addDays(30);
    $Color = $request->input('color1');
    $montoCobro = $request->input('montoCobro');
    $name = $request->input('pensionadoNombre');
    $placa = $request->input('placa1');
    $Color2 = $request->input('color2');
    $placa2 = $request->input('placa2');
    $folio = date('Ymdhms').'Z';

    $html_content = view('pensionado_historico', ['name' => $name , 'placa' => $placa, 'color' => $Color, 'placa2' => $placa2, 'color2' => $Color2,'pensionado'=> $pensionados, 'pensionados'=> $pensionado, 'fechaTermino'=>$fechaTermino, 'montoCobro'=> $montoCobro ])->render();

    // Resto del código para generar el PDF
    $output_path = base_path('public/pensionadoH.pdf');

    $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';

    $process = proc_open($pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ], $pipes);
    // dd($process);
    if (is_resource($process)) {
        // Escribe la cadena HTML en la entrada estándar del proceso
        fwrite($pipes[0], $html_content);
        fclose($pipes[0]);

        // Espera a que el proceso termine
        $exit_code = proc_close($process);
        // Impresion automatica
        if ($exit_code === 0) {
            ob_start();
            $pdfContent = file_get_contents($output_path);
            $pdfContent = utf8_encode($pdfContent);

            Factura::create([
                'cliente' => $name,
                'folio' => $folio,
                'pdf_content' => $pdfContent,
            ]);

            //Impresion automatica
            $printerName = "EPSON TM-T(203dpi) Receipt6";  // Nombre de la impresora
            $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\pensionadoH.pdf';
            $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";  // Ruta a SumatraPDF

            // Construir el comando completo
            $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

            // Ejecuta SumatraPDF con opciones para imprimir el PDF en tamaño real
            exec($command, $output, $returnVar);

            // Depuración del comando
            echo "Comando ejecutado: " . $command . "<br>";

            // Verificación del resultado de la impresión
            if ($returnVar == 0) {
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // Capturar y mostrar cualquier error en la salida
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                echo "Salida del comando de impresión (detalle): " . implode("\n", $output);
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
}

