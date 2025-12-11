<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    @livewireStyles

    <style>
        :root {
            --primary-color:
                {{ auth()->user()->etablissement->theme_color ?? '#ff6b35' }}
            ;
            --secondary-color:
                {{ auth()->user()->etablissement->secondary_color ?? '#ff9f43' }}
            ;
            --button-color:
                {{ auth()->user()->etablissement->button_color ?? '#ff6b35' }}
            ;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        }

        /* Default Button Gradient */
        .btn-primary {
            background-image: linear-gradient(135deg, var(--button-color), var(--secondary-color)) !important;
            border: none !important;
            color: white !important;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        /* Ensure text is readable if secondary is white */
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            filter: brightness(1.1);
        }

        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .top-header {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-brand text-center">
            @if(auth()->user()->etablissement && auth()->user()->etablissement->logo)
                <img src="{{ asset('images/' . auth()->user()->etablissement->logo) }}"
                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->etablissement->nom) }}&background=random';"
                    alt="Logo" class="mb-2" style="width: 50px; height: 50px; object-fit: cover;">
                <h5 class="text-white mb-0 fw-bold">{{ auth()->user()->etablissement->nom }}</h5>
            @else
                <h3 class="text-white mb-0">
                    <i class="fas fa-utensils me-2"></i> {{ auth()->user()->etablissement->nom ?? "O'Menu" }}
                </h3>
            @endif
        </div>
        <nav class="nav flex-column">
            @if(!auth()->user()->isSuperAdmin())
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
            @endif
            @if(auth()->user()->isSuperAdmin())
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('super_admin.dashboard') }}">
                        <i class="fas fa-store"></i>
                        <span>Restaurants</span>
                    </a>
                </div>
            @endif
            @if(auth()->user()->isAdmin())
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.*') && !request()->routeIs('settings.sections') && !request()->routeIs('settings.users') ? 'active' : '' }}"
                        href="{{ route('settings.restaurant') }}">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </div>
            @endif

            @if(auth()->user()->isAdmin())

                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.sections') ? 'active' : '' }}"
                        href="{{ route('settings.sections') }}">
                        <i class="fas fa-layer-group"></i>
                        <span>Sections</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.users') ? 'active' : '' }}"
                        href="{{ route('settings.users') }}">
                        <i class="fas fa-users"></i>
                        <span>Personnel</span>
                    </a>
                </div>
            @endif
        </nav>
    </div>

    <div class="main-content">
        <!-- Distinct Top Header -->
        <div class="top-header">
            <div>
                <h2 class="h5 fw-bold mb-0 text-dark">@yield('title', 'Tableau de bord')</h2>
                @if(auth()->user()->etablissement)
                    <p class="text-muted mb-0 small"><i class="fas fa-store me-1"></i>
                        {{ auth()->user()->etablissement->nom }}</p>
                @endif
            </div>

            <div class="user-profile d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="dropdown">
                    <div class="user-dropdown d-flex align-items-center gap-2 cursor-pointer" data-bs-toggle="dropdown"
                        aria-expanded="false" style="cursor: pointer;">
                        <div class="user-avatar bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold"
                            style="width: 40px; height: 40px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block text-end">
                            <div style="font-weight: 600; font-size: 0.9rem;">{{ auth()->user()->name }}</div>
                            <div style="color: #6b7280; font-size: 0.8rem;">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                        <i class="fas fa-chevron-down text-muted small ms-1"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li><a class="dropdown-item py-2" href="#"
                                onclick="Livewire.dispatch('open-profile-modal'); return false;"><i
                                    class="fas fa-user me-2 text-muted"></i>Profil</a>
                        </li>
                        <li><a class="dropdown-item py-2" href="{{ route('settings.restaurant') }}"><i
                                    class="fas fa-cog me-2 text-muted"></i>Paramètres</a></li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger"><i
                                        class="fas fa-sign-out-alt me-2"></i>Déconnexion </button>
                            </form>
                        </li>
                    </ul>
                </div>
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

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif


            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    <livewire:profile.edit-profile />
</body>

</html>