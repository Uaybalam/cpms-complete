<?php
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class VehiclesExport implements FromCollection, WithHeadings {
    protected $detalles;

    public function __construct($detalles)
    {
        $this->detalles = $detalles;
    }

    public function collection()
    {
        // Deberías ajustar esta parte para que retorne la colección basada en `$this->detalles`
        return Vehicle::all(); // Ejemplo: Retornar todos los vehículos
    }
}
