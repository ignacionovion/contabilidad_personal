@extends('adminlte::page')

@section('title', 'Registrar Gasto')

@section('content_header')
    <h1>Registrar Nuevo Gasto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('gastos.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="monto">Monto</label>
                    <input type="text" name="monto" class="form-control" id="monto" placeholder="Ingrese el monto" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" id="descripcion" placeholder="Ingrese una descripción">
                </div>
                <div class="form-group">
                    <label for="categoria_id">Categoría</label>
                    <select name="categoria_id" id="categoria_id" class="form-control" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" class="form-control" id="fecha">
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
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
