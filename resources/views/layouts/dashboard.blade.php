<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/biuma_logo_b.png') }}">
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
            --primary-color: #bf3a29;
            --secondary-color: #d64a39;
            /* Lighter declination */
            --button-color: #bf3a29;
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
    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop d-xl-none" id="sidebarBackdrop"></div>

    <div class="sidebar" id="mainSidebar">
        <!-- Sidebar Profile / Brand -->
        <div class="sidebar-brand text-center">
            @php
                $currentEtablissement = auth()->user()->etablissement;
                if (auth()->user()->isManager() && session('manager_view_site_id')) {
                    $contextSite = \App\Models\Etablissement::find(session('manager_view_site_id'));
                    if ($contextSite) {
                        $currentEtablissement = $contextSite;
                    }
                }
            @endphp

            @if(auth()->check() && auth()->user()->isSuperAdmin())
                <img src="{{ asset('images/biuma_logo_blanck.png') }}" alt="Biuma" class="img-fluid"
                    style="height: 60px; object-fit: contain;">
            @elseif($currentEtablissement && $currentEtablissement->logo)
                <div class="position-relative d-inline-block">
                    <!-- Fix: Ensure asset path points to 'images/' disk root, not 'storage/' -->
                    <img src="{{ asset('images/' . $currentEtablissement->logo) }}"
                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($currentEtablissement->nom) }}&background=ffffff&color=000000&rounded=true'; this.classList.add('rounded-circle');"
                        alt="Logo" class="mb-2 img-fluid rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(255,255,255,0.2);">
                </div>
                <h5 class="text-white mb-0 fw-bold mt-2" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    {{ $currentEtablissement->nom }}
                </h5>
            @else
                <div class="d-flex flex-column align-items-center">
                    <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold mb-2 position-relative"
                        style="width: 60px; height: 60px; border: 3px solid rgba(255,255,255,0.9); font-family: 'Outfit', sans-serif; font-size: 1.4rem; background-color: #ffffff !important; background-image: none !important; color: #000000 !important; box-shadow: 0 0 25px rgba(255,255,255,0.4);">
                        {{ strtoupper(substr($currentEtablissement->nom ?? "O'Menu", 0, 1)) }}{{ strtoupper(substr($currentEtablissement->nom ?? "O'Menu", 1, 1) ?? '') }}
                    </div>
                    <h5 class="text-white mb-0 fw-bold" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        {{ $currentEtablissement->nom ?? "O'Menu" }}
                    </h5>
                </div>
            @endif
        </div>

        <nav class="nav flex-column mt-4">
            @if(!auth()->user()->isSuperAdmin())
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>
                @if(auth()->user()->etablissement->hasModule('orders'))
                    <div class="nav-item">
                        <a href="{{ route('orders.create') }}"
                            class="nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle"></i>
                            <span>Nouvelle Commande</span>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->etablissement->hasModule('orders'))
                    <div class="nav-item">
                        <a href="{{ route('orders.index') }}"
                            class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                            <i class="fas fa-list-alt"></i>
                            <span>Commandes</span>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->etablissement->hasModule('products'))
                    <div class="nav-item">
                        <a href="{{ route('products.index') }}"
                            class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span>Produits</span>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->etablissement->hasModule('tables'))
                    <div class="nav-item">
                        <a href="{{ route('tables.index') }}"
                            class="nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                            <i class="fas fa-table"></i>
                            <span>Tables</span>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->etablissement->hasModule('inventory') && auth()->user()->isAdmin())
                    <div class="nav-item">
                        <a href="{{ route('suppliers.index') }}"
                            class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            <i class="fas fa-truck"></i>
                            <span>Fournisseurs</span>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->etablissement->hasModule('inventory'))
                    <div class="nav-item">
                        <a href="{{ route('stock.index') }}"
                            class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                            <i class="fas fa-cubes"></i>
                            <span>Stocks</span>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->etablissement->hasModule('caisses'))
                    <div class="nav-item">
                        <a href="{{ route('caisses.index') }}"
                            class="nav-link {{ request()->routeIs('caisses.*') ? 'active' : '' }}">
                            <i class="fas fa-cash-register"></i>
                            <span>Caisses</span>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->etablissement->hasModule('finance'))
                    <div class="nav-item">
                        <a href="{{ route('finance.index') }}"
                            class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                            <i class="fas fa-wallet"></i>
                            <span>Finance</span>
                        </a>
                    </div>
                @endif
                @if(auth()->user()->etablissement->hasModule('reports'))
                    <div class="nav-item">
                        <a href="{{ route('reports.index') }}"
                            class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Rapports</span>
                        </a>
                    </div>
                @endif
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

            @if(auth()->user()->isManager() && !auth()->user()->isSuperAdmin() && auth()->user()->etablissement->hasModule('pos'))
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manager.pos.index') ? 'active' : '' }}"
                        href="{{ route('manager.pos.index') }}">
                        <i class="fas fa-network-wired"></i>
                        <span>Points de Vente</span>
                    </a>
                </div>
            @endif

            @if(auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin())
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.*') && !request()->routeIs('settings.sections') && !request()->routeIs('settings.users') ? 'active' : '' }}"
                        href="{{ route('settings.restaurant') }}">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.backups') ? 'active' : '' }}"
                        href="{{ route('settings.backups') }}">
                        <i class="fas fa-shield-alt"></i>
                        <span>Sauvegardes</span>
                    </a>
                </div>
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
                @php $activeSession = auth()->user()->activeSession(); @endphp
                @if(auth()->user()->etablissement)
                    @php 
                        $parent = auth()->user()->parentEstablishment();
                    @endphp
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <p class="text-muted mb-0 small">
                            <i class="fas fa-store me-1"></i>
                            @if($parent && $parent->id != auth()->user()->etablissement_id)
                                <span class="fw-bold text-dark">{{ $parent->nom }}</span>
                                <i class="fas fa-chevron-right mx-1 tiny-text opacity-50"></i>
                            @endif
                            {{ auth()->user()->etablissement->nom }}
                        </p>
                        @if($activeSession)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                style="font-size: 0.7rem;">
                                <i class="fas fa-cash-register me-1"></i> {{ $activeSession->caisse->nom }}
                            </span>
                        @elseif(auth()->user()->caisse)
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"
                                style="font-size: 0.7rem;">
                                <i class="fas fa-cash-register me-1"></i> {{ auth()->user()->caisse->nom }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3">
                @if(auth()->user()->isManager() && request()->routeIs('dashboard'))
                    <livewire:admin.dashboard.site-switcher />
                @endif

            <div class="user-profile d-flex align-items-center gap-3">
                <button class="btn btn-light d-xl-none" type="button" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="dropdown">
                    <div class="user-dropdown d-flex align-items-center gap-2 cursor-pointer" data-bs-toggle="dropdown"
                        aria-expanded="false" style="cursor: pointer;">
                        <div class="user-avatar bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
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
                        @if(auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin())
                            <li><a class="dropdown-item py-2" href="{{ route('settings.restaurant') }}"><i
                                        class="fas fa-cog me-2 text-muted"></i>Paramètres</a></li>
                        @endif
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
    <livewire:restaurant-employees />

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (mobileMenuBtn && sidebar && backdrop) {
                function toggleSidebar() {
                    sidebar.classList.toggle('show');
                    backdrop.classList.toggle('show');
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                }

                mobileMenuBtn.addEventListener('click', toggleSidebar);
                backdrop.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>
</html>