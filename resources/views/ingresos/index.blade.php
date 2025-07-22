@extends('adminlte::page')

@section('title', 'Ingresos')

@section('content_header')
    <h1>Lista de Ingresos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('ingresos.create') }}" class="btn btn-primary">Registrar Nuevo Ingreso</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Monto</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ingresos as $ingreso)
                        <tr>
                            <td>{{ $ingreso->id }}</td>
                            <td>${{ number_format($ingreso->monto, 0, ',', '.') }}</td>
                            <td>{{ $ingreso->descripcion }}</td>
                            <td>{{ $ingreso->fecha }}</td>
                            <td>
                                <a href="{{ route('ingresos.edit', $ingreso) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('ingresos.destroy', $ingreso) }}" method="POST" style="display:inline-block;">
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
