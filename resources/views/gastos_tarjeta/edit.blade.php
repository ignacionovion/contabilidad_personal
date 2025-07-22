@extends('adminlte::page')

@section('title', 'Editar Gasto de Tarjeta')

@section('content_header')
    <h1>Editar Gasto de la Tarjeta: {{ $tarjeta->nombre }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('gastos_tarjeta.update', $gasto->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" id="descripcion" value="{{ old('descripcion', $gasto->descripcion) }}" required>
                </div>
                <div class="form-group">
                    <label for="monto">Monto Total</label>
                    <input type="text" name="monto" class="form-control" id="monto" value="{{ old('monto', $gasto->monto) }}">
                </div>
                <div class="form-group">
                    <label for="total_cuotas">Número de Cuotas</label>
                    <input type="number" name="total_cuotas" class="form-control" id="total_cuotas" value="{{ old('total_cuotas', $gasto->total_cuotas) }}" min="1" required>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha de Compra</label>
                    <input type="date" name="fecha" class="form-control" id="fecha" value="{{ old('fecha', $gasto->fecha->format('Y-m-d')) }}">
                    <small class="form-text text-muted">Esta fecha se usará como inicio para calcular las nuevas cuotas.</small>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Gasto</button>
                <a href="{{ route('gastos_tarjeta.index', ['tarjeta' => $tarjeta->id]) }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const montoInput = document.getElementById('monto');
        const form = montoInput.closest('form');

        const formatCurrency = (value) => {
            if (!value) return '';
            let number = parseInt(value.toString().replace(/\D/g, ''), 10);
            if (isNaN(number)) return '';
            return number.toLocaleString('es-CL');
        };

        montoInput.addEventListener('input', function (e) {
            let originalValue = e.target.value;
            let cursorPosition = e.target.selectionStart;
            let formattedValue = formatCurrency(originalValue);
            e.target.value = formattedValue;

            let newCursorPosition = cursorPosition + (formattedValue.length - originalValue.length);
            e.target.setSelectionRange(newCursorPosition, newCursorPosition);
        });

        form.addEventListener('submit', function (e) {
            let rawValue = montoInput.value.replace(/\./g, '');
            montoInput.value = rawValue;
        });

        if (montoInput.value) {
            montoInput.value = formatCurrency(montoInput.value);
        }
    });
</script>
@endpush
