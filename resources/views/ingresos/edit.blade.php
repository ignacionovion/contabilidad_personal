@extends('adminlte::page')

@section('title', 'Editar Ingreso')

@section('content_header')
    <h1>Editar Ingreso</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('ingresos.update', $ingreso) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="monto">Monto</label>
                    <input type="text" name="monto" class="form-control" id="monto" value="{{ old('monto', number_format($ingreso->monto, 0, ',', '.')) }}" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" id="descripcion" value="{{ old('descripcion', $ingreso->descripcion) }}">
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" class="form-control" id="fecha" value="{{ old('fecha', $ingreso->fecha) }}">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('ingresos.index') }}" class="btn btn-secondary">Cancelar</a>
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
            let cursorPosition = e.target.selectionStart;
            let originalLength = e.target.value.length;
            let formattedValue = formatCurrency(e.target.value);
            e.target.value = formattedValue;
            let newLength = e.target.value.length;
            cursorPosition = cursorPosition + (newLength - originalLength);
            e.target.setSelectionRange(cursorPosition, cursorPosition);
        });

        form.addEventListener('submit', function (e) {
            let rawValue = montoInput.value.replace(/\./g, '');
            montoInput.value = rawValue;
        });
    });
</script>
@endpush
