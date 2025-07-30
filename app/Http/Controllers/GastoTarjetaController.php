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

        // 1. Obtener TODAS las cuotas de la tarjeta de una sola vez.
        $todasLasCuotas = GastoTarjeta::where('tarjeta_credito_id', $tarjeta->id)
            ->where('user_id', Auth::id())
            ->orderBy('fecha', 'asc')
            ->get();

        // 2. Agrupar las cuotas por su compra. El ID de la compra es el 'gasto_padre_id' o, si es nulo, el propio 'id'.
        $gastosAgrupados = $todasLasCuotas->groupBy(function ($item) {
            return $item->gasto_padre_id ?? $item->id;
        });

        // 3. Procesar cada grupo para crear la vista.
        $gastosParaVista = $gastosAgrupados->map(function ($cuotas, $idPadre) {
            $compraPadre = $cuotas->firstWhere('id', $idPadre);
            
            // Si por alguna razón no se encuentra el padre (caso improbable), usamos la primera cuota.
            if (!$compraPadre) {
                $compraPadre = $cuotas->first();
            }

            $cuotasPagadas = $cuotas->where('estado', 'Pagada')->count();
            $totalCuotas = $cuotas->count();

            return (object) [
                'id_representativo' => $compraPadre->id,
                'descripcion' => $compraPadre->descripcion,
                'monto_total' => $cuotas->sum('monto_cuota'),
                'monto_cuota' => $compraPadre->monto_cuota,
                'total_cuotas' => $totalCuotas,
                'cuotas_pagadas' => $cuotasPagadas,
                'fecha_compra' => $compraPadre->fecha,
                'cuotas_detalle' => $cuotas->sortBy('numero_cuota')->map(function($c) {
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

        $gastoPadreId = null;

        DB::transaction(function () use ($totalCuotas, $montoBaseCuota, $resto, $fechaCompra, $tarjeta, $validatedData, &$gastoPadreId) {
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
                    'gasto_padre_id' => $gastoPadreId, // La primera vez será NULL
                    'estado' => $estadoCuota,
                ]);

                if ($i == 1) {
                    $gastoPadreId = $gasto->id; // Guardamos el ID del padre para las siguientes cuotas
                }
            }
        });

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

        DB::transaction(function () use ($gasto_padre_id, $validated, $tarjetaId) {
            // 1. Eliminar todas las cuotas antiguas (hijas y padre)
            GastoTarjeta::where('id', $gasto_padre_id)->delete(); // Elimina el padre
            GastoTarjeta::where('gasto_padre_id', $gasto_padre_id)->delete(); // Elimina las hijas

            // 2. Crear las nuevas cuotas con la lógica correcta
            $montoTotal = (int) str_replace('.', '', $validated['monto']);
            $totalCuotas = $validated['total_cuotas'];
            $fechaCompra = Carbon::parse($validated['fecha']);
            $montoBaseCuota = floor($montoTotal / $totalCuotas);
            $resto = $montoTotal % $totalCuotas;

            $nuevoGastoPadreId = null;

            for ($i = 1; $i <= $totalCuotas; $i++) {
                $montoCuotaActual = ($i == 1) ? $montoBaseCuota + $resto : $montoBaseCuota;
                $fechaCuota = $fechaCompra->copy()->addMonths($i - 1);
                $estadoCuota = $fechaCuota->isPast() ? 'Pagada' : 'Pendiente';

                $gasto = GastoTarjeta::create([
                    'user_id' => Auth::id(),
                    'tarjeta_credito_id' => $tarjetaId,
                    'descripcion' => $validated['descripcion'],
                    'numero_cuota' => $i,
                    'total_cuotas' => $totalCuotas, // Se guarda el total correcto en cada cuota
                    'monto_cuota' => $montoCuotaActual,
                    'fecha' => $fechaCuota,
                    'gasto_padre_id' => $nuevoGastoPadreId, // La primera vez es NULL
                    'estado' => $estadoCuota,
                ]);

                if ($i == 1) {
                    $nuevoGastoPadreId = $gasto->id; // Guardamos el ID del nuevo padre
                }
            }
        });

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

        // REGLA DE NEGOCIO: No se puede cambiar el estado de cuotas de meses futuros.
        $fechaCuota = Carbon::parse($gasto->fecha);
        $inicioMesSiguiente = Carbon::now()->addMonth()->startOfMonth();

        if ($fechaCuota->gte($inicioMesSiguiente)) {
            return response()->json(['success' => false, 'message' => 'No se puede cambiar el estado de cuotas de meses futuros.'], 403);
        }

        $validated = $request->validate([
            'estado' => ['required', 'string', \Illuminate\Validation\Rule::in(['Pagada', 'Pendiente', 'No Pagada'])],
        ]);

        $gasto->estado = $validated['estado'];
        $gasto->save();

        // Recalcular el progreso para la interfaz de forma robusta
        $idPadre = $gasto->gasto_padre_id ?? $gasto->id;
        $cuotasDeLaCompra = GastoTarjeta::where(function ($query) use ($idPadre) {
            $query->where('gasto_padre_id', $idPadre)
                  ->orWhere('id', $idPadre);
        })->get();

        $cuotasPagadas = $cuotasDeLaCompra->where('estado', 'Pagada')->count();
        $totalCuotas = $cuotasDeLaCompra->count();

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
