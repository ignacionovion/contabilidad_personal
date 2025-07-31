@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="image-panel"></div>
    <div class="form-panel">
        <div class="form-wrapper">
            <h1>Restablecer Contraseña</h1>
            <p class="text-muted mb-4">Ingresa tu correo electrónico y te enviaremos un enlace para que puedas recuperar tu cuenta.</p>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Correo Electrónico') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Enviar Enlace de Restablecimiento') }}
                    </button>
                </div>

                <p class="text-center mt-4 text-muted">
                    <a href="{{ route('login') }}">Volver a Iniciar Sesión</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
