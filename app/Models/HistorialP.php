<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialP extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'ultimo_pago' , 'cobro', 'pensionado_id'];
}
