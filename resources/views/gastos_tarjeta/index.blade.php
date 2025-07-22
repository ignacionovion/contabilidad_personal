@extends('adminlte::page')

@section('title', 'Gastos de Tarjeta')

@section('content_header')
    <h1>Gastos de la Tarjeta: {{ $tarjeta->nombre }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('gastos_tarjeta.create', ['tarjeta' => $tarjeta->id]) }}" class="btn btn-primary">Agregar Nuevo Gasto</a>
            <a href="{{ route('tarjetas.index') }}" class="btn btn-secondary">Volver a Tarjetas</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Monto Total</th>
                        <th>Valor Cuota</th>
                        <th>Progreso</th>
                        <th>Fecha Compra</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gastos as $gasto)
                        <tr>
                            <td>{{ $gasto->descripcion }}</td>
                            <td>${{ number_format($gasto->monto_total, 0, ',', '.') }}</td>
                            <td>${{ number_format($gasto->monto_cuota, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-info">{{ $gasto->cuotas_pagadas }} / {{ $gasto->total_cuotas }} pagadas</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($gasto->fecha_compra)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Ver Detalle"
                                        data-toggle="modal" data-target="#modal-detalle-cuotas"
                                        data-cuotas='{{ $gasto->cuotas_detalle }}'
                                        data-descripcion="{{ $gasto->descripcion }}">
                                    <i class="fa fa-fw fa-eye"></i>
                                </button>
                                <a href="{{ route('gastos_tarjeta.edit', $gasto->id_representativo) }}" class="btn btn-xs btn-default text-warning mx-1 shadow" title="Editar">
                                    <i class="fa fa-fw fa-pen"></i>
                                </a>
                                <form action="{{ route('gastos_tarjeta.destroy', $gasto->id_representativo) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar esta compra y todas sus cuotas?')">
                                        <i class="fa fa-fw fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay gastos registrados para esta tarjeta.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

{{-- Modal para Detalle de Cuotas --}}
<div class="modal fade" id="modal-detalle-cuotas" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Detalle de Cuotas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 id="modal-descripcion-compra" class="font-weight-bold mb-3"></h6>
                <ul class="list-group" id="lista-cuotas">
                    {{-- Las cuotas se insertarán aquí con JavaScript --}}
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function() {
    $('#modal-detalle-cuotas').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Botón que activó el modal
        var cuotas = button.data('cuotas'); // Extraer info de data-*
        var descripcion = button.data('descripcion');

        var modal = $(this);
        modal.find('#modal-descripcion-compra').text('Compra: ' + descripcion);

        var listaCuotas = modal.find('#lista-cuotas');
        listaCuotas.empty(); // Limpiar contenido anterior

        cuotas.forEach(function(cuota) {
            var estadoIcono = cuota.pagada
                ? '<i class="fas fa-check-circle text-success mr-2"></i><span class="text-success font-weight-bold">Pagada</span>'
                : '<i class="fas fa-clock text-warning mr-2"></i><span class="text-muted">Pendiente</span>';

            var item = '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                       '<span><strong>Cuota ' + cuota.numero_cuota + '</strong> - ' + cuota.fecha + '</span>' +
                       '<span>$' + new Intl.NumberFormat("de-DE").format(cuota.monto_cuota) + '</span>' +
                       '<span>' + estadoIcono + '</span>' +
                       '</li>';
            listaCuotas.append(item);
        });
    });
});
</script>
@endpush
