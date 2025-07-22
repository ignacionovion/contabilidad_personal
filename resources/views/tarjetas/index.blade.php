@extends('adminlte::page')

@section('title', 'Tarjetas de Crédito')

@section('content_header')
    <h1>Mis Tarjetas de Crédito</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('tarjetas.create') }}" class="btn btn-primary">Agregar Nueva Tarjeta</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cupo Total</th>
                        <th>Día de Facturación</th>
                        <th>Día de Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tarjetas as $tarjeta)
                        <tr>
                            <td>{{ $tarjeta->nombre }}</td>
                            <td>${{ number_format($tarjeta->cupo_total, 0, ',', '.') }}</td>
                            <td>{{ $tarjeta->dia_facturacion }}</td>
                            <td>{{ $tarjeta->dia_pago }}</td>
                            <td>
                                <a href="{{ route('gastos_tarjeta.index', ['tarjeta' => $tarjeta->id]) }}" class="btn btn-sm btn-info">Ver Gastos</a>
                                <a href="{{ route('tarjetas.edit', $tarjeta) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('tarjetas.destroy', $tarjeta) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar esta tarjeta?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No tienes tarjetas de crédito registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
