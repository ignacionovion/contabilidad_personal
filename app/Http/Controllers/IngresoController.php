<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ingreso;

class IngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ingresos = Ingreso::where('user_id', Auth::id())->get();
        return view('ingresos.index', compact('ingresos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ingresos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
        ]);

        if (empty($validatedData['fecha'])) {
            $validatedData['fecha'] = now();
        }

        $request->user()->ingresos()->create($validatedData);

        return redirect()->route('ingresos.index')->with('success', 'Ingreso registrado con éxito.');
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
    public function edit(Ingreso $ingreso)
    {
        if ($ingreso->user_id !== Auth::id()) {
            abort(403);
        }
        return view('ingresos.edit', compact('ingreso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingreso $ingreso)
    {
        if ($ingreso->user_id !== Auth::id()) {
            abort(403);
        }

        $validatedData = $request->validate([
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
        ]);

        if (empty($validatedData['fecha'])) {
            $validatedData['fecha'] = now();
        }

        $ingreso->update($validatedData);

        return redirect()->route('ingresos.index')->with('success', 'Ingreso actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingreso $ingreso)
    {
        if ($ingreso->user_id !== Auth::id()) {
            abort(403);
        }

        $ingreso->delete();

        return redirect()->route('ingresos.index')->with('success', 'Ingreso eliminado con éxito.');
    }
}
