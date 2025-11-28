@section('title', 'Gestion des Commandes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Commandes</li>
@endsection

<div class="orders-management">
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
    <!-- Filters & Search -->
    <div class="filters-bar">
        <div class="status-filters">
            <button wire:click="setFilter('all')" class="filter-btn {{ $filterStatus === 'all' ? 'active' : '' }}">
                Tout
            </button>
            <button wire:click="setFilter('en_attente')"
                class="filter-btn {{ $filterStatus === 'en_attente' ? 'active' : '' }}">
                En attente
            </button>
            <button wire:click="setFilter('en_preparation')"
                class="filter-btn {{ $filterStatus === 'en_preparation' ? 'active' : '' }}">
                En cuisine
            </button>
            <button wire:click="setFilter('prete')" class="filter-btn {{ $filterStatus === 'prete' ? 'active' : '' }}">
                Prêt
            </button>
            <button wire:click="setFilter('servie')"
                class="filter-btn {{ $filterStatus === 'servie' ? 'active' : '' }}">
                Servie
            </button>
            <button wire:click="setFilter('payee')" class="filter-btn {{ $filterStatus === 'payee' ? 'active' : '' }}">
                Payé
            </button>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input wire:model.live="search" type="text" placeholder="Rechercher une commande...">
        </div>
    </div>

    <!-- Orders List -->
    <div class="orders-grid">
        @forelse($commandes as $commande)
            <div class="order-card status-{{ $commande->statut }}">
                <div class="order-header">
                    <div class="order-id">
                        <span class="hash">#</span>{{ substr($commande->numero_commande, -4) }}
                    </div>
                    <div class="header-right">
                        <span class="status-badge-header status-{{ $commande->statut }}">
                            @if($commande->statut === 'en_attente')
                                En attente
                            @elseif($commande->statut === 'en_preparation')
                                En cuisine
                            @elseif($commande->statut === 'prete')
                                Prête
                            @elseif($commande->statut === 'servie')
                                Servie
                            @elseif($commande->statut === 'payee')
                                Payée
                            @endif
                        </span>
                        <div class="order-time">
                            {{ $commande->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>

                <div class="order-info">
                    <div class="info-row">
                        <i
                            class="fas fa-{{ $commande->type_commande === 'sur_place' ? 'utensils' : ($commande->type_commande === 'livraison' ? 'motorcycle' : 'shopping-bag') }}"></i>
                        <span>
                            @if($commande->type_commande === 'sur_place')
                                Table {{ $commande->table->numero ?? '?' }}
                            @else
                                {{ ucfirst($commande->type_commande) }}
                            @endif
                        </span>
                    </div>
                    @if($commande->client_nom)
                        <div class="info-row">
                            <i class="fas fa-user"></i>
                            <span>{{ $commande->client_nom }}</span>
                        </div>
                    @endif
                </div>

                <div class="order-items">
                    @foreach($commande->items->take(3) as $item)
                        <div class="item-line">
                            <span class="qty">{{ $item->quantite }}x</span>
                            <span class="name">{{ $item->produit->nom }}</span>
                        </div>
                    @endforeach
                    @if($commande->items->count() > 3)
                        <div class="more-items">+ {{ $commande->items->count() - 3 }} autres...</div>
                    @endif
                </div>

                <div class="order-footer">
                    <div class="total">€{{ number_format($commande->total, 2) }}</div>
                    <div class="actions">
                        @if($commande->statut === 'en_attente')
                            <button wire:click="updateStatus({{ $commande->id }}, 'en_preparation')" class="btn-action btn-cook"
                                title="Mettre en cuisine">
                                <i class="fas fa-fire"></i>
                            </button>
                        @elseif($commande->statut === 'en_preparation')
                            <button wire:click="updateStatus({{ $commande->id }}, 'prete')" class="btn-action btn-ready"
                                title="Marquer comme prête">
                                <i class="fas fa-check"></i>
                            </button>
                        @elseif($commande->statut === 'prete')
                            <button wire:click="updateStatus({{ $commande->id }}, 'servie')" class="btn-action btn-serve"
                                title="Marquer comme servie">
                                <i class="fas fa-concierge-bell"></i>
                            </button>
                        @elseif($commande->statut === 'servie')
                            <button wire:click="updateStatus({{ $commande->id }}, 'payee')" class="btn-action btn-pay"
                                title="Marquer comme payée">
                                <i class="fas fa-euro-sign"></i>
                            </button>
                        @endif
                        <a href="{{ route('orders.invoice', $commande->id) }}" target="_blank" class="btn-action btn-print">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <p>Aucune commande trouvée</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        {{ $commandes->links() }}
    </div>
    <style>
        .orders-management {
            padding: 0;
        }

        .filters-bar {
            background: white;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .status-filters {
            display: flex;
            gap: 0.5rem;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .filter-btn.active {
            background: #ff9f43;
            color: white;
            border-color: #ff9f43;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .search-box input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
        }

        .search-box input:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.1);
        }

        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .order-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .order-card.status-en_attente {
            border-left-color: #ff9f43;
        }

        .order-card.status-en_preparation {
            border-left-color: #f59e0b;
        }

        .order-card.status-prete {
            border-left-color: #10b981;
        }

        .order-card.status-servie {
            border-left-color: #8b5cf6;
        }

        .order-card.status-payee {
            border-left-color: #6b7280;
            opacity: 0.8;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .order-id {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .order-id .hash {
            color: #ff9f43;
        }

        .order-time {
            color: #6b7280;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .status-badge-header {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge-header.status-en_attente {
            background: #fff7ed;
            color: #ff9f43;
            border: 1px solid #ff9f43;
        }

        .status-badge-header.status-en_preparation {
            background: #fef3c7;
            color: #f59e0b;
            border: 1px solid #f59e0b;
        }

        .status-badge-header.status-prete {
            background: #dcfce7;
            color: #10b981;
            border: 1px solid #10b981;
        }

        .status-badge-header.status-servie {
            background: #ede9fe;
            color: #8b5cf6;
            border: 1px solid #8b5cf6;
        }

        .status-badge-header.status-payee {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #9ca3af;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4b5563;
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
        }

        .info-row i {
            width: 20px;
            text-align: center;
            color: #9ca3af;
        }

        .order-items {
            margin: 1rem 0;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 8px;
        }

        .item-line {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        .item-line .qty {
            font-weight: 600;
            color: #ff9f43;
        }

        .more-items {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 0.5rem;
            font-style: italic;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .total {
            font-weight: 700;
            font-size: 1.2rem;
            color: #1f2937;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: white;
        }

        .btn-cook {
            background: #f59e0b;
        }

        .btn-ready {
            background: #10b981;
        }

        .btn-serve {
            background: #8b5cf6;
        }

        .btn-pay {
            background: #3b82f6;
        }

        .btn-print {
            background: #6b7280;
            text-decoration: none;
        }

        .btn-action:hover {
            transform: scale(1.1);
            filter: brightness(1.1);
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        :deep(.pagination) {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 0;
            align-items: center;
        }

        :deep(.page-item) {
            display: inline-block;
        }

        :deep(.page-link) {
            min-width: 40px;
            height: 40px;
            padding: 0.5rem 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.3s ease;
            background: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        :deep(.page-link:hover) {
            background: #fff7ed;
            border-color: #ff9f43;
            color: #ff9f43;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 159, 67, 0.2);
        }

        :deep(.page-item.active .page-link) {
            background: linear-gradient(135deg, #ff9f43, #ee5253);
            border-color: #ff9f43;
            color: white;
            box-shadow: 0 4px 12px rgba(255, 159, 67, 0.4);
        }

        :deep(.page-item.disabled .page-link) {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f9fafb;
        }

        :deep(.page-item.disabled .page-link:hover) {
            transform: none;
            box-shadow: none;
            border-color: #e5e7eb;
        }
    </style>
</div>