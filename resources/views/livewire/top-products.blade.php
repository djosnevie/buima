<div class="top-products-container">
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
                            <i class="fas fa-euro-sign"></i>
                            {{ number_format($item->total_revenue, 2) }}
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

    <style>
        .top-products-container {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .products-header {
            margin-bottom: 1.5rem;
        }

        .products-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .products-title i {
            color: #ef4444;
        }

        .products-subtitle {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 500;
        }

        .products-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            flex: 1;
            overflow-y: auto;
        }

        .product-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, #fff7ed, #fffbf5);
            border-radius: 12px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .product-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .product-item:nth-child(1) {
            border-left-color: #fbbf24;
            background: linear-gradient(135deg, #fef3c7, #fffbeb);
        }

        .product-item:nth-child(2) {
            border-left-color: #d1d5db;
            background: linear-gradient(135deg, #f3f4f6, #fafafa);
        }

        .product-item:nth-child(3) {
            border-left-color: #fb923c;
            background: linear-gradient(135deg, #fed7aa, #fff7ed);
        }

        .product-item:nth-child(4),
        .product-item:nth-child(5) {
            border-left-color: #ff9f43;
        }

        .product-rank {
            flex-shrink: 0;
        }

        .rank-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            background: linear-gradient(135deg, #ff9f43, #ee5253);
        }

        .rank-badge.rank-1 {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
        }

        .rank-badge.rank-2 {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
            box-shadow: 0 4px 12px rgba(156, 163, 175, 0.4);
        }

        .rank-badge.rank-3 {
            background: linear-gradient(135deg, #fb923c, #f97316);
            box-shadow: 0 4px 12px rgba(251, 146, 60, 0.4);
        }

        .product-info {
            flex: 1;
            min-width: 0;
        }

        .product-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
        }

        .stat-quantity,
        .stat-revenue {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-quantity i {
            color: #ff9f43;
        }

        .stat-revenue i {
            color: #10b981;
        }

        .product-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            background: rgba(255, 159, 67, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff9f43;
            font-size: 1.1rem;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            color: #9ca3af;
            text-align: center;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Scrollbar styling */
        .products-list::-webkit-scrollbar {
            width: 6px;
        }

        .products-list::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 10px;
        }

        .products-list::-webkit-scrollbar-thumb {
            background: #ff9f43;
            border-radius: 10px;
        }

        .products-list::-webkit-scrollbar-thumb:hover {
            background: #ee5253;
        }
    </style>
</div>