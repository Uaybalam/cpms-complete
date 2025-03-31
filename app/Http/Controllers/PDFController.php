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
use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;
use FPDF;

class PDFController extends Controller
{
    public function generarQR(Request $request)
    {
        Log::info('Inicio de generación de QR', ['placa' => $request->input('plat_number')]);

        $texto = $request->input('plat_number');

        // Verificación de vehículo
        $vehicle = Vehicle::where('plat_number', $texto)->first();

        if ($vehicle) {
            $vehiclesIn = VehicleIn::where('vehicle_id', $vehicle->id)->first();

            if ($vehiclesIn) {
                Log::warning('Vehículo ya en estacionamiento', ['vehicle_id' => $vehicle->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'El vehículo ya se encuentra en el estacionamiento.'
                ], 409);
            }
        }

        // Generación de QR
        try {
            $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
            $scriptPath = base_path('scripts/generar_qr.py');
            $command = $pythonPath . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($texto);

            Log::debug('Ejecutando comando QR', ['command' => $command]);

            $process = proc_open($command, [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ], $pipes);

            if (!is_resource($process)) {
                Log::error('Error al iniciar proceso de generación QR');
                return response()->json([
                    'success' => false,
                    'message' => 'Error al iniciar el proceso para generar el código QR.'
                ], 500);
            }

            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $errorOutput = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $retorno = proc_close($process);

            if ($retorno !== 0) {
                Log::error('Error en generación QR', [
                    'exit_code' => $retorno,
                    'error_output' => $errorOutput
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Hubo un error al generar el código QR.'
                ], 500);
            }

            Log::info('QR generado exitosamente', ['placa' => $texto]);

            // Generar PDF
            $pdfResponse = $this->generarpdf($request);

            if ($pdfResponse->getStatusCode() !== 200) {
                return $pdfResponse;
            }

            Log::info('Proceso completo de generación QR y PDF');
            return response()->json([
                'success' => true,
                'message' => 'Código QR y PDF generados correctamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Excepción en generarQR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al generar QR: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarpdf(Request $request)
    {
        Log::info('Inicio de generación de PDF de entrada');
        set_time_limit(300);

        try {
            // Validación de campos requeridos
            $required = ['vigencia', 'Color', 'name', 'visitas', 'modelo', 'plat_number', 'category_id', 'salida', 'folio'];
            foreach ($required as $field) {
                if (!$request->has($field)) {
                    Log::error('Campo requerido faltante', ['campo' => $field]);
                    return response()->json([
                        'success' => false,
                        'message' => "Falta el campo requerido: $field"
                    ], 400);
                }
            }

            // Preparación de datos
            $platNumber = $request->input('plat_number');
            Log::debug('Buscando información de pensionado', ['placa' => $platNumber]);

            $sacarpensionado = Auto::where('placa', $platNumber)->orWhere('placa2', $platNumber)->first();
            $fechacobro = 'No disponible';

            if ($sacarpensionado) {
                $pensionados = Pensionado::where('id', $sacarpensionado->pensionado_id)->first();

                if ($pensionados && $pensionados->ultimo_pago) {
                    $fechaUltimoPago = $pensionados->ultimo_pago;
                    $fechacobro = date('d-m-Y', strtotime($fechaUltimoPago . ' + 30 days'));
                    Log::debug('Información de pensionado encontrada', [
                        'pensionado_id' => $pensionados->id,
                        'fecha_cobro' => $fechacobro
                    ]);
                }
            }

            $data = [
                'category_id' => $request->input('category_id'),
                'platNumber' => $platNumber,
                'fechaSalida' => str_replace('T', ' ', $request->input('salida')),
                'modelo' => $request->input('modelo'),
                'visitas' => (int)$request->input('visitas'),
                'folio' => $request->input('folio'),
                'fechaActual' => date('d-m-Y H:i'),
                'Color' => $request->input('Color'),
                'vigencia' => $request->input('vigencia'),
                'fechacobro' => $fechacobro,
                'name' => $request->input('name')
            ];

            Log::debug('Datos para el PDF', $data);

            // Generación de contenido HTML
            try {
                $html_content = view('ticket_de_llegada', $data)->render();
            } catch (\Exception $e) {
                Log::error('Error al generar HTML', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar el contenido HTML: ' . $e->getMessage()
                ], 500);
            }

            // Generación de PDF
            $output_path = base_path('public/entrada.pdf');
            $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
            $process = proc_open($pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ], $pipes);

            if (!is_resource($process)) {
                Log::error('Error al iniciar proceso de generación PDF');
                return response()->json([
                    'success' => false,
                    'message' => 'Error al iniciar el proceso de generación de PDF'
                ], 500);
            }

            fwrite($pipes[0], $html_content);
            fclose($pipes[0]);
            $errorOutput = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $exit_code = proc_close($process);

            if ($exit_code !== 0) {
                Log::error('Error en generación PDF', [
                    'exit_code' => $exit_code,
                    'error_output' => $errorOutput
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar el PDF: ' . $errorOutput
                ], 500);
            }

            Log::info('PDF generado exitosamente', ['path' => $output_path]);

            // Almacenamiento en Factura
            try {
                $pdfContent = file_get_contents($output_path);
                Factura::create([
                    'cliente' => $data['name'],
                    'folio' => $data['folio'],
                    'pdf_content' => utf8_encode($pdfContent),
                ]);
                Log::info('Factura creada exitosamente', ['folio' => $data['folio']]);
            } catch (\Exception $e) {
                Log::error('Error almacenando factura', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continuar aunque falle el almacenamiento
            }

            // Impresión del PDF
            $printerName = "EPSON TM-T20II Receipt";
            $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\entrada.pdf';
            $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";
            $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

            Log::debug('Ejecutando comando de impresión', ['command' => $command]);
            exec($command, $output, $returnVar);

            if ($returnVar != 0) {
                Log::error('Error al imprimir PDF', [
                    'returnVar' => $returnVar,
                    'output' => implode("\n", $output)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al imprimir el PDF',
                    'print_error' => implode("\n", $output)
                ], 500);
            }

            Log::info('PDF enviado a impresora exitosamente');
            return response()->download($output_path, 'entrada.pdf');

        } catch (\Exception $e) {
            Log::error('Excepción en generarpdf', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarpdfSalida(Request $request)
    {
        Log::info('Inicio de generación de PDF de salida', [
            'folio' => $request->input('folio'),
            'placa' => $request->input('inputPlaca')
        ]);

        try {
            // Ruta del archivo HTML
            $html_file_path = base_path('resources/views/ticket_de_salida.blade.php');

            // Verifica si el archivo HTML existe
            if (!file_exists($html_file_path)) {
                Log::error('Archivo de plantilla no encontrado', ['path' => $html_file_path]);
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo HTML no existe'
                ], 404);
            }

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

            Log::debug('Datos recibidos para PDF de salida', [
                'cliente' => $cliente,
                'folio' => $folio,
                'total' => $total,
                'placa' => $inputPlaca
            ]);

            // Generar contenido HTML
            try {
                $html_content = view('ticket_de_salida', [
                    'cliente' => $cliente,
                    'category_id' => $category_id,
                    'visitas' => $visitas,
                    'folio' => $folio,
                    'total' => $total,
                    'cambio' => $cambio,
                    'cantidad' => $cantidad,
                    'inputPlaca' => $inputPlaca,
                    'detalles' => $detalles
                ])->render();
            } catch (\Exception $e) {
                Log::error('Error al generar HTML', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar el contenido del ticket'
                ], 500);
            }

            // Ruta de salida del PDF
            $output_path = base_path('public/salida.pdf');
            Log::debug('Ruta de salida del PDF', ['path' => $output_path]);

            // Generar PDF con Python
            $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
            $command = $pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path);

            Log::debug('Ejecutando comando para generar PDF', ['command' => $command]);

            $process = proc_open($command, [
                0 => ['pipe', 'r'], // stdin
                1 => ['pipe', 'w'], // stdout
                2 => ['pipe', 'w'], // stderr
            ], $pipes);

            if (!is_resource($process)) {
                Log::error('Error al iniciar proceso de generación de PDF');
                return response()->json([
                    'success' => false,
                    'message' => 'Error al iniciar el proceso de generación de PDF'
                ], 500);
            }

            // Escribe el contenido HTML en el proceso
            fwrite($pipes[0], $html_content);
            fclose($pipes[0]);

            // Captura la salida de error
            $errorOutput = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $exit_code = proc_close($process);

            if ($exit_code !== 0) {
                Log::error('Error en generación de PDF', [
                    'exit_code' => $exit_code,
                    'error_output' => $errorOutput
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar el PDF',
                    'error_detail' => $errorOutput
                ], 500);
            }

            Log::info('PDF generado exitosamente', ['path' => $output_path]);

            // Almacenar en Factura
            try {
                $pdfContent = file_get_contents($output_path);
                Factura::create([
                    'cliente' => $cliente,
                    'folio' => $folio,
                    'pdf_content' => utf8_encode($pdfContent),
                ]);
                Log::info('Factura creada exitosamente', ['folio' => $folio]);
            } catch (\Exception $e) {
                Log::error('Error al guardar factura', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continuar aunque falle el guardado de la factura
            }

            // Impresión automática
            $printerName = "EPSON TM-T20II Receipt";
            $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\salida.pdf';
            $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";
            $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

            Log::debug('Ejecutando comando de impresión', ['command' => $command]);

            exec($command, $output, $returnVar);

            if ($returnVar != 0) {
                Log::error('Error al imprimir PDF', [
                    'return_code' => $returnVar,
                    'output' => implode("\n", $output)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al imprimir el PDF',
                    'print_error' => implode("\n", $output)
                ], 500);
            }

            Log::info('PDF enviado a impresora correctamente');
            return response()->download($output_path, 'ticket_salida.pdf');

        } catch (\Exception $e) {
            Log::error('Excepción no controlada en generarpdfSalida', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al generar PDF de salida'
            ], 500);
        }
    }

    public function generarpdfCierre(Request $request)
    {
        Log::info('Inicio de generación de PDF de cierre');

        // Ruta del archivo HTML
        $html_file_path = base_path('resources/views/ticket_Cierre.blade.php');

        // Verifica si el archivo HTML existe
        if (file_exists($html_file_path)) {
            Log::debug('Archivo HTML encontrado', ['path' => $html_file_path]);

            // Recibe los datos del formulario
            $total = $request->input('total');
            $cantidad = $request->input('cantidad');
            $detalles = $request->input('detalles');

            Log::debug('Datos recibidos para PDF de cierre', [
                'total' => $total,
                'cantidad' => $cantidad,
                'detalles' => $detalles
            ]);

            $html_content = view('ticket_Cierre', [
                'total' => $total,
                'cantidad' => $cantidad,
                'detalles' => $detalles
            ])->render();

            // Resto del código para generar el PDF
            $output_path = base_path('public/cierre.pdf');
            Log::debug('Ruta de salida del PDF', ['path' => $output_path]);

            $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';

            $process = proc_open($pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
                0 => ['pipe', 'r'], // stdin
                1 => ['pipe', 'w'], // stdout
                2 => ['pipe', 'w'], // stderr
            ], $pipes);

            if (is_resource($process)) {
                Log::debug('Proceso de generación de PDF iniciado');

                // Escribe la cadena HTML en la entrada estándar del proceso
                fwrite($pipes[0], $html_content);
                fclose($pipes[0]);

                // Captura la salida de error
                $errorOutput = stream_get_contents($pipes[2]);
                fclose($pipes[2]);

                $exit_code = proc_close($process);

                if ($exit_code === 0) {
                    Log::info('PDF generado exitosamente', ['path' => $output_path]);

                    ob_start();
                    $pdfContent = file_get_contents($output_path);
                    $pdfContent = utf8_encode($pdfContent);

                    Factura::create([
                        'cliente' => 'Cierre' . date('Ymdhms'),
                        'folio' => date('Ymdhms'),
                        'pdf_content' => $pdfContent,
                    ]);

                    Log::info('Factura creada exitosamente', ['folio' => date('Ymdhms')]);

                    // Impresión automática
                    $printerName = "EPSON TM-T20II Receipt";  // Nombre de la impresora
                    $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\cierre.pdf';
                    $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";  // Ruta a SumatraPDF

                    // Construir el comando completo
                    $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

                    Log::debug('Ejecutando comando de impresión', ['command' => $command]);

                    // Ejecuta SumatraPDF con opciones para imprimir el PDF en tamaño real
                    exec($command, $output, $returnVar);

                    // Verificación del resultado de la impresión
                    if ($returnVar == 0) {
                        Log::info('El archivo se ha enviado a la impresora correctamente');
                    } else {
                        Log::error('Error al enviar el archivo a la impresora', [
                            'return_code' => $returnVar,
                            'output' => implode("\n", $output)
                        ]);
                    }

                    return response()->download($output_path, 'nombre_del_archivo.pdf');
                } else {
                    Log::error('Error al generar el PDF', [
                        'exit_code' => $exit_code,
                        'error_output' => $errorOutput
                    ]);
                    return response()->json(['message' => 'Error al generar el PDF'], 500);
                }
            } else {
                Log::error('Error al iniciar el proceso de generación de PDF');
                return response()->json(['message' => 'Error al iniciar el proceso'], 500);
            }
        } else {
            Log::error('Archivo HTML no encontrado', ['path' => $html_file_path]);
            return response()->json(['message' => 'El archivo HTML no existe'], 404);
        }
    }

    public function generarpdfHCierre(Request $request)
{
    Log::info('Inicio de generación de PDF de historial de cierre', [
        'fecha' => $request->input('fecha'),
        'turno' => $request->input('turno')
    ]);

    // Recibir filtros desde el frontend
    $fecha = $request->input('fecha');
    $turno = $request->input('turno');

    // Aplicar filtros a la consulta
    $query = historial::query();

    if ($fecha) {
        $query->whereDate('created_at', $fecha);
        Log::debug('Filtro aplicado: fecha', ['fecha' => $fecha]);
    }

    if ($turno) {
        if ($turno === 'matutino') {
            $query->whereTime('created_at', '>=', '07:00:00')
                  ->whereTime('created_at', '<=', '19:00:00');
            Log::debug('Filtro aplicado: turno matutino');
        } elseif ($turno === 'vespertino') {
            $query->where(function($query) {
                $query->whereTime('created_at', '>=', '20:00:00')
                      ->orWhereTime('created_at', '<=', '06:00:00');
            });
            Log::debug('Filtro aplicado: turno vespertino');
        }
    }

    // Obtener todos los registros filtrados (sin paginación)
    $cierres = $query->get();

    // Verifica si hay resultados para generar el PDF
    if ($cierres->isEmpty()) {
        Log::warning('No se encontraron registros para los filtros seleccionados');
        return response()->json(['message' => 'No se encontraron registros para los filtros seleccionados.'], 404);
    }

    Log::info('Registros encontrados para generación de PDF', ['total_registros' => $cierres->count()]);

    // Generar el contenido HTML para el PDF usando una vista
    try {
        $html_content = view('ticket_HCierre', compact('cierres'))->render();
        Log::debug('Contenido HTML generado correctamente');
    } catch (\Exception $e) {
        Log::error('Error al generar contenido HTML', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['message' => 'Error al generar el contenido HTML'], 500);
    }

    // Ruta para guardar el PDF
    $output_path = public_path('Hcierre.pdf');
    Log::debug('Ruta de salida del PDF', ['path' => $output_path]);

    // Generar el PDF usando Python
    $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
    $scriptPath = base_path('scripts/generar_pdf.py');

    Log::debug('Ejecutando script de Python para generar PDF', [
        'python_path' => $pythonPath,
        'script_path' => $scriptPath
    ]);

    $process = proc_open("$pythonPath $scriptPath " . escapeshellarg($output_path), [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ], $pipes);

    if (!is_resource($process)) {
        Log::error('Error al iniciar el proceso de generación de PDF');
        return response()->json(['message' => 'Error al iniciar el proceso de generación de PDF'], 500);
    }

    // Escribir el contenido HTML en la entrada estándar del proceso
    fwrite($pipes[0], $html_content);
    fclose($pipes[0]);
    $error_output = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    // Verifica el estado de cierre del proceso
    $exit_code = proc_close($process);

    if ($exit_code === 0) {
        Log::info('PDF generado exitosamente', ['path' => $output_path]);

        // Impresión automática
        $printerName = "EPSON TM-T20II Receipt";  // Nombre de la impresora
        $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\Hcierre.pdf';
        $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";  // Ruta a SumatraPDF

        $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";
        Log::debug('Ejecutando comando de impresión', ['command' => $command]);

        exec($command, $output, $returnVar);

        if ($returnVar == 0) {
            Log::info('El archivo se ha enviado a la impresora correctamente');
        } else {
            Log::error('Error al enviar el archivo a la impresora', [
                'return_code' => $returnVar,
                'output' => implode("\n", $output)
            ]);
        }

        return response()->download($output_path, 'nombre_del_archivo.pdf');
    } else {
        Log::error('Error al generar el PDF', [
            'exit_code' => $exit_code,
            'error_output' => $error_output
        ]);
        return response()->json(['message' => 'Error al generar el PDF'], 500);
    }
}

public function generarPdfLavadas(Request $request)
{
    Log::info('Inicio de generación de Excel de lavadas');

    $vehiclesIn = VehicleIn::with(['vehicle:id,name,registration_number,plat_number,model,color', 'user:id,name'])
        ->orderBy('salida', 'desc')
        ->get();

    Log::debug('Registros obtenidos para Excel de lavadas', ['total_registros' => $vehiclesIn->count()]);

    $vehiclesInData = $vehiclesIn->map(function($vehicleIn) {
        return [
            'Vehicle Name' => $vehicleIn->vehicle->name,
            'Plate Number' => $vehicleIn->vehicle->plat_number,
            'Model' => $vehicleIn->vehicle->model,
            'Color' => $vehicleIn->vehicle->color,
            'Salida' => $vehicleIn->salida,
            'Entrada' => $vehicleIn->created_at->format('Y-m-d H:i:s'),
        ];
    });

    $json_data = json_encode($vehiclesInData);
    $fileName = 'lavadas.xlsx';
    $output_path = public_path($fileName);

    $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';
    $command = escapeshellcmd($pythonPath . ' ' . base_path('scripts/generar_exel.py') . ' ' . escapeshellarg($output_path));

    Log::debug('Ejecutando comando para generar Excel', ['command' => $command]);

    $process = proc_open($command, [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
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
            Log::info('Excel generado exitosamente', ['path' => $output_path]);

            if (file_exists($output_path)) {
                return response()->json(['url' => url($fileName)]);
            } else {
                Log::error('El archivo Excel no se generó correctamente');
                return response()->json(['message' => 'El archivo no se generó correctamente'], 500);
            }
        } else {
            Log::error('Error al generar el archivo Excel', [
                'exit_code' => $exit_code,
                'error_output' => $error_output
            ]);
            return response()->json(['message' => 'Error al generar el archivo Excel', 'error' => $error_output], 500);
        }
    } else {
        Log::error('No se pudo iniciar el proceso de Python');
        return response()->json(['message' => 'No se pudo iniciar el proceso de Python'], 500);
    }
}

public function generarpdfPensiones(Request $request)
{
    Log::info('Inicio de generación de PDF de pensiones');

    set_time_limit(300);
    $html_file_path = base_path('resources/views/ticket_de_pensionado.blade.php');

    if (file_exists($html_file_path)) {
        Log::debug('Archivo HTML encontrado', ['path' => $html_file_path]);

        $Total = $request->input('Total');
        $Color = $request->input('color');
        $name = $request->input('nombre');
        $modelo = $request->input('modelo');
        $placa = $request->input('placa');
        $Color2 = $request->input('color2');
        $modelo2 = $request->input('modelo2');
        $placa2 = $request->input('placa2');
        $folio = date('Ymdhms').'Z';

        Log::debug('Datos recibidos para PDF de pensiones', [
            'name' => $name,
            'placa' => $placa,
            'color' => $Color,
            'modelo' => $modelo,
            'placa2' => $placa2,
            'color2' => $Color2,
            'modelo2' => $modelo2,
            'Total' => $Total
        ]);

        $html_content = view('ticket_de_pensionado', [
            'name' => $name,
            'placa' => $placa,
            'color' => $Color,
            'modelo' => $modelo,
            'placa2' => $placa2,
            'color2' => $Color2,
            'modelo2' => $modelo2,
            'Total' => $Total
        ])->render();

        $output_path = base_path('public/pensionado.pdf');
        $pythonPath = env('USERPROFILE') . '\AppData\Local\Programs\Python\Python313\python.exe';

        $process = proc_open($pythonPath . ' ' . base_path('scripts/generar_pdf.py') . ' ' . escapeshellarg($output_path), [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (is_resource($process)) {
            fwrite($pipes[0], $html_content);
            fclose($pipes[0]);

            $exit_code = proc_close($process);

            if ($exit_code === 0) {
                Log::info('PDF generado exitosamente', ['path' => $output_path]);

                ob_start();
                $pdfContent = file_get_contents($output_path);
                $pdfContent = utf8_encode($pdfContent);

                Factura::create([
                    'cliente' => $name,
                    'folio' => $folio,
                    'pdf_content' => $pdfContent,
                ]);

                Log::info('Factura creada exitosamente', ['folio' => $folio]);

                $printerName = "EPSON TM-T20II Receipt";
                $pdfPath = 'C:\\xampp\\htdocs\\cpms-complete\\public\\pensionado.pdf';
                $sumatraPath = "C:\\Program Files\\SumatraPDF\\SumatraPDF.exe";

                $command = "\"$sumatraPath\" -print-to \"$printerName\" \"$pdfPath\" -print-settings \"noscale\" 2>&1";

                Log::debug('Ejecutando comando de impresión', ['command' => $command]);

                exec($command, $output, $returnVar);

                if ($returnVar == 0) {
                    Log::info('El archivo se ha enviado a la impresora correctamente');
                } else {
                    Log::error('Error al enviar el archivo a la impresora', [
                        'return_code' => $returnVar,
                        'output' => implode("\n", $output)
                    ]);
                }

                return response()->download($output_path, 'nombre_del_archivo.pdf');
            } else {
                Log::error('Error al generar el PDF', ['exit_code' => $exit_code]);
                return response()->json(['message' => 'Error al generar el PDF'], 500);
            }
        } else {
            Log::error('Error al iniciar el proceso de generación de PDF');
            return response()->json(['message' => 'Error al iniciar el proceso'], 500);
        }
    } else {
        Log::error('Archivo HTML no encontrado', ['path' => $html_file_path]);
        return response()->json(['message' => 'El archivo HTML no existe'], 404);
    }
}
}

