<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Models\Bebida;
use App\Models\BebidasInventario;
use App\Models\Ingrediente;
use App\Models\Inventario;
use App\Models\Order;
use Inertia\Response;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user(); 
        $sucursalId = $user->sucursal_id;

        $bebidas = BebidasInventario::all();
        $ingredientes = Ingrediente::all();
        $orders = Order::where('sucursal_id', $sucursalId)
        ->whereNotIn('estado',['Entregado', 'Cancelado'])
        ->with(['marquesitas.ingredientes', 'bebidas'])
        ->get();

        return Inertia::render('Dashboard', [
            'bebidas' => $bebidas,
            'ingredientes' => $ingredientes,
            'orders' => $orders,
            'sucursal' => $sucursalId
        ]);
    }

    public function corte(Request $request)
    {
        $user = $request->user(); 
        $sucursalId = $user->sucursal_id;

        // Obtener la fecha de hoy
        $hoy = \Carbon\Carbon::now()->startOfDay();
        
        // Filtrar las órdenes por sucursal, método de pago "Efectivo" y fecha de hoy
        $orders = Order::where('sucursal_id', $sucursalId)
                        ->where('metodo', 'Efectivo')
                        ->whereNotIn('estado',['Cancelado', 'Pagado'])
                        ->whereDate('created_at', $hoy)
                        ->with('marquesitas.ingredientes', 'bebidas')
                        ->get();

        $total = $orders->sum('total');
        $numeroDeOrdenes = $orders->count();

        return Inertia::render('Corte', [
            'orders' => $orders,
            'total' => $total,
            'numeroDeOrdenes' => $numeroDeOrdenes,
            'hoy' => $hoy,
            'sucursal' => $sucursalId
        ]);
    }

    public function inventario(Request $request)
    {
        $user = auth()->user();
        $sucursalId = $user->sucursal_id;

        $ingredientes = Ingrediente::all();
        $bebidas = BebidasInventario::all();
        $inventario = Inventario::where('sucursal_id', $sucursalId)->get();

        // Mapear los datos de inventario para cada ingrediente
        $ingredientesInventario = $ingredientes->map(function($ingrediente) use ($inventario) {
            $inventarioIngrediente = $inventario->where('ingrediente_id', $ingrediente->id)->first();
            $cantidad = $inventarioIngrediente->cantidad ?? 0;
            $precio = $inventarioIngrediente->precio ?? 0; // Asegúrate de que el campo 'precio' esté en la tabla de inventario
            return [
                'id' => $ingrediente->id,
                'nombre' => $ingrediente->nombre,
                'cantidad' => $cantidad,
                'precio' => $precio
            ];
        });

        // Mapear los datos de inventario para cada bebida
        $bebidasInventario = $bebidas->map(function($bebida) use ($inventario) {
            $inventarioBebida = $inventario->where('bebida_id', $bebida->id)->first();
            $cantidad = $inventarioBebida->cantidad ?? 0;
            $precio = $inventarioBebida->precio ?? 0; // Asegúrate de que el campo 'precio' esté en la tabla de inventario
            return [
                'id' => $bebida->id,
                'nombre' => $bebida->nombre,
                'cantidad' => $cantidad,
                'precio' => $precio
            ];
        });

        return Inertia::render('Inventario', [
            'ingredientes' => $ingredientesInventario,
            'bebidas' => $bebidasInventario,
            'sucursal' => $sucursalId
        ]);
    }




}
