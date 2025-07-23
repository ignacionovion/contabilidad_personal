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
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Balance de Gastos (Últimos 12 Meses)</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="gastos-mensuales-chart" height="300"></canvas>
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
        }

        var mode = 'index'
        var intersect = true

        var labels = @json($mesesLabels);
        var data = @json($gastosMensualesData);

        var $salesChart = $('#gastos-mensuales-chart')
        var salesChart = new Chart($salesChart, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Gastos Totales',
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: '#007bff',
                        pointRadius: 3,
                        pointBackgroundColor: '#007bff',
                        pointBorderColor: '#007bff',
                        data: data
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var label = data.datasets[tooltipItem.datasetIndex].label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(tooltipItem.yLabel);
                            return label;
                        }
                    }
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            callback: function (value) {
                                if (value >= 1000) {
                                    return '$' + (value / 1000) + 'k';
                                }
                                return '$' + value;
                            }
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })
    })
</script>
@endpush
