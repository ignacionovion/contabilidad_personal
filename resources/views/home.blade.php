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

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Balance General (Últimos 12 Meses)</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="balance-mensual-chart" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Balance de Cuentas del Hogar (Últimos 12 Meses)</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="gastos-hogar-chart" height="220"></canvas>
                    </div>
                </div>
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

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function () {
        'use strict'

        var ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        };

        var mode = 'index';
        var intersect = true;

        function formatCurrency(value) {
            return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(value);
        }

        // --- Gráfico de Balance General Mensual ---
        var labels = @json($mesesLabels);
        var balanceData = @json($balanceMensualData);
        var $balanceChart = $('#balance-mensual-chart');

        var balanceChart = new Chart($balanceChart, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Balance Mensual',
                    data: balanceData,
                    borderColor: balanceData.map(v => v >= 0 ? 'rgba(40, 167, 69, 0.8)' : 'rgba(220, 53, 69, 0.8)'),
                    backgroundColor: balanceData.map(v => v >= 0 ? 'rgba(40, 167, 69, 0.2)' : 'rgba(220, 53, 69, 0.2)'),
                    pointBackgroundColor: balanceData.map(v => v >= 0 ? 'rgba(40, 167, 69, 1)' : 'rgba(220, 53, 69, 1)'),
                    fill: true,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect,
                    callbacks: { label: (item, data) => `${data.datasets[item.datasetIndex].label}: ${formatCurrency(item.yLabel)}` }
                },
                hover: { mode: mode, intersect: intersect },
                legend: { display: false },
                scales: {
                    yAxes: [{ 
                        gridLines: { display: true, color: '#dee2e6', zeroLineColor: '#6c757d' }, 
                        ticks: $.extend({ callback: (v) => v >= 1000 ? '$' + v/1000 + 'k' : (v <= -1000 ? '-$' + Math.abs(v)/1000 + 'k' : '$' + v) }, ticksStyle)
                    }],
                    xAxes: [{ display: true, gridLines: { display: false }, ticks: ticksStyle }]
                }
            }
        });

        // --- Gráfico de Gastos del Hogar ---
        var datasetsHogar = @json($gastosHogarDatasets);
        var $hogarChart = $('#gastos-hogar-chart');
        var hogarChart = new Chart($hogarChart, {
            type: 'line',
            data: {
                labels: labels, // Usamos las mismas etiquetas de meses
                datasets: datasetsHogar
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: { label: (item, data) => `${data.datasets[item.datasetIndex].label}: ${formatCurrency(item.yLabel)}` }
                },
                hover: { mode: 'index', intersect: false },
                legend: { display: true, position: 'top' }, // Leyenda activada
                scales: {
                    yAxes: [{ gridLines: { display: true }, ticks: $.extend({ beginAtZero: true, callback: (v) => v >= 1000 ? '$' + v/1000 + 'k' : '$' + v }, ticksStyle)}],
                    xAxes: [{ display: true, gridLines: { display: false }, ticks: ticksStyle }]
                }
            }
        });
    });
</script>
@endpush
