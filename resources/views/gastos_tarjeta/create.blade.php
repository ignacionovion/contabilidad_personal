@extends('adminlte::page')

@section('title', 'Agregar Gasto de Tarjeta')

@section('content_header')
    <h1>Agregar Gasto a la Tarjeta: {{ $tarjeta->nombre }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('gastos_tarjeta.store', ['tarjeta' => $tarjeta->id]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" id="descripcion" placeholder="Ej: Compra en supermercado" required>
                </div>
                <div class="form-group">
                    <label for="monto_total">Monto Total</label>
                    <input type="text" name="monto_total" class="form-control" id="monto_total" required>
                </div>
                <div class="form-group">
                    <label for="total_cuotas">Número de Cuotas</label>
                    <input type="number" name="total_cuotas" class="form-control" id="total_cuotas" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha de Compra</label>
                    <input type="date" name="fecha" class="form-control" id="fecha">
                    <small class="form-text text-muted">Si no seleccionas una fecha, se usará la fecha actual.</small>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Gasto</button>
                <a href="{{ route('gastos_tarjeta.index', ['tarjeta' => $tarjeta->id]) }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const montoInput = document.getElementById('monto_total');
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
