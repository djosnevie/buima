@section('title', 'Gestion des Produits')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Produits</li>
@endsection

<div class="products-management-container w-100">
    <style>
        .segmented-control {
            background-color: #f1f3f5;
            border-radius: 8px;
            padding: 4px;
            display: inline-flex;
        }
        .segmented-btn {
            background: transparent;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.2s ease;
        }
        .segmented-btn:hover {
            color: #495057;
        }
        .segmented-btn.active {
            background-color: #fff;
            color: var(--primary-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .product-card-premium {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .product-card-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
            border-color: rgba(var(--primary-color-rgb), 0.3);
        }
        .img-wrapper {
            height: 160px;
            background-color: #f8f9fa;
            position: relative;
        }
        .img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #adb5bd;
        }
        .badge-type-entree { background-color: #d1fae5; color: #065f46; border: 1px solid #34d399;}
        .badge-type-plat { background-color: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;}
        .badge-type-boisson { background-color: #ede9fe; color: #5b21b6; border: 1px solid #c4b5fd;}
        .badge-type-dessert { background-color: #fce7f3; color: #9d174d; border: 1px solid #f9a8d4;}
        .badge-type-accompagnement { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d;}
        .badge-type-autre { background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db;}
    </style>

    @if (session()->has('error'))
        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Header Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        
        <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-center">
            <!-- Search -->
            <div class="position-relative">
                <i class="fas fa-search position-absolute text-muted" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                <input wire:model.live="search" type="text" class="form-control bg-white border-0 shadow-sm rounded-pill" placeholder="Rechercher un produit..." style="padding-left: 40px; min-width: 250px;">
            </div>
            
            <!-- Filters Segmented -->
            <div class="segmented-control shadow-sm bg-white">
                <button wire:click="setFilter('tous')" class="segmented-btn {{ $typeFilter === 'tous' ? 'active' : '' }}">Tous</button>
                <button wire:click="setFilter('boisson')" class="segmented-btn {{ $typeFilter === 'boisson' ? 'active' : '' }}"><i class="fas fa-glass-martini-alt me-1 d-none d-md-inline"></i> Boissons</button>
                <button wire:click="setFilter('plat')" class="segmented-btn {{ $typeFilter === 'plat' ? 'active' : '' }}"><i class="fas fa-utensils me-1 d-none d-md-inline"></i> Cuisine</button>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                <input type="file" id="importProduct" wire:model.live="importFile" style="display:none;" accept=".xlsx,.xls,.csv">
                <label for="importProduct" class="btn btn-light border shadow-sm mb-0 d-flex align-items-center" style="cursor: pointer;" title="Importer vos produits depuis Excel">
                    <i class="fas fa-file-import text-primary me-2"></i> Importer
                </label>
                
                <div wire:loading wire:target="importFile" class="spinner-border spinner-border-sm text-primary ms-2 align-self-center" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                
                <button wire:click="exportExcel" class="btn btn-light border shadow-sm d-flex align-items-center" style="color: #217346;" title="Télécharger le catalogue">
                    <i class="fas fa-file-excel me-2"></i> Exporter
                </button>
                <a href="{{ route('products.create') }}" class="btn btn-primary shadow-sm fw-bold d-flex align-items-center" style="background-color: var(--primary-color); border: none;">
                    <i class="fas fa-plus me-2"></i> Nouveau Produit
                </a>
            @endif
        </div>
    </div>

    <!-- Product Grid -->
    <div class="row g-4">
        @forelse($produits as $produit)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card product-card-premium h-100 rounded-4 overflow-hidden bg-white">
                    <!-- Image Wrapper -->
                    <div class="img-wrapper rounded-top-4 overflow-hidden">
                        @php
                            $isPlaceholder = str_contains($produit->image_url, 'ui-avatars.com') || empty($produit->image_url);
                        @endphp
                        @if(!$isPlaceholder)
                            <img src="{{ $produit->image_url }}" alt="{{ $produit->nom }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="img-placeholder" style="{{ !$isPlaceholder ? 'display: none;' : 'display: flex;' }}">
                            <i class="fas {{ $produit->type === 'boisson' ? 'fa-glass-martini-alt' : 'fa-utensils' }} mb-2" style="font-size: 3rem; opacity: 0.2;"></i>
                        </div>
                        
                        <!-- Badges Overlay -->
                        <div class="position-absolute d-flex flex-column gap-1" style="top: 10px; left: 10px;">
                            <span class="badge shadow-sm px-2 py-1 bg-white text-dark rounded-pill fw-bold border">
                                <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: {{ $produit->categorie->couleur ?? '#ccc' }};"></span>
                                {{ $produit->categorie->nom ?? 'Standard' }}
                            </span>
                            <span class="badge shadow-sm px-2 py-1 rounded-pill fw-bold badge-type-{{ strtolower($produit->type ?? 'autre') }}">
                                {{ ucfirst($produit->type ?? 'Autre') }}
                            </span>
                        </div>
                        
                        @if($produit->gestion_stock)
                            <div class="position-absolute shadow-sm badge bg-dark text-white rounded-pill px-2 py-1" style="bottom: 10px; right: 10px; font-size: 0.75rem;">
                                <i class="fas fa-cubes me-1"></i> Stock suivi
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 85%;" title="{{ $produit->nom }}">{{ $produit->nom }}</h5>
                            <!-- Availability Dot -->
                            <div class="rounded-circle shadow-sm flex-shrink-0" style="width: 12px; height: 12px; background-color: {{ $produit->disponible ? '#10B981' : '#EF4444' }};" title="{{ $produit->disponible ? 'Disponible' : 'Indisponible' }}"></div>
                        </div>
                        
                        <p class="text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                            {{ $produit->description ?? 'Aucune description renseignée pour ' . strtolower($produit->nom) . '.' }}
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-end mt-auto pt-3 border-top">
                            <div>
                                <span class="d-block small text-muted mb-1">Prix unitaire</span>
                                <h4 class="mb-0 fw-bold" style="color: var(--primary-color);">
                                    {{ number_format($produit->prix_vente, 0, ',', ' ') }} <span class="fs-6 text-muted">{{ auth()->user()->etablissement->devise_display ?? 'FCFA' }}</span>
                                </h4>
                            </div>
                            
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <div class="d-flex gap-2">
                                    <a href="{{ route('products.edit', $produit->id) }}" class="btn btn-sm btn-light border text-primary rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button wire:click="delete({{ $produit->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer définitivement cet article ?" class="btn btn-sm btn-light border text-danger rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center bg-white rounded-4 shadow-sm border mt-3">
                <i class="fas fa-box-open text-muted opacity-25" style="font-size: 5rem;"></i>
                <h4 class="mt-4 fw-bold text-muted">Aun article dans le catalogue</h4>
                <p class="text-muted">Créez votre premier produit pour qu'il s'affiche ici.</p>
                @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('products.create') }}" class="btn btn-primary mt-2 shadow-sm" style="background-color: var(--primary-color); border: none;">
                        Créer un article
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    @if($produits->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $produits->links('livewire.custom-pagination') }}
        </div>
    @endif
</div>