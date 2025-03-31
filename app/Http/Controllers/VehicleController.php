<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleIn;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon; // Importar Carbon para manejo de fechas
use Illuminate\Support\Facades\Log;
class VehicleController extends Controller
{

    public function index(Request $request)
    {
        Log::info('Inicio de método index en VehicleController', ['request' => $request->all()]);
        $query = Vehicle::with(['customer:id,name', 'user:id,name', 'category:id,name']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            Log::info('Aplicando filtro de búsqueda', ['search' => $search]);
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('plat_number', 'like', '%' . $search . '%')
                ->orWhereHas('category', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $vehicles = $query->paginate(10);
        Log::info('Vehículos recuperados', ['count' => $vehicles->count()]);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        Log::info('Inicio de método create en VehicleController');
        $lastFolio = Vehicle::max('registration_number');
        $nextFolio = $lastFolio ? $lastFolio + 1 : 1;

        Log::info('Folio siguiente calculado', ['nextFolio' => $nextFolio]);

        return view('vehicles.create', [
            'categories' => Category::get(['id', 'name']),
            'customers' => Customer::get(['id', 'name']),
            'nextFolio' => $nextFolio
        ]);
    }

    public function getCosto($category_id)
    {
        Log::info('Inicio de método getCosto en VehicleController', ['category_id' => $category_id]);
        $category = Category::findOrFail($category_id);
        Log::info('Categoría encontrada', ['category' => $category]);

        return response()->json(['costo' => $category->costo]);
    }

    public function store(StoreVehicleRequest $request)
    {
        Log::info('Inicio de método store en VehicleController', ['request' => $request->all()]);
        try {
            $existingVehicle = Vehicle::where('plat_number', $request->plat_number)->first();
            Log::info('Verificación de vehículo existente', ['existingVehicle' => $existingVehicle]);

            if ($existingVehicle) {
                $latestVehicleId = $existingVehicle->id;
                $existingVehicleIn = VehicleIn::where('vehicle_id', $latestVehicleId)->first();
                Log::info('Verificación de VehicleIn existente', ['existingVehicleIn' => $existingVehicleIn]);

                if ($existingVehicleIn) {
                    Log::warning('Vehículo ya registrado en VehicleIn');
                    return redirect()->route('vehicles.create')->with('error', 'Ya existe un vehiculo registrado, por favor dale salida para poder registrar la entrada!!');
                } else {
                    $existingVehicle->Visitas = ($existingVehicle->Visitas === 10) ? 1 : $existingVehicle->Visitas + 1;
                    $existingVehicle->update($request->except('vehicle_id', 'status'));
                    Log::info('Vehículo existente actualizado', ['vehicle' => $existingVehicle]);

                    VehicleIn::create(['vehicle_id' => $latestVehicleId] + $request->all());
                    Log::info('Nuevo registro de VehicleIn creado', ['vehicle_id' => $latestVehicleId]);
                }
            } else {
                $customer = Customer::updateOrCreate(['id' => $request->customer_id], $request->except('customer_id'));
                $newVehicle = Vehicle::create($request->except('vehicle_id', 'status') + ['status' => 0, 'customer_id' => $customer->id, 'Visitas' => 1]);
                Log::info('Nuevo vehículo creado', ['vehicle' => $newVehicle]);

                VehicleIn::create(['vehicle_id' => $newVehicle->id] + $request->all());
                Log::info('Nuevo registro de VehicleIn creado para nuevo vehículo', ['vehicle_id' => $newVehicle->id]);
            }

            return redirect()->route('vehicles.index')->with('success', $request->vehicle_id ? 'Vehicle Updated Successfully!!' : 'Vehicle Created Successfully!!');
        } catch (\Throwable $th) {
            Log::error('Error en método store de VehicleController', ['error' => $th->getMessage()]);
            return redirect()->route('vehicles.create')->with('error', 'Por favor revisa que los campos estén llenos!!');
        }
    }

    public function edit(Vehicle $vehicle)
    {
        Log::info('Inicio de método edit en VehicleController', ['vehicle_id' => $vehicle->id]);
        $now = Carbon::now();
        $creationTime = Carbon::parse($vehicle->updated_at);
        $diffInMinutes = $creationTime->diffInMinutes($now);

        if ($diffInMinutes > 15) {
            Log::warning('Intento de edición después de 15 minutos', ['vehicle_id' => $vehicle->id]);
            return redirect()->route('vehicles.index')->with('error', 'No puedes editar este vehículo después de 15 minutos desde su registro.');
        }

        $vehiculo = Vehicle::find($vehicle->id);
        $categories = Category::all();
        $salida = VehicleIn::where('vehicle_id', $vehicle->id)->first();

        Log::info('Datos preparados para edición de vehículo', ['vehicle' => $vehiculo, 'categories' => $categories, 'salida' => $salida]);

        return view('vehicles.edit', [
            'vehiculo' => $vehiculo,
            'categories' => $categories,
            'salida' => $salida
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        Log::info('Inicio de método update en VehicleController', ['request' => $request->all(), 'vehicle_id' => $vehicle->id]);
        $vehiculo = Vehicle::where('registration_number', $request->input('registration_number'))->first();

        if ($vehiculo) {
            $vehiculoin = VehicleIn::where('vehicle_id', $vehiculo->id)->first();
            $vehiculo->update($request->except(['_token', '_method', 'vehicle_id', 'status', 'updated_at']));
            Log::info('Vehículo actualizado', ['vehicle' => $vehiculo]);

            if ($vehiculoin) {
                $vehiculoin->update(['salida' => $request->input('salida')]);
                Log::info('Registro de VehicleIn actualizado', ['vehicle_in' => $vehiculoin]);
            }

            return redirect()->route('vehicles.index', $vehiculo->id)->with('success', 'Vehículo actualizado correctamente.');
        } else {
            Log::warning('Vehículo no encontrado para actualización', ['registration_number' => $request->input('registration_number')]);
            return redirect()->route('vehicles.index', $vehicle->id)->with('error', 'Vehículo no existe.');
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        Log::info('Inicio de método destroy en VehicleController', ['vehicle_id' => $vehicle->id]);
        $vehicle->delete();
        Log::info('Vehículo eliminado', ['vehicle_id' => $vehicle->id]);

        return redirect()->route('vehicles.index')->with('success', 'Vehiculo Eliminado');
    }
}


