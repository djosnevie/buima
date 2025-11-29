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
        <div class="header-actions">
            <div class="left-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input wire:model.live="search" type="text" placeholder="Rechercher une commande...">
                </div>
                <div class="filter-group">
                    <button wire:click="setFilter('all')"
                        class="filter-btn {{ $filterStatus === 'all' ? 'active' : '' }}">
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
                    <button wire:click="setFilter('prete')"
                        class="filter-btn {{ $filterStatus === 'prete' ? 'active' : '' }}">
                        Prêt
                    </button>
                    <button wire:click="setFilter('servie')"
                        class="filter-btn {{ $filterStatus === 'servie' ? 'active' : '' }}">
                        Servie
                    </button>
                    <button wire:click="setFilter('payee')"
                        class="filter-btn {{ $filterStatus === 'payee' ? 'active' : '' }}">
                        Payé
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="orders-grid">
            @forelse($commandes as $commande)
                <div class="order-card status-{{ $commande->statut }}" wire:click="selectOrder({{ $commande->id }})">
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
                                <button wire:click="updateStatus({{ $commande->id }}, 'en_preparation')"
                                    class="btn-action btn-cook" title="Mettre en cuisine">
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
                            <button
                                onclick="printInvoice('{{ route('orders.invoice', $commande->id) }}'); event.stopPropagation();"
                                class="btn-action btn-print">
                                <i class="fas fa-print"></i>
                            </button>
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

            .header-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
            }

            .left-actions {
                display: flex;
                align-items: center;
                gap: 1rem;
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
                padding: 0.75rem 1rem 0.75rem 2.5rem;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                outline: none;
                transition: all 0.3s;
            }

            .search-box input:focus {
                border-color: #ff9f43;
                box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.1);
            }

            .filter-group {
                display: flex;
                background: #f3f4f6;
                padding: 0.25rem;
                border-radius: 10px;
                gap: 0.25rem;
            }

            .filter-btn {
                padding: 0.5rem 1rem;
                border: none;
                background: none;
                border-radius: 8px;
                color: #6b7280;
                font-weight: 600;
                font-size: 0.9rem;
                cursor: pointer;
                transition: all 0.3s;
            }

            .filter-btn.active {
                background: white;
                color: #ff9f43;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
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

            .empty-state {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 200px;
                background: #f9fafb;
                border-radius: 12px;
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

            /* Order Card Cursor */
            .order-card {
                cursor: pointer;
            }

            /* Side Panel Styles */
            .side-panel-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                display: flex;
                justify-content: flex-end;
                backdrop-filter: blur(2px);
            }

            .side-panel {
                width: 500px;
                height: 100%;
                background: white;
                box-shadow: -5px 0 25px rgba(0, 0, 0, 0.15);
                animation: slideIn 0.3s ease-out;
                display: flex;
                flex-direction: column;
            }

            .panel-header {
                padding: 1.5rem;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f9fafb;
            }

            .panel-header h2 {
                margin: 0;
                font-size: 1.5rem;
                color: #1f2937;
            }

            .btn-close-panel {
                background: none;
                border: none;
                font-size: 1.5rem;
                color: #6b7280;
                cursor: pointer;
                transition: color 0.3s;
            }

            .btn-close-panel:hover {
                color: #ef4444;
            }

            .panel-content {
                flex: 1;
                overflow-y: auto;
                padding: 1.5rem;
            }

            .panel-section {
                margin-bottom: 2rem;
            }

            .section-title {
                font-size: 0.9rem;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 1rem;
                border-bottom: 1px solid #f3f4f6;
                padding-bottom: 0.5rem;
            }

            .detail-row {
                display: flex;
                margin-bottom: 0.75rem;
                font-size: 1rem;
                color: #374151;
            }

            .detail-label {
                width: 120px;
                font-weight: 500;
                color: #6b7280;
            }

            .detail-value {
                flex: 1;
                font-weight: 600;
            }

            .order-items-list {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .order-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem;
                background: #f9fafb;
                border-radius: 8px;
            }

            .item-info {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .item-qty {
                background: #ff9f43;
                color: white;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.8rem;
                font-weight: 600;
            }

            .item-name {
                font-weight: 600;
                color: #374151;
            }

            .item-price {
                font-weight: 600;
                color: #1f2937;
            }

            .panel-footer {
                padding: 1.5rem;
                border-top: 1px solid #e5e7eb;
                background: #f9fafb;
            }

            .total-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                font-size: 1.25rem;
                font-weight: 700;
                color: #1f2937;
            }

            .action-buttons {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .btn-panel {
                padding: 0.75rem;
                border-radius: 8px;
                border: none;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                color: white;
            }

            .btn-panel:hover {
                transform: translateY(-2px);
                filter: brightness(1.1);
            }

            .btn-delete-order {
                width: 100%;
                background: #fee2e2;
                color: #ef4444;
                border: 1px solid #fecaca;
            }

            .btn-delete-order:hover {
                background: #ef4444;
                color: white;
            }

            .status-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
                margin-bottom: 1rem;
            }

            .status-option {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 0.75rem;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: white;
                cursor: pointer;
                transition: all 0.2s;
                gap: 0.5rem;
            }

            .status-option:hover {
                border-color: #ff9f43;
                background: #fff7ed;
            }

            .status-option.active {
                border-color: #ff9f43;
                background: #fff7ed;
                color: #ff9f43;
                box-shadow: 0 0 0 2px rgba(255, 159, 67, 0.2);
            }

            .status-icon {
                font-size: 1.25rem;
                margin-bottom: 0.25rem;
            }

            .btn-print {
                background: #6b7280;
                text-decoration: none;
                color: white;
            }

            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                }

                to {
                    transform: translateX(0);
                }
            }
        </style>

        @if($selectedOrder)
            <div class="side-panel-overlay" wire:click.self="closeSideView">
                <div class="side-panel">
                    <div class="panel-header">
                        <h2>Commande #{{ substr($selectedOrder->numero_commande, -4) }}</h2>
                        <button wire:click="closeSideView" class="btn-close-panel">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="panel-content">
                        <div class="panel-section">
                            <div class="section-title">Informations Client</div>
                            <div class="detail-row">
                                <span class="detail-label">Client:</span>
                                <span class="detail-value">{{ $selectedOrder->client_nom ?: 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Type:</span>
                                <span class="detail-value">
                                    @if($selectedOrder->type_commande === 'sur_place')
                                        Sur Place (Table {{ $selectedOrder->table->numero ?? '?' }})
                                    @else
                                        {{ ucfirst($selectedOrder->type_commande) }}
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Statut:</span>
                                <span class="detail-value status-text-{{ $selectedOrder->statut }}">
                                    {{ ucfirst(str_replace('_', ' ', $selectedOrder->statut)) }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value">{{ $selectedOrder->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="panel-section">
                            <div class="section-title">Articles</div>
                            <div class="order-items-list">
                                @foreach($selectedOrder->items as $item)
                                    <div class="order-item">
                                        <div class="item-info">
                                            <span class="item-qty">{{ $item->quantite }}</span>
                                            <span class="item-name">{{ $item->produit->nom }}</span>
                                        </div>
                                        <span class="item-price">{{ number_format($item->prix_unitaire * $item->quantite, 2) }}
                                            €</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($selectedOrder->notes)
                            <div class="panel-section">
                                <div class="section-title">Notes</div>
                                <p class="text-gray-600 italic">{{ $selectedOrder->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="panel-footer">
                        <div class="total-row">
                            <span>Total</span>
                            <span>{{ number_format($selectedOrder->total, 2) }} €</span>
                        </div>

                        <div class="panel-section">
                            <div class="section-title">Changer le statut</div>
                            <div class="status-grid">
                                @php
                                    $statuses = [
                                        'en_attente' => ['label' => 'En attente', 'icon' => 'clock', 'color' => 'text-orange-500 bg-orange-50 border-orange-200'],
                                        'en_preparation' => ['label' => 'En cuisine', 'icon' => 'fire', 'color' => 'text-amber-500 bg-amber-50 border-amber-200'],
                                        'prete' => ['label' => 'Prête', 'icon' => 'bell', 'color' => 'text-emerald-500 bg-emerald-50 border-emerald-200'],
                                        'servie' => ['label' => 'Servie', 'icon' => 'concierge-bell', 'color' => 'text-violet-500 bg-violet-50 border-violet-200'],
                                        'payee' => ['label' => 'Payée', 'icon' => 'check-circle', 'color' => 'text-blue-500 bg-blue-50 border-blue-200'],
                                        'annulee' => ['label' => 'Annulée', 'icon' => 'ban', 'color' => 'text-red-500 bg-red-50 border-red-200'],
                                    ];
                                @endphp

                                @foreach($statuses as $key => $status)
                                    <button wire:click="updateStatus({{ $selectedOrder->id }}, '{{ $key }}')"
                                        class="status-option {{ $selectedOrder->statut === $key ? 'active' : '' }}">
                                        <div class="status-icon">
                                            <i class="fas fa-{{ $status['icon'] }}"></i>
                                        </div>
                                        <span>{{ $status['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button onclick="printInvoice('{{ route('orders.invoice', $selectedOrder->id) }}')"
                                class="btn-panel btn-print">
                                <i class="fas fa-print"></i> Imprimer Facture
                            </button>
                            <button wire:click="deleteOrder({{ $selectedOrder->id }})"
                                wire:confirm="Êtes-vous sûr de vouloir supprimer cette commande ?"
                                class="btn-panel btn-delete-order">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <iframe id="printFrame" style="display:none;"></iframe>
        <script>
            function printInvoice(url) {
                const frame = document.getElementById('printFrame');
                frame.src = url;
            }
        </script>
    </div>