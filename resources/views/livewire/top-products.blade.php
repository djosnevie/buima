<div class="top-products-container">
    <style>
        .top-products-container .products-title i {
            color: var(--primary-color) !important;
        }

        .rank-badge.rank-1 {
            background: var(--primary-color) !important;
            color: white !important;
        }

        .product-icon {
            color: var(--primary-color) !important;
            background: rgba(var(--primary-color-rgb), 0.1) !important;
        }
    </style>
    <div class="products-header">
        <div class="products-title">
            <i class="fas fa-fire me-2"></i>
            <span>Top Produits</span>
        </div>
        <div class="products-subtitle">
            Les plus vendus
        </div>
    </div>

    <div class="products-list">
        @forelse($topProducts as $index => $item)
            <div class="product-item">
                <div class="product-rank">
                    <div class="rank-badge rank-{{ $index + 1 }}">
                        {{ $index + 1 }}
                    </div>
                </div>
                <div class="product-info">
                    <div class="product-name">{{ $item->produit->nom }}</div>
                    <div class="product-stats">
                        <span class="stat-quantity">
                            <i class="fas fa-shopping-cart"></i>
                            {{ $item->total_quantity }} vendus
                        </span>
                        <span class="stat-revenue">
                            <i class="fas fa-money-bill-wave"></i>
                            {{ number_format($item->total_revenue, 2, ',', ' ') }}
                            {{ auth()->user()->etablissement->devise ?? 'XAF' }}
                        </span>
                    </div>
                </div>
                <div class="product-icon">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>Aucune vente enregistrée</p>
            </div>
        @endforelse
    </div>

    <!-- Top Products Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/top-products.css') }}">
</div>