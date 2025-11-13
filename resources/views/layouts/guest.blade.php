<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SquadUp | @yield('title', 'Acceso')</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Estilos personalizados --}}
    <style>
        .custom-bg-dark {
            background-color: #212529 !important; /* El color oscuro de tu navegación */
        }
        .custom-text-green {
            color: #198754 !important; /* Tu color de acento */
        }
        .btn-custom-green {
            background-color: #198754;
            color: white;
            border-color: #198754;
        }
        .btn-custom-green:hover {
            background-color: #157347;
            border-color: #157347;
            color: white;
        }
    </style>
</head>
<body>
    {{-- Contenedor principal: Ocupa el 100% de la altura de la pantalla --}}
    <div class="container-fluid">
        <div class="row g-0 min-vh-100">

            {{-- 1. COLUMNA IZQUIERDA: LOGO y BRANDING (Oculta en móviles, visible en escritorio) --}}
            <div class="col-lg-6 d-none d-lg-flex custom-bg-dark justify-content-center align-items-center text-white">
                <div class="text-center p-5">
                    {{-- Logo Grande --}}
                    <img src="{{ asset('images/LOGO-sin-eslogan.png') }}" alt="SquadUp Logo" class="mb-4" style="max-height: 300px; width: auto;">

                    {{-- Texto de Marca --}}
                    <p class="lead custom-text-green">El Deporte sin Deudas. Solo preocúpate de jugar.</p>
                </div>
            </div>

            {{-- 2. COLUMNA DERECHA: FORMULARIO DE ACCESO --}}
            <div class="col-lg-6 d-flex bg-white justify-content-center align-items-center">
                <div class="w-100 p-4" style="max-width: 440px;">

                    {{-- Logo Visible en Móviles (Pequeño) --}}
                    <div class="text-center mb-4 d-lg-none">
                        <img src="{{ asset('images/LOGO-sin-eslogan.png') }}" alt="SquadUp Logo" class="mb-2" style="max-height: 60px; width: auto;">
                    </div>

                    {{-- Contenedor del Formulario (Inyectado desde login.blade.php) --}}
                    <div class="card shadow-lg border-0" style="border-radius: 1rem;">
                        <div class="card-body p-4 p-md-5">
                            @yield('content')
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
