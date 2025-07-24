@extends('adminlte::page')

@section('title', 'Cuentas del Hogar')

@section('content_header')
    <h1>Administrar Cuentas del Hogar</h1>
@stop

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Añadir Nueva Cuenta</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('gastos-recurrentes.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre de la Cuenta</label>
                                <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ej: Luz, Agua, Internet" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="icono">Ícono (Opcional)</label>
                                <input type="text" name="icono" class="form-control" id="icono" placeholder="Ej: fas fa-lightbulb">
                                <small class="form-text text-muted">Busca íconos en <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a>.</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cuenta</button>
                </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Mis Cuentas del Hogar</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">Ícono</th>
                        <th>Nombre</th>
                        <th style="width: 150px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gastosRecurrentes as $gastoRecurrente)
                        <tr>
                            <td class="text-center"><i class="{{ $gastoRecurrente->icono ?? 'fas fa-question-circle' }}"></i></td>
                            <td>{{ $gastoRecurrente->nombre }}</td>
                            <td>
                                <a href="{{ route('gastos-recurrentes.edit', $gastoRecurrente->id) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                                                                                                <form action="{{ route('gastos-recurrentes.destroy', $gastoRecurrente->id) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar"><i class="fa fa-lg fa-fw fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No has añadido ninguna cuenta del hogar. ¡Empieza ahora!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (confirm('¿Estás seguro de que deseas eliminar esta cuenta?')) {
                    this.submit();
                }
            });
        });
    });
</script>
@stop
