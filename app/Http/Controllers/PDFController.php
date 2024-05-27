<?php
// app/Http/Controllers/PDFController.php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use App\Models\Pensionado;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\VehiclesExport;
use App\Models\HistorialP;
use App\Models\Vehicle;
use FPDF;

class PDFController extends Controller{
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

        set_time_limit(300);
    // Ruta del archivo HTML
    $html_file_path = base_path('resources/views/ticket_de_llegada.blade.php');

    // Verifica si el archivo HTML existe
    if (file_exists($html_file_path)) {
    // Recibe los datos del formulario
    $Color = $request->input('Color');
    $name = $request->input('name');
    $visitas = (int) $request->input('visitas');
    $modelo = $request->input('modelo');
    $platNumber = $request->input('plat_number');
    $fechaActual = date('Y-m-d');
    $fechaSalida = $request->input('salida');

    $folio = $request->input('folio');
    $html_content = view('ticket_de_llegada', ['platNumber' => $platNumber, 'fechaSalida' => $fechaSalida ,'modelo' => $modelo, 'visitas' => $visitas, 'folio' => $folio, 'fechaActual' => $fechaActual, 'Color' => $Color])->render();

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
            $scriptPath = 'C:\xampp\htdocs\cpms-complete\scripts\entrada.ps1';
            exec("powershell -ExecutionPolicy Bypass -File $scriptPath", $output, $returnVar);

             // Verificación del resultado de la impresión
             if ($returnVar == 0) {
                // El comando se ejecutó correctamente
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // El comando falló
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                // Imprimir la salida del comando para depuración
                echo "Salida del comando de impresión: " . implode("\n", $output);
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

            //Impresion automatica
            $scriptPath = 'C:\xampp\htdocs\cpms-complete\scripts\salida.ps1';
            exec("powershell -ExecutionPolicy Bypass -File $scriptPath", $output, $returnVar);

             // Verificación del resultado de la impresión
             if ($returnVar == 0) {
                // El comando se ejecutó correctamente
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // El comando falló
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                // Imprimir la salida del comando para depuración
                echo "Salida del comando de impresión: " . implode("\n", $output);
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

        ob_start();
        $pdfContent = file_get_contents($output_path);
        $pdfContent = utf8_encode($pdfContent);

        Factura::create([
            'cliente' => 'Cierre'.date('Ymdhms'),
            'folio' => date('Ymdhms'),
            'pdf_content' => $pdfContent,
        ]);

            //Impresion automatica
            $scriptPath = 'C:\xampp\htdocs\cpms-complete\scripts\cierre.ps1';
            exec("powershell -ExecutionPolicy Bypass -File $scriptPath", $output, $returnVar);

             // Verificación del resultado de la impresión
             if ($returnVar == 0) {
                // El comando se ejecutó correctamente
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // El comando falló
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                // Imprimir la salida del comando para depuración
                echo "Salida del comando de impresión: " . implode("\n", $output);
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

    public function generarPdfLavadas(Request $request)
    {
        $detalles = $request->input('detalles');
        $json_data = json_encode($detalles);
        $fileName = 'lavadas.xlsx';
        $output_path = public_path($fileName); // Guardar temporalmente en el almacenamiento

        // Comando para ejecutar el script Python
        $command = escapeshellcmd("python " . base_path('scripts/generar_exel.py') . " " . escapeshellarg($output_path));

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
                    // Devolver el archivo como una respuesta de descarga
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
    $Color = $request->input('color');
    $name = $request->input('nombre');
    $modelo = $request->input('modelo');
    $placa = $request->input('placa');
    $Color2 = $request->input('color2');
    $modelo2 = $request->input('modelo2');
    $placa2 = $request->input('placa2');
    $folio = date('Ymdhms').'Z';

    $html_content = view('ticket_de_pensionado', ['placa' => $placa, 'color' => $Color, 'modelo' => $modelo,'placa2' => $placa2, 'color2' => $Color2, 'modelo2' => $modelo2 ])->render();

    // Resto del código para generar el PDF
    $output_path = base_path('public/pensionado.pdf');

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
            $scriptPath = 'C:\xampp\htdocs\cpms-complete\scripts\entrada.ps1';
            exec("powershell -ExecutionPolicy Bypass -File $scriptPath", $output, $returnVar);

             // Verificación del resultado de la impresión
             if ($returnVar == 0) {
                // El comando se ejecutó correctamente
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // El comando falló
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                // Imprimir la salida del comando para depuración
                echo "Salida del comando de impresión: " . implode("\n", $output);
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
    $Color = $request->input('color1');
    $name = $request->input('pensionadoNombre');
    $placa = $request->input('placa1');
    $Color2 = $request->input('color2');
    $placa2 = $request->input('placa2');
    $folio = date('Ymdhms').'Z';

    $html_content = view('pensionado_historico', ['placa' => $placa, 'color' => $Color, 'placa2' => $placa2, 'color2' => $Color2, 'pensionados'=> $pensionado ])->render();

    // Resto del código para generar el PDF
    $output_path = base_path('public/pensionadoH.pdf');

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
            $scriptPath = 'C:\xampp\htdocs\cpms-complete\scripts\pensionadoH.ps1';
            exec("powershell -ExecutionPolicy Bypass -File $scriptPath", $output, $returnVar);

             // Verificación del resultado de la impresión
             if ($returnVar == 0) {
                // El comando se ejecutó correctamente
                echo "El archivo se ha enviado a la impresora correctamente.";
            } else {
                // El comando falló
                echo "Error al enviar el archivo a la impresora. Código de error: $returnVar";
                // Imprimir la salida del comando para depuración
                echo "Salida del comando de impresión: " . implode("\n", $output);
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

