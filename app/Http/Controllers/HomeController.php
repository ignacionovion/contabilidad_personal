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
        $inicioMesActual = Carbon::now()->startOfMonth();

        // --- CÁLCULOS PARA TARJETAS DE RESUMEN (TOTALES HISTÓRICOS O DEL MES) ---
        $totalIngresos = Ingreso::where('user_id', $user->id)
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->sum('monto');
        if ($user->sueldo_activo) {
            $totalIngresos += $user->sueldo;
        }
        $totalGastosGenerales = Gasto::where('user_id', $user->id)->sum('monto');
        $totalGastosTarjetaMes = GastoTarjeta::where('user_id', $user->id)
            ->whereYear('fecha', $inicioMesActual->year)
            ->whereMonth('fecha', $inicioMesActual->month)
            ->sum('monto_cuota');
        $totalGastos = $totalGastosGenerales + $totalGastosTarjetaMes;
        $balance = $totalIngresos - $totalGastos;

        // --- CÁLCULO RESUMEN TARJETAS DE CRÉDITO ---
        $tarjetas = TarjetaCredito::where('user_id', $user->id)->get();
        $resumenTarjetas = $tarjetas->map(function ($tarjeta) use ($inicioMesActual) {
            $deudaMesActual = $tarjeta->gastos()->whereYear('fecha', $inicioMesActual->year)->whereMonth('fecha', $inicioMesActual->month)->sum('monto_cuota');
            $deudaTotal = $tarjeta->gastos()->where('fecha', '>=', $inicioMesActual)->sum('monto_cuota');
            $cupoDisponible = $tarjeta->cupo_total - $deudaTotal;
            return (object) [
                'id' => $tarjeta->id, 'nombre' => $tarjeta->nombre, 'cupo_total' => $tarjeta->cupo_total,
                'deuda_mes_actual' => $deudaMesActual, 'deuda_total' => $deudaTotal, 'cupo_disponible' => $cupoDisponible,
            ];
        });

        // --- LÓGICA PARA GRÁFICOS (ÚLTIMOS 12 MESES) ---
        $mesesLabels = [];
        $balanceMensualData = [];
        $gastosMensualesData = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $mes = $fecha->month;
            $ano = $fecha->year;

            // CÁLCULO INGRESOS DEL MES
            $ingresosVariablesMes = Ingreso::where('user_id', $user->id)->whereYear('fecha', $ano)->whereMonth('fecha', $mes)->sum('monto');
            $ingresosTotalesMes = $ingresosVariablesMes + ($user->sueldo_activo ? $user->sueldo : 0);

            // CÁLCULO GASTOS DEL MES
            $gastosGeneralesMes = Gasto::where('user_id', $user->id)->whereYear('fecha', $ano)->whereMonth('fecha', $mes)->sum('monto');
            $gastosTarjetaMes = GastoTarjeta::where('user_id', $user->id)->whereYear('fecha', $ano)->whereMonth('fecha', $mes)->sum('monto_cuota');
            $totalGastosMes = $gastosGeneralesMes + $gastosTarjetaMes;

            // GUARDAR DATOS PARA GRÁFICOS
            $mesesLabels[] = ucfirst($fecha->isoFormat('MMM YYYY'));
            $balanceMensualData[] = $ingresosTotalesMes - $totalGastosMes;
            $gastosMensualesData[] = $totalGastosMes;
        }

        // --- LÓGICA PARA GRÁFICO DE GASTOS DEL HOGAR (MÚLTIPLES LÍNEAS) ---
        $gastosHogarDatasets = [];
        $cuentasHogar = $user->gastosRecurrentes()->get();
        $colores = [
            'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'
        ];
        $colorIndex = 0;

        foreach ($cuentasHogar as $cuenta) {
            $datosCuenta = [];
            for ($i = 11; $i >= 0; $i--) {
                $fecha = Carbon::now()->subMonths($i);
                $gastoMes = Gasto::where('user_id', $user->id)
                    ->where('gasto_recurrente_id', $cuenta->id) // Búsqueda más precisa por ID
                    ->whereYear('fecha', $fecha->year)
                    ->whereMonth('fecha', $fecha->month)
                    ->sum('monto');
                $datosCuenta[] = $gastoMes;
            }

            $color = $colores[$colorIndex % count($colores)];
            $gastosHogarDatasets[] = [
                'label' => $cuenta->nombre,
                'data' => $datosCuenta,
                'borderColor' => $color,
                'backgroundColor' => str_replace('0.7', '0.2', $color),
                'fill' => true, 'pointRadius' => 3, 'pointBackgroundColor' => $color,
            ];
            $colorIndex++;
        }

        return view('home', compact(
            'totalIngresos', 'totalGastos', 'balance', 'resumenTarjetas',
            'mesesLabels', 'balanceMensualData', 'gastosMensualesData', 'gastosHogarDatasets'
        ));
    }
}
