<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corte extends Model
{
    use HasFactory;
    protected $fillable = ['Cajero', 'Placa' , 'Total', 'Estatus' ,'cantidad_inicial','Retiro','Corte_parcial'];
}
