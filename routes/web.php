<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CierrePDFController;
use App\Http\Controllers\NuevaCajaController;
use App\Http\Controllers\PDFController;


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
Route::get('/Nueva-caja', [NuevaCajaController::class, 'abrirModal']);
Route::post('/guardar-datos', [NuevaCajaController::class, 'guardarDatos'])->name('guardar-datos');
Route::get('/Ventas', [NuevaCajaController::class, 'abrirparcial']);
Route::get('/Caja', [NuevaCajaController::class, 'Venta'])->name('caja.venta');
Route::get('/obtener-datos/{id}', [NuevaCajaController::class, 'obtenerdatos']);
Route::post('/generar-pdf', [PDFController::class, 'generarpdf']);
Route::post('/generar-Cpdf', [CierrePDFController::class, 'generarpdf']);
Route::post('/venta', [NuevaCajaController::class, 'guardarVenta']);
Route::get('/retiro-parcial', [NuevaCajaController::class, 'retiroParcial']);
Route::get('/cierre-Caja', [NuevaCajaController::class, 'cierreCaja']);
Route::get('/mostrarFactura/{id}', [NuevaCajaController::class, 'mostrarFactura'])->name('mostrar.Factura');
Route::get('/historial', [NuevaCajaController::class, 'historial']);


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
