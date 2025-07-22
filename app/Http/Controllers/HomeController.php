<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ingreso;
use App\Models\Gasto;
use App\Models\GastoTarjeta;
use App\Models\TarjetaCredito;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $totalIngresos = Ingreso::where('user_id', $user->id)->sum('monto');
        $inicioMes = Carbon::now()->startOfMonth();

        // Gastos generales (excluyendo tarjetas)
        $totalGastosGenerales = Gasto::where('user_id', $user->id)->sum('monto');

        // Suma de las cuotas de tarjeta del mes actual
        $totalGastosTarjetaMes = GastoTarjeta::where('user_id', $user->id)
            ->whereYear('fecha', $inicioMes->year)
            ->whereMonth('fecha', $inicioMes->month)
            ->sum('monto_cuota');

        // Gasto total consolidado
        $totalGastos = $totalGastosGenerales + $totalGastosTarjetaMes;
        $balance = $totalIngresos - $totalGastos;

        // Lógica para el resumen de tarjetas de crédito
        $tarjetas = TarjetaCredito::where('user_id', $user->id)->get();
        $resumenTarjetas = $tarjetas->map(function ($tarjeta) {
            $inicioMes = Carbon::now()->startOfMonth();

            // Deuda del mes actual: suma de cuotas cuya fecha de pago es este mes.
            $deudaMesActual = $tarjeta->gastos()->whereYear('fecha', $inicioMes->year)->whereMonth('fecha', $inicioMes->month)->sum('monto_cuota');

            // Deuda total: suma de todas las cuotas pendientes (del mes actual en adelante).
            $deudaTotal = $tarjeta->gastos()->where('fecha', '>=', $inicioMes)->sum('monto_cuota');

            $cupoDisponible = $tarjeta->cupo_total - $deudaTotal;

            return (object) [
                'id' => $tarjeta->id,
                'nombre' => $tarjeta->nombre,
                'cupo_total' => $tarjeta->cupo_total,
                'deuda_mes_actual' => $deudaMesActual,
                'deuda_total' => $deudaTotal,
                'cupo_disponible' => $cupoDisponible,
            ];
        });

        return view('home', compact('totalIngresos', 'totalGastos', 'balance', 'resumenTarjetas'));
    }
}
