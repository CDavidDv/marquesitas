<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BebidasInventario extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'nombre', 'precio', 'cantidad'];
}
