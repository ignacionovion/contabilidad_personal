@extends('adminlte::page')

@section('title', 'Editar Tarjeta de Crédito')

@section('content_header')
    <h1>Editar Tarjeta de Crédito</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('tarjetas.update', $tarjeta) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nombre">Nombre de la Tarjeta</label>
                    <input type="text" name="nombre" class="form-control" id="nombre" value="{{ old('nombre', $tarjeta->nombre) }}" required>
                </div>
                <div class="form-group">
                    <label for="cupo_total">Cupo Total</label>
                    <input type="text" name="cupo_total" class="form-control" id="cupo_total" value="{{ old('cupo_total', $tarjeta->cupo_total) }}" required>
                </div>
                <div class="form-group">
                    <label for="dia_facturacion">Día de Facturación (1-31)</label>
                    <input type="number" name="dia_facturacion" class="form-control" id="dia_facturacion" value="{{ old('dia_facturacion', $tarjeta->dia_facturacion) }}" min="1" max="31" required>
                </div>
                <div class="form-group">
                    <label for="dia_pago">Día de Pago (1-31)</label>
                    <input type="number" name="dia_pago" class="form-control" id="dia_pago" value="{{ old('dia_pago', $tarjeta->dia_pago) }}" min="1" max="31" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Tarjeta</button>
                <a href="{{ route('tarjetas.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const montoInput = document.getElementById('cupo_total');
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

            // Recalcular la posición del cursor
            let newCursorPosition = cursorPosition + (formattedValue.length - originalValue.length);
            e.target.setSelectionRange(newCursorPosition, newCursorPosition);
        });

        form.addEventListener('submit', function (e) {
            let rawValue = montoInput.value.replace(/\./g, '');
            montoInput.value = rawValue;
        });

        // Formatear el valor inicial si existe
        if (montoInput.value) {
            montoInput.value = formatCurrency(montoInput.value);
        }
    });
</script>
@endpush
