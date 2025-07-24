@extends('adminlte::page')

@section('title', 'Gastos')

@section('content_header')
    <h1>Lista de Gastos</h1>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formatNumber = (number) => {
            return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(number);
        };

        document.querySelectorAll('.monto-formateado').forEach(input => {
            input.addEventListener('input', function (e) {
                let value = e.target.value.replace(/[^\d]/g, '');
                if (value) {
                    e.target.value = formatNumber(value).replace(/[^\d.,]/g, '');
                }
            });

            // Formatear al perder el foco para asegurar el formato correcto
            input.addEventListener('blur', function (e) {
                let value = e.target.value.replace(/[^\d]/g, '');
                if (value) {
                    e.target.value = formatNumber(value);
                }
            });
        });

        // Limpiar el formato antes de enviar el formulario
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                form.querySelectorAll('.monto-formateado').forEach(input => {
                    let value = input.value.replace(/[^\d]/g, '');
                    input.value = value;
                });
            });
        });
    });
</script>
@stop

@section('content')

    @if($gastosRecurrentes->isNotEmpty())
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Registrar Cuentas del Hogar</h3>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($gastosRecurrentes as $gastoRecurrente)
                    @php
                        $pagado = in_array($gastoRecurrente->id, $gastosPagadosEsteMes);
                    @endphp
                    <div class="col-md-4">
                        <form action="{{ route('gastos.storeRecurrente') }}" method="POST" class="form-inline mb-3">
                            @csrf
                            <input type="hidden" name="gasto_recurrente_id" value="{{ $gastoRecurrente->id }}">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="{{ $gastoRecurrente->icono ?? 'fas fa-file-invoice-dollar' }}"></i></span>
                                </div>
                                <input type="text" name="monto" class="form-control monto-formateado" 
                                       placeholder="{{ $pagado ? 'Pagado este mes' : $gastoRecurrente->nombre }}" 
                                       required {{ $pagado ? 'disabled' : '' }}>
                                <div class="input-group-append">
                                    <button type="submit" class="btn {{ $pagado ? 'btn-secondary' : 'btn-primary' }}" {{ $pagado ? 'disabled' : '' }}>
                                        {{ $pagado ? 'Registrado' : 'Registrar' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <a href="{{ route('gastos.create') }}" class="btn btn-primary">Registrar Nuevo Gasto</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Monto</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gastos as $gasto)
                        <tr>
                            <td>{{ $gasto->id }}</td>
                            <td>${{ number_format($gasto->monto, 0, ',', '.') }}</td>
                            <td>{{ $gasto->descripcion }}</td>
                            <td>{{ $gasto->categoria->nombre ?? 'Sin categoría' }}</td>
                            <td>{{ $gasto->fecha }}</td>
                            <td>
                                <a href="{{ route('gastos.edit', $gasto) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('gastos.destroy', $gasto) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
