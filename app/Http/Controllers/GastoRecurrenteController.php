<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GastoRecurrente;

class GastoRecurrenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gastosRecurrentes = GastoRecurrente::where('user_id', auth()->id())->get();
        return view('gastos-recurrentes.index', compact('gastosRecurrentes'));
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
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        GastoRecurrente::create($data);

        return redirect()->route('gastos-recurrentes.index')->with('success', 'Cuenta de hogar creada con éxito.');
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
    public function edit($id)
    {
        $gastoRecurrente = GastoRecurrente::findOrFail($id);

        // Añadimos la seguridad que faltaba
        if ($gastoRecurrente->user_id !== auth()->id()) {
            abort(403, 'Acción no autorizada.');
        }

        return view('gastos-recurrentes.edit', compact('gastoRecurrente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $gastoRecurrente = GastoRecurrente::findOrFail($id);

        // Añadimos la seguridad que faltaba
        if ($gastoRecurrente->user_id !== auth()->id()) {
            abort(403, 'Acción no autorizada.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
        ]);

        $gastoRecurrente->update($data);

        return redirect()->route('gastos-recurrentes.index')->with('success', 'Cuenta de hogar actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // PASO DE DIAGNÓSTICO: Buscamos manualmente el modelo para saltarnos el Route-Model Binding.
        $gastoRecurrente = GastoRecurrente::find($id);

        if (!$gastoRecurrente) {
            abort(404, 'Cuenta de hogar no encontrada.');
        }

        // DIAGNÓSTICO FINAL DE ERROR 403:
        // Comparamos el ID del dueño del registro con el ID del usuario logueado.
        if ($gastoRecurrente->user_id !== auth()->id()) {
            dd(
                'DIAGNÓSTICO CON BÚSQUEDA MANUAL:',
                'ID del Dueño del Registro (gasto_recurrente.user_id): ' . $gastoRecurrente->user_id,
                'ID del Usuario Conectado (auth()->id()): ' . auth()->id()
            );
        }

        try {
            // Usamos una transacción para asegurar que ambas operaciones (update y delete) se completen
            // o ninguna lo haga. Esto resuelve el problema del rollback silencioso.
            \Illuminate\Support\Facades\DB::transaction(function () use ($gastoRecurrente) {
                // Desvincula los gastos hijos.
                $gastoRecurrente->gastos()->update(['gasto_recurrente_id' => null]);

                // Borrado del registro principal.
                $gastoRecurrente->delete();
            });

            return redirect()->route('gastos-recurrentes.index')->with('success', 'Cuenta de hogar eliminada con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('gastos-recurrentes.index')->with('error', 'Error durante el borrado: ' . $e->getMessage());
        }
    }
}
