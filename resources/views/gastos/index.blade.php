@extends('adminlte::page')

@section('title', 'Gastos')

@section('content_header')
    <h1>Lista de Gastos</h1>
@stop

@section('content')
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
