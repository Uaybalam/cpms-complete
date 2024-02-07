<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NuevaCaja extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'cantidad_inicial'];
}
