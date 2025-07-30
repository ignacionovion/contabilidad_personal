<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarjetaCredito;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TarjetaCreditoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
                $tarjetas = TarjetaCredito::where('user_id', Auth::id())
            ->withCount('compras')
            ->get();
        return view('tarjetas.index', compact('tarjetas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tarjetas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'cupo_total' => 'required|string',
            'dia_facturacion' => 'required|integer|min:1|max:31',
            'dia_pago' => 'required|integer|min:1|max:31',
        ]);

        $cupo_total = (int) str_replace('.', '', $validatedData['cupo_total']);

        $request->user()->tarjetasCredito()->create([
            'nombre' => $validatedData['nombre'],
            'cupo_total' => $cupo_total,
            'dia_facturacion' => $validatedData['dia_facturacion'],
            'dia_pago' => $validatedData['dia_pago'],
        ]);

        return redirect()->route('tarjetas.index')->with('success', 'Tarjeta de crédito agregada con éxito.');
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
    public function edit(TarjetaCredito $tarjeta)
    {
        if ($tarjeta->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }

        return view('tarjetas.edit', compact('tarjeta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TarjetaCredito $tarjeta)
    {
        if ($tarjeta->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'cupo_total' => 'required|string',
            'dia_facturacion' => 'required|integer|min:1|max:31',
            'dia_pago' => 'required|integer|min:1|max:31',
        ]);

        $cupo_total = (int) str_replace('.', '', $validatedData['cupo_total']);

        $tarjeta->update([
            'nombre' => $validatedData['nombre'],
            'cupo_total' => $cupo_total,
            'dia_facturacion' => $validatedData['dia_facturacion'],
            'dia_pago' => $validatedData['dia_pago'],
        ]);

        return redirect()->route('tarjetas.index')->with('success', 'Tarjeta de crédito actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TarjetaCredito $tarjeta)
    {
        if ($tarjeta->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }

        $tarjeta->delete();

        return redirect()->route('tarjetas.index')->with('success', 'Tarjeta de crédito eliminada con éxito.');
    }
}
