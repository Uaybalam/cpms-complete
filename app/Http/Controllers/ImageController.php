<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Utils;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\Customer;
class ImageController extends Controller
{


    public function index()
    {
        try {
            // Reemplaza 'tu_api_key' con tu clave API de Plate Recognizer
            $apiKey = 'a2eac9effc461f353760bde8fe838e0f257e25aa';

            // Reemplaza '192.168.1.100' con la dirección IP de tu cámara
            $cameraIp = '10.28.124.79:8080';

            // Hacer la solicitud a la cámara
            $client = new Client();
            $response = $client->get("http://{$cameraIp}/photoaf.jpg");

            // Guardar la foto en storage
            $photoPath = 'photos/' . uniqid() . '.jpg';
            Storage::put($photoPath, $response->getBody());

            // Construir la solicitud como form-data
            $multipart = new MultipartStream([
                [
                    'name' => 'upload',
                    'contents' => file_get_contents(storage_path("app/$photoPath")),
                    'filename' => 'photo.jpg',
                ],
            ]);

            $request = new Request('POST', 'https://api.platerecognizer.com/v1/plate-reader/', [
                'Authorization' => 'Token ' . $apiKey,
            ], $multipart);

            // Enviar la solicitud y obtener la respuesta
            $response = $client->send($request);

            // Decodificar la respuesta JSON
            $result = json_decode($response->getBody(), true);

            // Verificar si se detectaron matrículas
            if (!empty($result['results'])) {
                // Tomar la primera matrícula detectada
                $plateNumber = $result['results'][0]['plate'];

                return view('vehicles.create', ['success' => true, 'plate_number' => $plateNumber, 'photo_path' => $photoPath,'categories' => Category::get(['id','name']),
                'customers' => Customer::get(['id','name'])]);
            }

            return view('vehicles.create', ['success' => true, 'plate_number' => null, 'photo_path' => $photoPath,'categories' => Category::get(['id','name']),
            'customers' => Customer::get(['id','name'])]);
        } catch (\Exception $e) {
            return view('vehicles.create', ['success' => false, 'error' => $e->getMessage(),'categories' => Category::get(['id','name']),
            'customers' => Customer::get(['id','name'])]);
        }
    }
}
