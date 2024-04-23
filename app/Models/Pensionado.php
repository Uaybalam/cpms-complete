<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class Pensionado extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'telefono' , 'precio_fijo', 'name' , 'ultimo_pago'];
// app/Models/Pensionado.php

    public function proximoPagoEstado() {
        $proximoPago = $this->proximoPago();
        $fechaLimite = Carbon::parse($proximoPago)->addDays(5); // Añadir 5 días a la fecha de pago

        if (Carbon::now() > $fechaLimite) {
            return 'rojo'; // Si la fecha actual es mayor que la fecha límite, se pasó la fecha de pago
        } elseif (Carbon::now()->diffInDays($proximoPago, false) <= 5) {
            return 'naranja'; // Si la diferencia de días entre la fecha actual y la fecha de pago es menor o igual a 5
        } else {
            return 'verde'; // En cualquier otro caso, el pago está dentro del tiempo normal
        }
    }
    public function proximoPago() {
        // Supongamos que la fecha del último pago está almacenada en la columna 'ultimo_pago'
        $ultimoPago = Carbon::parse($this->ultimo_pago);

        // Calcular la fecha del próximo pago sumando un mes al último pago
        $proximoPago = $ultimoPago->addMonth();

        return $proximoPago;
    }
    public function autos()
    {
        return $this->hasMany(Auto::class);
    }

    public function puedeAgregarAuto()
    {
        return $this->autos->count() < 2;
    }

}
