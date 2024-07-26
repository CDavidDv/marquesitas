<?php

namespace App\Http\Controllers;

use App\Models\Bebida;
use App\Models\Marquesita;
use App\Models\Order;
use Inertia\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Models\BebidasInventario;
use App\Models\Ingrediente;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
            'orders' => $orders
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user(); // Obtiene el usuario autenticado
        $sucursalId = $user->sucursal_id;

        $pedidoData = $request->validate([
            'nombre_comprador' => 'required|string',
            'estado' => 'required|string',
            'metodo' => 'required|string',
            'total' => 'required|numeric',
            'marquesitas' => 'array',
            'bebidas' => 'array',
        ]);

        if (empty($pedidoData['marquesitas']) && empty($pedidoData['bebidas'])) {
            return redirect()->back()->withErrors(['pedido' => 'Debe agregar al menos una marquesita o bebida al pedido.']);
        }

        $pedidoData['sucursal_id'] = $sucursalId;

        $order = Order::create($pedidoData);

        if (!empty($pedidoData['marquesitas'])) {
            foreach ($pedidoData['marquesitas'] as $marquesitaData) {
                $marquesita = new Marquesita([
                    'precio_marquesita' => $marquesitaData['precio'],
                    'cantidad' => $marquesitaData['cantidad']
                ]);

                $order->marquesitas()->save($marquesita);

                if (isset($marquesitaData['ingredientes'])) {
                    foreach ($marquesitaData['ingredientes'] as $ingredienteId) {
                        $marquesita->ingredientes()->attach($ingredienteId);
                    }
                }
            }
        }

        if (!empty($pedidoData['bebidas'])) {
            foreach ($pedidoData['bebidas'] as $bebidaData) {
                $bebida = new Bebida([
                    'nombre' => $bebidaData['nombre'],
                    'precio' => $bebidaData['precio'],
                    'cantidad' => $bebidaData['cantidad']
                ]);

                $order->bebidas()->save($bebida);
            }
        }

        return redirect()->back()->with('success', [
            'message' => 'Pedido guardado con éxito',
            'clearForm' => true
        ]);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'estado' => 'required|string'
        ]);

        $pedido = Order::findOrFail($id);
        $pedido->estado = $validated['estado'];
        $pedido->save();

        return redirect()->back()->with('success', [
            'message' => 'Pedido editado con éxito',
            'clearForm' => true
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
