<?php

namespace App\Http\Controllers;

use App\Models\BebidasInventario;
use App\Models\Ingrediente;
use Illuminate\Http\Request;

class IngredienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function agregarIngrediente(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
        ]);

        $ingrediente = Ingrediente::create([
            'nombre' => $validated['nombre'],
            'precio' => $validated['precio'], 
            'cantidad' => 0, 
        ]);

        return redirect()->back()->with('success', 'Ingrediente agregado con éxito');
    }

    public function editarIngrediente(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0',
        ]);

        $ingrediente = Ingrediente::findOrFail($id);
        $ingrediente->precio = $request->input('precio');
        $ingrediente->update($validated);

        return redirect()->back()->with('success', 'Ingrediente actualizado con éxito');
    }

    public function eliminarIngrediente($id)
    {
        $ingrediente = Ingrediente::findOrFail($id);
        $ingrediente->delete();

        return redirect()->back()->with('success', [
            'message' => 'Ingrediente eliminado con éxito',
            'clearForm' => true
        ]);
    }

    public function agregarBebida(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $bebida = BebidasInventario::create([
            'nombre' => $validated['nombre'],
            'cantidad' => 0, // Asume cantidad inicial de 0
        ]);

        return redirect()->back()->with('success', 'Bebida agregado con éxito');
    }

    public function editarBebida(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $bebida = BebidasInventario::findOrFail($id);
        $bebida->update([
            'nombre' => $validated['nombre'],
        ]);

        return redirect()->back()->with('success', 'Bebida editado con éxito');
    }

    public function eliminarBebida($id)
    {
        $bebida = BebidasInventario::findOrFail($id);
        $bebida->delete();

        return redirect()->back()->with('success', 'Bebida eliminado con éxito');
    }
}
