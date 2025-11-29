<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Dashboard Styles -->
    <link rel="stylesheet" href="{{ asset('css/layouts/dashboard.css') }}">

</head>

<body>
    <div class="sidebar">
        <div class="sidebar-brand">
            <h3 class="text-white mb-0">
                <i class="fas fa-utensils me-2"></i> OMenu
            </h3>
        </div>
        <nav class="nav flex-column">
            <div class="nav-item">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Tableau de bord</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('orders.create') }}"
                    class="nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvelle Commande</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('orders.index') }}"
                    class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                    <i class="fas fa-list-alt"></i>
                    <span>Commandes</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('products.index') }}"
                    class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('tables.index') }}"
                    class="nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                    <i class="fas fa-table"></i>
                    <span>Tables</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-line"></i>
                    <span>Rapports</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0">@yield('title')</h4>
            </div>
            <div class="dropdown">
                <div class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">{{ auth()->user()->name }}</div>
                        <div style="color: #6b7280; font-size: 0.8rem;">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <i class="fas fa-chevron-down text-muted"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item"><i
                                    class="fas fa-sign-out-alt me-2"></i>Déconnexion </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <div class="content-area">
            @hasSection('breadcrumb')
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>

</html>