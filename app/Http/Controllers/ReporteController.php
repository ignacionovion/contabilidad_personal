<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TarjetaCredito;
use App\Models\GastoTarjeta;
use Carbon\Carbon;
use ZipArchive;
use Illuminate\Support\Facades\File;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function descargar(Request $request)
    {
        $request->validate([
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|min:2000',
        ]);

        $mes = $request->input('mes');
        $anio = $request->input('anio');
        $fecha = Carbon::create($anio, $mes, 1);

        $tarjetas = TarjetaCredito::where('user_id', Auth::id())->get();

        $zip = new ZipArchive();
        $nombreMes = $fecha->locale('es')->monthName;
        $nombreArchivoZip = 'Reporte-' . ucfirst($nombreMes) . '-' . $anio . '.zip';
        $rutaTemporalZip = storage_path('app/temp/' . $nombreArchivoZip);

        // Asegurarse de que el directorio temporal exista
        File::ensureDirectoryExists(storage_path('app/temp'));

        if ($zip->open($rutaTemporalZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return back()->with('error', 'No se pudo crear el archivo ZIP.');
        }

        $directorioEnZip = ucfirst($nombreMes) . '-' . $anio . '/';
        $zip->addEmptyDir($directorioEnZip);

        $seAgregoContenido = false;

        foreach ($tarjetas as $tarjeta) {
            $gastos = GastoTarjeta::where('tarjeta_credito_id', $tarjeta->id)
                ->whereYear('fecha', $anio)
                ->whereMonth('fecha', $mes)
                ->orderBy('fecha', 'asc')
                ->get();

            if ($gastos->isNotEmpty()) {
                $seAgregoContenido = true;
                $contenidoCsv = "Fecha;Descripcion;Monto;Tipo Compra;Total Cuotas;Cuota Actual\n";
                foreach ($gastos as $gasto) {
                    // Limpiamos la descripción para evitar que un salto de línea rompa el CSV
                    $descripcionLimpia = str_replace(["\r", "\n"], ' ', $gasto->descripcion);
                    $tipoCompra = $gasto->es_cuotas ? 'Cuotas' : 'Contado';
                    // Si es de contado, la cuota actual es 1. Si no, usamos el nro_cuota.
                    $cuotaActual = $gasto->es_cuotas ? $gasto->nro_cuota : '1';
                    $contenidoCsv .= "{$gasto->fecha};\"{$descripcionLimpia}\";{$gasto->monto};{$tipoCompra};{$gasto->total_cuotas};{$cuotaActual}\n";
                }

                $nombreArchivoCsv = str_replace(' ', '_', $tarjeta->nombre) . '.csv';
                $zip->addFromString($directorioEnZip . $nombreArchivoCsv, $contenidoCsv);
            }
        }

        $zip->close();

        if (!$seAgregoContenido) {
            File::delete($rutaTemporalZip);
            return back()->with('info', 'No se encontraron gastos para el período seleccionado.');
        }

        return response()->download($rutaTemporalZip)->deleteFileAfterSend(true);
    }
}
