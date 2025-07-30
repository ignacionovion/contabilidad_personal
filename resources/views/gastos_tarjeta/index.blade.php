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
                        <tr id="gasto-row-{{ $gasto->id_representativo }}">
                            <td>{{ $gasto->descripcion }}</td>
                            <td>${{ number_format($gasto->monto_total, 0, ',', '.') }}</td>
                            <td>${{ number_format($gasto->monto_cuota, 0, ',', '.') }}</td>
                            <td>
                                <span id="progreso-gasto-{{ $gasto->id_representativo }}" class="badge badge-info">{{ $gasto->cuotas_pagadas }} / {{ $gasto->total_cuotas }} pagadas</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($gasto->fecha_compra)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Ver Detalle"
                                        data-toggle="modal" data-target="#modal-detalle-cuotas"
                                        data-cuotas='{{ $gasto->cuotas_detalle }}'
                                        data-descripcion="{{ $gasto->descripcion }}"
                                        data-gasto-padre-id="{{ $gasto->id_representativo }}">
                                    <i class="fa fa-fw fa-eye"></i>
                                </button>
                                <a href="{{ route('gastos_tarjeta.edit', $gasto->id_representativo) }}" class="btn btn-sm btn-warning">Editar
                                    <i class="fa fa-fw fa-pen"></i>
                                </a>
                                <form action="{{ route('gastos_tarjeta.destroy', $gasto->id_representativo) }}" method="POST" class="d-inline form-delete">
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
        var button = $(event.relatedTarget); 
        var gastoPadreId = button.data('gasto-padre-id');
        // Forzamos la lectura desde el atributo para evitar la caché de jQuery y obtener siempre los datos más frescos.
        var cuotas = JSON.parse(button.attr('data-cuotas'));
        var descripcion = button.data('descripcion');

        var modal = $(this);
        modal.find('.modal-title').text('Detalle de Cuotas: ' + descripcion);

        var modalBody = modal.find('.modal-body');
        modalBody.empty(); 

        var list = $('<ul class="list-group"></ul>');
        cuotas.forEach(function(cuota) {
            const fechaCuota = new Date(cuota.fecha);
            const hoy = new Date();
            const inicioMesSiguiente = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 1);

            const isEditable = fechaCuota < inicioMesSiguiente;
            const disabledAttribute = isEditable ? '' : 'disabled';

            var listItem = `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Cuota ${cuota.numero_cuota}</strong> - ${new Date(cuota.fecha).toLocaleDateString('es-CL', { year: 'numeric', month: '2-digit', day: '2-digit' })}
                    </span>
                    <strong>$${new Intl.NumberFormat('es-CL').format(cuota.monto_cuota)}</strong>
                    <select class="custom-select custom-select-sm estado-cuota" data-gasto-id="${cuota.id}" data-gasto-padre-id="${gastoPadreId}" data-estado-original="${cuota.estado}" style="width: 120px;" ${disabledAttribute}>
                        <option value="Pagada" ${cuota.estado === 'Pagada' ? 'selected' : ''}>Pagada</option>
                        <option value="Pendiente" ${cuota.estado === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="No Pagada" ${cuota.estado === 'No Pagada' ? 'selected' : ''}>No Pagada</option>
                    </select>
                </li>`;
            list.append(listItem);
        });

        modalBody.append(list);
    });

    // Listener para los cambios en el estado de la cuota
    $(document).on('change', '.estado-cuota', function() {
        const select = $(this);
        const gastoId = select.data('gasto-id');
        const gastoPadreId = select.data('gasto-padre-id');
        const nuevoEstado = select.val();

        // Deshabilitar el select mientras se procesa
        select.prop('disabled', true);

        fetch(`/gastos_tarjeta/${gastoId}/estado`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ estado: nuevoEstado })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Si el guardado fue exitoso, actualizamos el estado original para el futuro
                select.data('estado-original', nuevoEstado);

                // Actualizar el contador de progreso en la tabla principal, apuntando al ID específico
                const progressBadge = $(`#progreso-gasto-${gastoPadreId}`);
                if (progressBadge.length) {
                    progressBadge.text(data.progreso_texto);
                }

                // Actualizamos los datos del botón para que la próxima vez que se abra el modal, esté actualizado.
                const button = $(`#gasto-row-${gastoPadreId}`).find('button[data-toggle="modal"]');
                let cuotas = JSON.parse(button.attr('data-cuotas')); // Leer siempre el valor más fresco

                const cuotaIndex = cuotas.findIndex(c => c.id === gastoId);
                if (cuotaIndex > -1) {
                    // Usamos la variable 'nuevoEstado' que contiene la selección del usuario.
                    cuotas[cuotaIndex].estado = nuevoEstado;
                    
                    // Re-serializar y actualizar el atributo data-cuotas
                    button.attr('data-cuotas', JSON.stringify(cuotas));
                }

            } else {
                Swal.fire('Error', data.message || 'No se pudo actualizar el estado.', 'error');
                select.val(select.data('estado-original')); // Revertir al estado original guardado
            }
        })
        .catch(error => {
            console.error('Error de red:', error);
            Swal.fire('Error', 'Ocurrió un error de red. Por favor, inténtalo de nuevo.', 'error');
            select.val(select.data('estado-original')); // Revertir al estado original guardado
        })
        .finally(() => {
            // Siempre volver a habilitar el select
            select.prop('disabled', false);
        });
    });
});
</script>
@endpush
