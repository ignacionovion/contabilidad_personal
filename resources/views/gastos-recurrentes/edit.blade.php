@extends('adminlte::page')

@section('title', 'Editar Cuenta del Hogar')

@section('content_header')
    <h1>Editar Cuenta: {{ $gastoRecurrente->nombre }}</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Modificar Datos de la Cuenta</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('gastos-recurrentes.update', $gastoRecurrente->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre de la Cuenta</label>
                            <input type="text" name="nombre" class="form-control" id="nombre" value="{{ old('nombre', $gastoRecurrente->nombre) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="icono">Ícono (Opcional)</label>
                            <input type="text" name="icono" class="form-control" id="icono" value="{{ old('icono', $gastoRecurrente->icono) }}" placeholder="Ej: fas fa-lightbulb">
                            <small class="form-text text-muted">Busca íconos en <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a>.</small>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Cuenta</button>
                <a href="{{ route('gastos-recurrentes.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop
