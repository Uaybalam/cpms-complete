<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CierrePDFController;
use App\Http\Controllers\NuevaCajaController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\PensionesController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\PlateController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('/lavadas', [PDFController::class, 'generarPdfLavadas']);

Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');




Route::post('/generar-pdf', [PDFController::class, 'generarQR']);
Route::post('/generar-pdf-salida', [PDFController::class, 'generarpdfSalida']);
Route::post('/generar-Cpdf', [PDFController::class, 'generarpdfCierre']);

Route::get('/Nueva-caja', [NuevaCajaController::class, 'abrirModal']) -> name('abrir.caja');
Route::post('/guardar-datos', [NuevaCajaController::class, 'guardarDatos'])->name('guardar-datos');
Route::get('/Ventas', [NuevaCajaController::class, 'abrirparcial'])-> name('cierreCaja');
Route::get('/Caja', [NuevaCajaController::class, 'Venta'])->name('caja.venta');
Route::get('/obtener-datos/{platNumber}', [NuevaCajaController::class, 'obtenerdatos']);
Route::post('/venta', [NuevaCajaController::class, 'guardarVenta']);
Route::get('/retiro-parcial', [NuevaCajaController::class, 'retiroParcial']);
Route::get('/cierre-Caja', [NuevaCajaController::class, 'cierreCaja']);
Route::get('/mostrarFactura/{id}', [NuevaCajaController::class, 'mostrarFactura'])->name('mostrar.Factura');
Route::get('/historial', [NuevaCajaController::class, 'historial']) -> name('historial');


Route::get('/get-costo/{category}', [VehicleController::class, 'getCosto'])->name('get-costo');


Route::get('/buscar-placa/{platNumber}', [PlateController::class, 'buscarPlaca'])->name('buscar.placa');

// Rutas para los pensionados
Route::get('/Nuevo_pensionados', [PensionesController::class, 'index'])->name('pensionados.index');
Route::post('/Nuevo_pensionados', [PensionesController::class, 'store'])->name('pensionados.store');
Route::get('/Pensionados', [PensionesController::class, 'pensionados'])->name('pensionados.pensionados');;
Route::get('/pensionados/{pensionado}/verificar-pago', [PensionesController::class, 'verificarPago'])->name('pensionados.verificarPago');
Route::get('/pensionados/{pensionado}/edit', [PensionesController::class, 'edit'])->name('pensionados.edit');
Route::put('/pensionados/{pensionado}', [PensionesController::class, 'update'])->name('pensionados.update');
Route::delete('/pensionados/{pensionado}', [PensionesController::class, 'destroy'])->name('pensionados.destroy');
Route::post('/usuario', [RegisterController::class, 'store'])->name('crear.store');

// Rutas para los autos
Route::post('/autos', [AutoController::class, 'store'])->name('autos.store');

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Route::get('/user', [App\Http\Controllers\UserController::class, 'index'])->name('user.index');
    Route::resource('/user', App\Http\Controllers\UserController::class);
    Route::resource('/customers', App\Http\Controllers\CustomerController::class);
    Route::resource('/categories', App\Http\Controllers\CategoryController::class);
    Route::resource('/vehicles', App\Http\Controllers\VehicleController::class);
    Route::resource('/vehiclesIn', App\Http\Controllers\VehicleInController::class);
    Route::resource('/vehiclesOut', App\Http\Controllers\VehicleOutController::class);

    Route::get('reports/index', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/imagen', [ImageController::class, 'index']);


});
