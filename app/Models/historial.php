<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historial extends Model
{
    use HasFactory;
    protected $fillable = ['Cajero', 'Total','cantidad_inicial','Retiro', 'created_at', 'updated_at'];
}
