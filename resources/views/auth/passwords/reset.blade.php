@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="image-panel"></div>
    <div class="form-panel">
        <div class="form-wrapper">
            <h1>Crea tu Nueva Contraseña</h1>
            <p class="text-muted mb-4">Define una nueva contraseña segura para tu cuenta.</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Correo Electrónico') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Nueva Contraseña') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-confirm" class="form-label">{{ __('Confirmar Nueva Contraseña') }}</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Restablecer Contraseña') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
