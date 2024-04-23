<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auto extends Model
{
    use HasFactory;
    protected $fillable = ['placa', 'placa2', 'Modelo', 'Modelo2', 'Color', 'Color2' , 'pensionado_id'];

    public function pensionado()
    {
        return $this->belongsTo(Pensionado::class);
    }
}
