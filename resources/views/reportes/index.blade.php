@extends('adminlte::page')

@section('title', 'Reportes Mensuales')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Generador de Reportes Mensuales</h1>
    </div>
@stop

@section('content')

@if(session('info'))
    <div class="alert alert-info">
        {{ session('info') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-body">
        <p class="card-text">
            Selecciona el mes y el año para generar un reporte detallado de los gastos de tus tarjetas de crédito. Se descargará un archivo <code>.zip</code> que contendrá una carpeta para el mes seleccionado y, dentro de ella, un archivo <code>.csv</code> por cada tarjeta con sus respectivos gastos.
        </p>

        <form action="{{ route('reportes.descargar') }}" method="POST" class="mt-4">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="mes">Mes:</label>
                        <select name="mes" id="mes" class="form-control">
                            @foreach(range(1, 12) as $mes)
                                <option value="{{ $mes }}" {{ $mes == date('m') ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($mes)->locale('es')->monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="anio">Año:</label>
                        <select name="anio" id="anio" class="form-control">
                            @foreach(range(date('Y'), date('Y') - 5) as $anio)
                                <option value="{{ $anio }}" {{ $anio == date('Y') ? 'selected' : '' }}>{{ $anio }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group w-100">
                         <button type="submit" class="btn btn-primary w-100"><i class="fas fa-fw fa-download mr-2"></i>Generar y Descargar Reporte</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
