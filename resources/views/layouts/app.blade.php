<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transmetro Guatemala — @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-w: 250px;
            --sidebar-bg: #1a2e4a;
            --sidebar-hover: #243d60;
            --sidebar-active: #0d6efd;
            --topbar-h: 56px;
        }

        body { background: #f0f2f5; overflow-x: hidden; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform .25s ease;
        }
        #sidebar .sidebar-brand {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            background: rgba(0,0,0,.25);
            color: #fff;
            font-weight: 700;
            font-size: 1.05rem;
            gap: .6rem;
            flex-shrink: 0;
        }
        #sidebar .sidebar-brand .bus-icon {
            width: 34px; height: 34px;
            background: var(--sidebar-active);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        #sidebar .sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }
        #sidebar .sidebar-section {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.4);
            padding: .75rem 1.25rem .25rem;
        }
        #sidebar .nav-item a {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem 1.25rem;
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-size: .9rem;
            border-left: 3px solid transparent;
            transition: background .15s, color .15s, border-color .15s;
        }
        #sidebar .nav-item a:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        #sidebar .nav-item a.active {
            background: var(--sidebar-hover);
            color: #fff;
            border-left-color: var(--sidebar-active);
        }
        #sidebar .nav-item a .nav-icon {
            width: 22px;
            text-align: center;
            font-size: .95rem;
            opacity: .85;
        }
        #sidebar .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
        }
        #sidebar .user-info {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: .75rem;
        }
        #sidebar .user-avatar {
            width: 38px; height: 38px;
            background: var(--sidebar-active);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 1rem;
            flex-shrink: 0;
        }
        #sidebar .user-name {
            color: #fff;
            font-size: .85rem;
            font-weight: 600;
            line-height: 1.2;
        }
        #sidebar .user-role {
            font-size: .72rem;
            color: rgba(255,255,255,.5);
        }

        /* ── Topbar ── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            transition: left .25s ease;
        }
        #topbar .page-title {
            font-weight: 600;
            color: #1a2e4a;
            font-size: 1rem;
        }
        #topbar .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        /* ── Main content ── */
        #main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 1.5rem;
            min-height: calc(100vh - var(--topbar-h));
            transition: margin-left .25s ease;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; }
            .sidebar-overlay { display: block; }
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 1039;
        }

        /* ── Cards / utils ── */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.07);
        }
        .stat-card .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .page-header { margin-bottom: 1.5rem; }
        .page-header h4 { font-weight: 700; color: #1a2e4a; margin-bottom: .15rem; }
        .page-header .breadcrumb { font-size: .8rem; }
        .table th { font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; font-weight: 600; }

        @media print {
            #sidebar, #topbar, .no-print { display: none !important; }
            #main-content { margin: 0 !important; padding: 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Overlay móvil --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ══ SIDEBAR ══ --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="bus-icon"><i class="fas fa-bus-simple"></i></div>
        Transmetro GT
    </div>

    <div class="sidebar-body">

        {{-- GENERAL --}}
        <div class="sidebar-section">General</div>
        <ul class="nav flex-column list-unstyled">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                    Dashboard
                </a>
            </li>
        </ul>

        {{-- OPERACIONES (admin y supervisor) --}}
        @if(auth()->user()->esAdmin() || auth()->user()->esSupervisor())
        <div class="sidebar-section">Operaciones</div>
        <ul class="nav flex-column list-unstyled">
            <li class="nav-item">
                <a href="{{ route('lineas.index') }}" class="{{ request()->routeIs('lineas.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-route"></i></span>
                    Líneas
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('estaciones.index') }}" class="{{ request()->routeIs('estaciones.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-map-pin"></i></span>
                    Estaciones
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('buses.index') }}" class="{{ request()->routeIs('buses.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-bus"></i></span>
                    Buses
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pilotos.index') }}" class="{{ request()->routeIs('pilotos.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-id-card"></i></span>
                    Pilotos
                </a>
            </li>
        </ul>
        @endif

        {{-- OPERADOR: solo su estación --}}
        @if(auth()->user()->esOperador())
        <div class="sidebar-section">Mi Estación</div>
        <ul class="nav flex-column list-unstyled">
            <li class="nav-item">
                <a href="{{ route('estaciones.index') }}" class="{{ request()->routeIs('estaciones.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-map-pin"></i></span>
                    Estaciones
                </a>
            </li>
        </ul>
        @endif

        {{-- ALERTAS (todos) --}}
        <div class="sidebar-section">Monitoreo</div>
        <ul class="nav flex-column list-unstyled">
            <li class="nav-item">
                <a href="{{ route('alertas.index') }}" class="{{ request()->routeIs('alertas.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-bell"></i></span>
                    Alertas
                    @php
                        $user = auth()->user();
                        $pendientes = ($user->esOperador() && $user->estacion_id)
                            ? \App\Models\Alerta::where('atendida', false)->where('estacion_id', $user->estacion_id)->count()
                            : \App\Models\Alerta::where('atendida', false)->count();
                    @endphp
                    @if($pendientes > 0)
                        <span class="badge bg-danger ms-auto">{{ $pendientes }}</span>
                    @endif
                </a>
            </li>
        </ul>

        {{-- REPORTES (admin) --}}
        @if(auth()->user()->esAdmin())
        <div class="sidebar-section">Administración</div>
        <ul class="nav flex-column list-unstyled">
            <li class="nav-item">
                <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-file-chart-column"></i></span>
                    Reportes
                </a>
            </li>
        </ul>
        @endif

    </div>{{-- /sidebar-body --}}

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-circle-user"></i>
            </div>
            <div>
                <div class="user-name">{{ auth()->user()->nombre }}</div>
                <div class="user-role">
                    <span class="badge bg-warning text-dark">{{ ucfirst(auth()->user()->rol) }}</span>
                    @if(auth()->user()->estacion)
                        &nbsp;{{ auth()->user()->estacion->nombre }}
                    @endif
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                <i class="fas fa-right-from-bracket me-1"></i> Cerrar Sesión
            </button>
        </form>
    </div>
</nav>

{{-- ══ TOPBAR ══ --}}
<header id="topbar">
    <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    <span class="page-title">
        <i class="{{ View::hasSection('page_icon') ? '' : 'fas fa-circle-dot' }} me-1"></i>
        @yield('title', 'Dashboard')
    </span>
    <div class="topbar-right">
        <span class="text-muted small d-none d-md-inline">
            {{ now()->format('d/m/Y') }}
        </span>
    </div>
</header>

{{-- ══ CONTENIDO PRINCIPAL ══ --}}
<main id="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-check me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-exclamation me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggle   = document.getElementById('sidebarToggle');

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.style.display = 'none';
    });
</script>
@stack('scripts')
</body>
</html>
