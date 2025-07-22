@extends('adminlte::page')

@section('title', 'Editar Gasto')

@section('content_header')
    <h1>Editar Gasto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('gastos.update', $gasto) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="monto">Monto</label>
                    <input type="text" name="monto" class="form-control" id="monto" value="{{ old('monto', number_format($gasto->monto, 0, ',', '.')) }}" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" id="descripcion" value="{{ old('descripcion', $gasto->descripcion) }}">
                </div>
                <div class="form-group">
                    <label for="categoria_id">Categoría</label>
                    <select name="categoria_id" id="categoria_id" class="form-control" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id', $gasto->categoria_id) == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" class="form-control" id="fecha" value="{{ old('fecha', $gasto->fecha) }}">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Cancelar</a>
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
