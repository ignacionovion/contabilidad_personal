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
        $user = auth()->user();
        $sueldo = Ingreso::where('user_id', $user->id)->where('es_sueldo', true)->first();
        $ingresos = Ingreso::where('user_id', $user->id)->where('es_sueldo', false)->orderBy('fecha', 'desc')->get();
        
        return view('ingresos.index', compact('ingresos', 'sueldo', 'user'));
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
        $ingreso->delete();
        return redirect()->route('ingresos.index')->with('success', 'Ingreso eliminado con éxito');
    }

    public function guardarSueldo(Request $request)
    {
        $request->validate([
            'monto' => 'required|string',
        ]);

        $monto = preg_replace('/[\$\.]/', '', $request->monto);

        Ingreso::updateOrCreate(
            ['user_id' => auth()->id(), 'es_sueldo' => true],
            [
                'monto' => $monto,
                'descripcion' => 'Sueldo Fijo',
                'activo' => $request->has('activo'),
                'fecha' => now(), // Asignamos una fecha para consistencia
                'categoria_id' => null // El sueldo no necesita categoría
            ]
        );

        return redirect()->route('ingresos.index')->with('success', 'Sueldo guardado con éxito.');
    }

    /**
     * Actualiza el sueldo del usuario.
     */
    public function actualizarSueldo(Request $request)
    {
        // Limpiar el monto de puntos y comas antes de validar
        $sueldoLimpio = str_replace(['.', ','], '', $request->input('sueldo'));
        $request->merge(['sueldo' => $sueldoLimpio]);

        $request->validate([
            'sueldo' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $user->sueldo = $request->sueldo;
        $user->sueldo_activo = $request->has('sueldo_activo');
        $user->save();

        return redirect()->route('ingresos.index')->with('success', 'Sueldo actualizado con éxito.');
    }
}
