<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarjetaCredito;
use App\Models\GastoTarjeta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GastoTarjetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TarjetaCredito $tarjeta)
    {
        if ($tarjeta->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }

        // Obtener todas las cuotas de la tarjeta y agruparlas por la compra original
        $gastosPorCompra = GastoTarjeta::where('tarjeta_credito_id', $tarjeta->id)
            ->where('user_id', Auth::id())
            ->orderBy('fecha', 'asc')
            ->get()
            ->groupBy('gasto_padre_id');

        $hoy = Carbon::now();

        // Procesar cada grupo para crear un resumen de la compra
        $gastosParaVista = $gastosPorCompra->map(function ($cuotas, $gasto_padre_id) {
            $primeraCuota = $cuotas->first();

            // Contar cuotas por su estado real
            $cuotasPagadas = $cuotas->where('estado', 'Pagada')->count();

            return (object) [
                'id_representativo' => $gasto_padre_id,
                'descripcion' => $primeraCuota->descripcion,
                'monto_total' => $cuotas->sum('monto_cuota'),
                'monto_cuota' => $primeraCuota->monto_cuota,
                'total_cuotas' => $primeraCuota->total_cuotas,
                'cuotas_pagadas' => $cuotasPagadas,
                'fecha_compra' => $cuotas->min('fecha'),
                'cuotas_detalle' => $cuotas->map(function($c) {
                    return [
                        'id'           => $c->id,
                        'numero_cuota' => $c->numero_cuota,
                        'monto_cuota'  => $c->monto_cuota,
                        'fecha'        => $c->fecha,
                        'estado'       => $c->estado,
                    ];
                })->values()->toJson()
            ];
        });

        return view('gastos_tarjeta.index', [
            'tarjeta' => $tarjeta,
            'gastos' => $gastosParaVista->sortByDesc('fecha_compra')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(TarjetaCredito $tarjeta)
    {
        if ($tarjeta->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }

        return view('gastos_tarjeta.create', compact('tarjeta'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TarjetaCredito $tarjeta)
    {
        if ($tarjeta->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }

        $validatedData = $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto_total' => 'required|string',
            'total_cuotas' => 'required|integer|min:1',
            'fecha' => 'nullable|date',
        ]);

        $montoTotal = (int) str_replace('.', '', $validatedData['monto_total']);
        $totalCuotas = (int) $validatedData['total_cuotas'];
        $fechaCompra = $validatedData['fecha'] ? Carbon::parse($validatedData['fecha']) : Carbon::now();

        $montoBaseCuota = floor($montoTotal / $totalCuotas);
        $resto = $montoTotal % $totalCuotas;

        $gastoPadre = null;

        for ($i = 1; $i <= $totalCuotas; $i++) {
            $montoCuotaActual = ($i == 1) ? $montoBaseCuota + $resto : $montoBaseCuota;
            $fechaCuota = $fechaCompra->copy()->addMonths($i - 1);
            $estadoCuota = $fechaCuota->isPast() ? 'Pagada' : 'Pendiente';

            $gasto = GastoTarjeta::create([
                'tarjeta_credito_id' => $tarjeta->id,
                'user_id' => Auth::id(),
                'descripcion' => $validatedData['descripcion'],
                'monto_cuota' => $montoCuotaActual,
                'numero_cuota' => $i,
                'total_cuotas' => $totalCuotas,
                'fecha' => $fechaCuota,
                'gasto_padre_id' => $gastoPadre ? $gastoPadre->id : null,
                'estado' => $estadoCuota,
            ]);

            if ($i == 1) {
                $gastoPadre = $gasto;
                $gastoPadre->gasto_padre_id = $gastoPadre->id;
                $gastoPadre->save();
            }
        }

        return redirect()->route('gastos_tarjeta.index', $tarjeta)->with('success', 'Gasto agregado con éxito en ' . $totalCuotas . ' cuota(s).');
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
    public function edit($gasto_padre_id)
    {
        $gastosAsociados = GastoTarjeta::where('gasto_padre_id', $gasto_padre_id)
            ->where('user_id', Auth::id())
            ->orderBy('fecha', 'asc')
            ->get();

        if ($gastosAsociados->isEmpty()) {
            abort(404);
        }

        $primeraCuota = $gastosAsociados->first();

        // Reconstruimos un objeto que representa la compra completa
        $gastoCompra = (object) [
            'id' => $gasto_padre_id, // Usamos el id padre para el formulario
            'descripcion' => $primeraCuota->descripcion,
            'monto' => $gastosAsociados->sum('monto_cuota'),
            'total_cuotas' => $primeraCuota->total_cuotas,
            'fecha' => Carbon::parse($primeraCuota->fecha), // Aseguramos que sea un objeto Carbon
            'tarjeta_credito_id' => $primeraCuota->tarjeta_credito_id
        ];

        $tarjeta = TarjetaCredito::findOrFail($gastoCompra->tarjeta_credito_id);

        return view('gastos_tarjeta.edit', [
            'gasto' => $gastoCompra,
            'tarjeta' => $tarjeta
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $gasto_padre_id)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'total_cuotas' => 'required|integer|min:1',
            'fecha' => 'required|date',
        ]);

        // Obtener la tarjeta de crédito antes de eliminar las cuotas
        $gastoExistente = GastoTarjeta::where('gasto_padre_id', $gasto_padre_id)
                                      ->where('user_id', Auth::id())
                                      ->firstOrFail();
        
        $tarjetaId = $gastoExistente->tarjeta_credito_id;

        // Eliminar todos los gastos antiguos asociados
        GastoTarjeta::where('gasto_padre_id', $gasto_padre_id)
                    ->where('user_id', Auth::id())
                    ->delete();

        // Crear los nuevos gastos
        $montoTotal = (int) str_replace('.', '', $validated['monto']);
        $totalCuotas = $validated['total_cuotas'];
        $fechaCompra = Carbon::parse($validated['fecha']);

        $montoBaseCuota = floor($montoTotal / $totalCuotas);
        $resto = $montoTotal % $totalCuotas;

        $gastoPadre = null;

        for ($i = 1; $i <= $totalCuotas; $i++) {
            $montoCuotaActual = ($i == 1) ? $montoBaseCuota + $resto : $montoBaseCuota;
            $fechaCuota = $fechaCompra->copy()->addMonths($i - 1);
            $estadoCuota = $fechaCuota->isPast() ? 'Pagada' : 'Pendiente';

            $nuevoGasto = GastoTarjeta::create([
                'user_id' => Auth::id(),
                'tarjeta_credito_id' => $tarjetaId,
                'descripcion' => $validated['descripcion'],
                'numero_cuota' => $i,
                'total_cuotas' => $totalCuotas,
                'monto_cuota' => $montoCuotaActual,
                'fecha' => $fechaCuota,
                'gasto_padre_id' => $gastoPadre ? $gastoPadre->id : null,
                'estado' => $estadoCuota,
            ]);

            if ($i == 1) {
                $gastoPadre = $nuevoGasto; // El primer gasto es el padre
                $gastoPadre->gasto_padre_id = $gastoPadre->id; // Se apunta a sí mismo
                $gastoPadre->save();
            }
        }

        return redirect()->route('gastos_tarjeta.index', ['tarjeta' => $tarjetaId])
                         ->with('success', 'Gasto actualizado con éxito.');
    }

    /**
     * Actualiza el estado de una cuota específica.
     */
    public function updateEstado(Request $request, GastoTarjeta $gasto)
    {
        // Comprobación de propiedad manual y explícita
        if (!$gasto->tarjetaCredito || $gasto->tarjetaCredito->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Acción no autorizada.'], 403);
        }

        $validated = $request->validate([
            'estado' => ['required', 'string', \Illuminate\Validation\Rule::in(['Pagada', 'Pendiente', 'No Pagada'])],
        ]);

        $gasto->estado = $validated['estado'];
        $gasto->save();

        // Recalcular el progreso para la interfaz
        $gastosDelPadre = GastoTarjeta::where('gasto_padre_id', $gasto->gasto_padre_id)->get();
        $cuotasPagadas = $gastosDelPadre->where('estado', 'Pagada')->count();
        $totalCuotas = $gastosDelPadre->count();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado con éxito.',
            'nuevo_estado' => $gasto->estado,
            'progreso_texto' => "$cuotasPagadas / $totalCuotas pagadas"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($gasto_padre_id)
    {
        // Primero, encontrar una de las cuotas para verificar el permiso y obtener el ID de la tarjeta para la redirección.
        $gasto = GastoTarjeta::where('gasto_padre_id', $gasto_padre_id)
                             ->where('user_id', Auth::id())
                             ->first();

        if (!$gasto) {
            abort(403, 'Acceso no autorizado o gasto no encontrado.');
        }

        // Guardar el ID de la tarjeta antes de eliminar
        $tarjetaId = $gasto->tarjeta_credito_id;

        // Eliminar todas las cuotas asociadas a la misma compra (mismo padre)
        GastoTarjeta::where('gasto_padre_id', $gasto_padre_id)
                    ->where('user_id', Auth::id())
                    ->delete();

        return redirect()->route('gastos_tarjeta.index', ['tarjeta' => $tarjetaId])
                         ->with('success', 'Gasto y todas sus cuotas eliminados con éxito.');
    }
}
