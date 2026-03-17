<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SquadUp | @yield('title', 'El Deporte sin Deudas')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.ico') }}" alt="SquadUp Logo">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-width: 280px;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            padding: 1rem;
            background-color: #212529;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {

            /* Sidebar oculta por defecto */
            .sidebar {
                display: none;
                position: absolute;
                width: 100%;
                height: auto;
                z-index: 2000;
            }

            /* Sidebar visible al activar */
            .sidebar.active {
                display: block;
            }

            /* Contenido ocupa todo el ancho */
            .main-content {
                margin-left: 0;
            }

            /* Botón hamburguesa visible */
            #toggleSidebar {
                position: relative;
                z-index: 3000;
            }
        }
    </style>
</head>

<body @auth data-home-route="{{ route('dashboard') }}" @endauth>

@auth

    {{-- BOTÓN HAMBURGUESA (ANTES DE LA SIDEBAR) --}}
    <button class="btn btn-dark d-lg-none m-3" id="toggleSidebar">
        <i class="bi bi-list"></i> Menú
    </button>

    {{-- SIDEBAR --}}
    @include('layouts.navigation')

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="main-content">

        @if (!request()->is('dashboard'))
            <div class="mb-3">
                <a href="{{ url()->previous() }}" class="text-primary text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Volver atrás
                </a>
            </div>
        @endif

        @yield('content')
    </div>

@else
    @yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }
});
</script>

</body>
</html>
