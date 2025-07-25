@extends('adminlte::page')

@section('title', 'Ingresos')

@section('content_header')
    <h1>Gestión de Ingresos</h1>
@stop

@section('content')
    {{-- Panel para Gestionar Sueldo Fijo --}}
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Gestionar Sueldo Fijo</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('ingresos.actualizarSueldo') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sueldo">Monto del Sueldo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="text" name="sueldo" class="form-control" id="sueldo" value="{{ number_format($user->sueldo, 0, ',', '.') }}" placeholder="Ingrese el monto de su sueldo">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-start mt-3">
                         <div class="form-group">
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input" id="sueldo_activo" name="sueldo_activo" {{ $user->sueldo_activo ? 'checked' : '' }}>
                                <label class="custom-control-label" for="sueldo_activo">Incluir sueldo en el balance mensual</label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Sueldo</button>
            </form>
        </div>
    </div>



        <!-- Tarjeta de Ingresos Variables -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ingresos Variables</h3>
                        <div class="card-tools">
                            <a href="{{ route('ingresos.create') }}" class="btn btn-success">Añadir Ingreso Variable</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Monto</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ingresos as $ingreso)
                                    <tr>
                                        <td>${{ number_format($ingreso->monto, 0, ',', '.') }}</td>
                                        <td>{{ $ingreso->descripcion }}</td>
                                        <td>{{ $ingreso->fecha ? \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') : 'No especificada' }}</td>
                                        <td>
                                            <a href="{{ route('ingresos.edit', $ingreso->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                            <form action="{{ route('ingresos.destroy', $ingreso->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No hay ingresos variables registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function setupInputFormatter(inputId) {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/[^\d]/g, '');
                    e.target.value = formatNumber(value);
                });
            }
        }

        setupInputFormatter('sueldo');
        setupInputFormatter('monto_variable');
    });
</script>
@endpush
