<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CsvImportController extends Controller
{
    // public function showForm()
    // {
    //     return view('csv_import');
    // }

    public function importCsv(Request $request)
    {
        // $request->validate([
        //     'csv_file' => 'required|mimes:csv,txt|max:2048',
        // ]);

        $file = $request->file('csv_file');
        $path = $file->storeAs('uploads', $file->getClientOriginalName());

        // Obtener las variables de entorno de la base de datos de Laravel
        $db_user = env('DB_USERNAME1');
        $db_password = env('DB_PASSWORD1');
        $db_host = env('DB_HOST1');
        $db_port = env('DB_PORT1');
        $db_name = env('DB_DATABASE1');

        // Ejecutar el script de Python
        $command = escapeshellcmd("python3 ../scripts/import_csv.py {$path} {$db_user} {$db_password} {$db_host} {$db_port} {$db_name}");
        $output = shell_exec($command . ' 2>&1'); // Capturar también los errores

        return back()->with('success', 'CSV importado correctamente.')->with('output', $output);
    }
}
