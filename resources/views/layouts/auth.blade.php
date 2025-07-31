<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Styles -->
    <style>
        .auth-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        .image-panel {
            flex: 1;
            background: url("{{ asset('images/contabilidad_publica_privada_1200x630.original.jpg') }}") no-repeat center center;
            background-size: cover;
        }
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background-color: #f8f9fa;
        }
        .form-wrapper {
            width: 100%;
            max-width: 400px;
        }
        .form-wrapper h1 {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .form-wrapper .form-label {
            font-weight: 500;
        }
        .form-wrapper .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .form-wrapper .form-control {
            padding: 12px;
        }
        .form-wrapper .links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }
        body {
            padding: 0 !important;
            margin: 0 !important;
            background-color: #f8f9fa; /* Color de fondo consistente */
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
