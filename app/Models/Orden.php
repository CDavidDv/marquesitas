<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;

    protected $fillable = ['nombre_comprador', 'estado', 'metodo', 'total', 'sucursal_id'];

    public function marquesitas()
    {
        return $this->hasMany(Marquesita::class);
    }

    public function bebidas()
    {
        return $this->hasMany(Bebida::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
