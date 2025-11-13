<nav class="sidebar shadow-lg d-none d-lg-flex flex-column">
    <div class="d-flex flex-column h-100">
       {{-- 1. Logo/Título (AQUÍ ESTÁ LA MODIFICACIÓN) --}}
        <div class="sidebar-header text-center py-4 border-bottom border-secondary mb-3">
            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                <img src="{{ asset('images/LOGO-sin-eslogan.png') }}" alt="SquadUp Logo" class="img-fluid" style="max-height: 150px;">
            </a>
            <span class="text-muted small d-block mt-1">El Deporte sin Deudas</span>
        </div>

        {{-- 2. Enlaces Principales --}}
        <ul class="nav nav-pills flex-column mb-auto">

            {{-- Dashboard --}}
            <li class="nav-item mb-2">
                <a href="{{ route('dashboard') }}" class="nav-link py-2 px-3 text-white">
                    <i class="bi bi-speedometer2 me-3 fs-5"></i>
                    Dashboard
                </a>
            </li>

            {{-- Explorar/Matchmaking (RF7, RF8) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('events.explore') }}" class="nav-link py-2 px-3 text-white">
                    <i class="bi bi-compass me-3 fs-5"></i>
                    Explorar Eventos
                </a>
            </li>

            {{-- Mis Grupos (RF2, RF14) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('groups.index') }}" class="nav-link py-2 px-3 text-white">
                    <i class="bi bi-people me-3 fs-5"></i>
                    Mis Grupos
                </a>
            </li>

            {{-- Crear Evento --}}
            <li class="nav-item mb-2">
                <a href="{{ route('event.create') }}" class="nav-link py-2 px-3 text-white">
                    <i class="bi bi-calendar-plus me-3 fs-5"></i>
                    Crear Evento
                </a>
            </li>
        </ul>

        {{-- 3. Sección de Usuario y Logout --}}
        <div class="mt-auto pt-3 border-top border-secondary">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-person-circle me-3 fs-4 text-white"></i>
                <div>
                    <span class="d-block text-white fw-bold">{{ Auth::user()->name }}</span>
                    <span class="text-muted small">{{ Auth::user()->email }}</span>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- Estilos para la barra lateral (Se recomienda mantenerlos aquí o en app.blade.php) --}}
<style>
    .sidebar .nav-link {
        transition: background-color 0.2s, color 0.2s;
        border-radius: 0.5rem;
    }
    .sidebar .nav-link:hover {
        background-color: #343a40; /* Hover oscuro */
    }
    .sidebar .nav-link.active {
        background-color: #198754; /* Fondo verde para activo (Success) */
        color: white !important;
        font-weight: bold;
    }
</style>
