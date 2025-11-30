@section('title', 'Gestion des Produits')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Produits</li>
@endsection

<div class="products-management">
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="header-actions">
        <div class="left-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input wire:model.live="search" type="text" placeholder="Rechercher un produit...">
            </div>
            <div class="filter-group">
                <button wire:click="setFilter('tous')"
                    class="filter-btn {{ $typeFilter === 'tous' ? 'active' : '' }}">Tous</button>
                <button wire:click="setFilter('boisson')"
                    class="filter-btn {{ $typeFilter === 'boisson' ? 'active' : '' }}">Boissons</button>
                <button wire:click="setFilter('plat')"
                    class="filter-btn {{ $typeFilter === 'plat' ? 'active' : '' }}">Plats</button>
            </div>
        </div>

        <div class="right-actions">
            <button wire:click="toggleCategoryManager" class="btn-secondary">
                <i class="fas fa-tags"></i> Catégories
            </button>
            <a href="{{ route('products.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> Nouveau Produit
            </a>
        </div>
    </div>

    <div class="products-grid">
        @forelse($produits as $produit)
            <div class="product-card">
                <div class="product-image">
                    @if($produit->image)
                        <img src="{{ asset('public/' . $produit->image) }}" alt="{{ $produit->nom }}">
                    @else
                        <div class="placeholder-image">
                            <i class="fas fa-utensils"></i>
                        </div>
                    @endif
                    <div class="category-badge">{{ $produit->categorie->nom ?? 'Sans catégorie' }}</div>
                </div>

                <div class="product-details">
                    <h3>{{ $produit->nom }}</h3>
                    <p class="description">{{ Str::limit($produit->description, 50) }}</p>
                    <div class="price-row">
                        <span class="price">CFA {{ number_format($produit->prix_vente, 2) }}</span>
                        <span class="status {{ $produit->disponible ? 'active' : 'inactive' }}">
                            {{ $produit->disponible ? 'Disponible' : 'Indisponible' }}
                        </span>
                    </div>
                </div>

                <div class="product-actions">
                    <a href="{{ route('products.edit', $produit->id) }}" class="btn-edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button wire:click="delete({{ $produit->id }})"
                        wire:confirm="Êtes-vous sûr de vouloir supprimer ce produit ?" class="btn-delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>Aucun produit trouvé</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        {{ $produits->links('livewire.custom-pagination') }}
    </div>

    <!-- Product List Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/pages/products/product-list.css') }}">

    @if($showCategoryManager)
        <div class="modal-overlay" wire:click.self="toggleCategoryManager">
            <div class="modal-panel">
                <livewire:products.category-manager />
            </div>
        </div>
    @endif
</div>