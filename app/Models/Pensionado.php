<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pensionado extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'precio_fijo', 'ultimo_pago'];

    public function autos()
    {
        return $this->hasMany(Auto::class);
    }

    public function puedeAgregarAuto()
    {
        return $this->autos->count() < 2;
    }
}
