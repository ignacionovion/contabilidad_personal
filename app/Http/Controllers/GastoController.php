<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gasto;
use App\Models\Categoria;
use App\Models\GastoRecurrente;

class GastoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gastos = Gasto::with('categoria')->where('user_id', Auth::id())->latest()->get();
        $gastosRecurrentes = GastoRecurrente::where('user_id', Auth::id())->get();

        // Obtener los nombres de las cuentas ya pagadas este mes
        $gastosPagadosEsteMes = Gasto::where('user_id', Auth::id())
            ->whereNotNull('gasto_recurrente_id')
            ->whereYear('fecha', now()->year)
            ->whereMonth('fecha', now()->month)
            ->pluck('gasto_recurrente_id')
            ->toArray();

        return view('gastos.index', compact('gastos', 'gastosRecurrentes', 'gastosPagadosEsteMes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::where('user_id', Auth::id())->get();
        return view('gastos.create', compact('categorias'));
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
            'categoria_id' => 'required|exists:categorias,id,user_id,' . Auth::id(),
        ]);

        $validatedData['descripcion'] = $validatedData['descripcion'] ?? '';

        if (empty($validatedData['fecha'])) {
            $validatedData['fecha'] = now();
        }

        $request->user()->gastos()->create($validatedData);

        return redirect()->route('gastos.index')->with('success', 'Gasto registrado con éxito.');
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
    public function edit(Gasto $gasto)
    {
        if ($gasto->user_id !== Auth::id()) {
            abort(403);
        }

        $categorias = Categoria::where('user_id', Auth::id())->get();
        return view('gastos.edit', compact('gasto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gasto $gasto)
    {
        if ($gasto->user_id !== Auth::id()) {
            abort(403);
        }

        $validatedData = $request->validate([
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
            'categoria_id' => 'required|exists:categorias,id,user_id,' . Auth::id(),
        ]);

        $validatedData['descripcion'] = $validatedData['descripcion'] ?? '';

        if (empty($validatedData['fecha'])) {
            $validatedData['fecha'] = now();
        }

        $gasto->update($validatedData);

        return redirect()->route('gastos.index')->with('success', 'Gasto actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gasto $gasto)
    {
        if ($gasto->user_id !== Auth::id()) {
            abort(403);
        }

        $gasto->delete();

        return redirect()->route('gastos.index')->with('success', 'Gasto eliminado con éxito.');
    }

    public function storeRecurrente(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0',
            'gasto_recurrente_id' => 'required|exists:gastos_recurrentes,id,user_id,' . Auth::id(),
        ]);

        $gastoRecurrente = GastoRecurrente::find($request->gasto_recurrente_id);

        // Verificar si ya existe un gasto para esta cuenta en el mes y año actual
        $gastoExistente = Gasto::where('user_id', Auth::id())
            ->where('gasto_recurrente_id', $request->gasto_recurrente_id)
            ->whereYear('fecha', now()->year)
            ->whereMonth('fecha', now()->month)
            ->first();

        if ($gastoExistente) {
            return redirect()->route('gastos.index')->with('warning', 'El gasto de ' . $gastoRecurrente->nombre . ' ya fue registrado este mes.');
        }

        // Busca o crea una categoría 'Cuentas del Hogar' para el usuario
        $categoria = Categoria::firstOrCreate(
            ['user_id' => Auth::id(), 'nombre' => 'Cuentas del Hogar'],
            ['descripcion' => 'Gastos fijos mensuales del hogar.']
        );

        Auth::user()->gastos()->create([
            'monto' => $request->monto,
            'descripcion' => $gastoRecurrente->nombre,
            'categoria_id' => $categoria->id,
            'fecha' => now(),
            'gasto_recurrente_id' => $gastoRecurrente->id,
        ]);

        return redirect()->route('gastos.index')->with('success', 'Gasto de ' . $gastoRecurrente->nombre . ' registrado con éxito.');
    }
}
