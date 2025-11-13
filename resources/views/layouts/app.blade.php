<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SquadUp | @yield('title', 'El Deporte sin Deudas')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.ico') }}" alt="SquadUp Logo"">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Estilos Personalizados para la Navegación Vertical --}}
    <style>
        :root {
            --sidebar-width: 280px; /* Ancho de la barra lateral */
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            padding: 1rem;
            background-color: #212529; /* Fondo oscuro (Dark) */
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            background-color: #f8f9fa; /* Fondo claro para el contenido */
            min-height: 100vh;
        }

        /* Ajuste para pantallas pequeñas (móvil/tablet) */
        @media (max-width: 992px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
{{-- 1. SOLUCIÓN ROBUSTA: Pasamos la ruta 'home' a un atributo de datos en el body --}}
<body @auth data-home-route="{{ route('dashboard') }}" @endauth>
    @auth
        {{-- Incluye la barra lateral: layouts.navigation --}}
        @include('layouts.navigation')

        {{-- Contenido principal --}}
        <div class="main-content">
            @yield('content')
        </div>
    @else
        {{-- Si no está autenticado (Login/Register), muestra solo el contenido --}}
        @yield('content')
    @endauth

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- 2. SCRIPT DE RESALTADO ACTIVO --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Leemos la ruta Home del atributo data-home-route (SOLUCIÓN al error de linter)
            const homeRouteUrl = document.body.dataset.homeRoute;

            // Obtiene la URL actual, eliminando parámetros de consulta
            const currentUrl = window.location.href.split('?')[0];

            document.querySelectorAll('.nav-link').forEach(link => {
                const linkHref = link.getAttribute('href');

                // Compara el href del enlace con la URL actual
                if (linkHref === currentUrl) {
                    link.classList.add('active');
                    return;
                }

                // Caso especial para el dashboard (Home):
                if (linkHref === homeRouteUrl && currentUrl === homeRouteUrl) {
                    link.classList.add('active');
                    return;
                }
            });
        });
    </script>
</body>
</html>
