@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Bienvenido a tu panel de contabilidad personal.</p>
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($totalIngresos, 0, ',', '.') }}</h3>
                    <p>Total de Ingresos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <a href="{{ route('ingresos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($totalGastos, 0, ',', '.') }}</h3>
                    <p>Total de Gastos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <a href="{{ route('gastos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($balance, 0, ',', '.') }}</h3>
                    <p>Balance Actual</p>
                </div>
                <div class="icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <a href="#" class="small-box-footer">&nbsp;</a>
            </div>
        </div>
    </div>

    <hr>

    <h2>Resumen de Tarjetas de Crédito</h2>
    <div class="row">
        @forelse ($resumenTarjetas as $resumen)
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title">{{ $resumen->nombre }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Deuda de este mes:</strong>
                            <span class="float-right badge bg-warning">${{ number_format($resumen->deuda_mes_actual, 0, ',', '.') }}</span>
                        </p>
                        <p class="card-text">
                            <strong>Deuda Total:</strong>
                            <span class="float-right badge bg-danger">${{ number_format($resumen->deuda_total, 0, ',', '.') }}</span>
                        </p>
                        <p class="card-text">
                            <strong>Cupo Disponible:</strong>
                            <span class="float-right badge bg-success">${{ number_format($resumen->cupo_disponible, 0, ',', '.') }}</span>
                        </p>
                        <p class="card-text">
                            <small class="text-muted">Cupo Total: ${{ number_format($resumen->cupo_total, 0, ',', '.') }}</small>
                        </p>
                    </div>
                    <div class="card-footer">
                         <a href="{{ route('gastos_tarjeta.index', ['tarjeta' => $resumen->id]) }}" class="btn btn-primary btn-sm">Ver Gastos</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No tienes tarjetas de crédito registradas. <a href="{{ route('tarjetas.create') }}">¡Agrega una ahora!</a>
                </div>
            </div>
        @endforelse
    </div>
@stop
