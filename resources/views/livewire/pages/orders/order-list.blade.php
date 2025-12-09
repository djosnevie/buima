@section('title', 'Gestion des Commandes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Commandes</li>
@endsection

<div class="orders-management">
    <style>
        /* Dynamic Theme Overrides for Orders List */
        .filter-btn.active {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        .filter-btn:hover:not(.active) {
            color: var(--primary-color) !important;
            background: rgba(255, 159, 67, 0.05);
            /* Fallback tint */
        }

        .order-card:hover {
            border-left-color: var(--primary-color) !important;
        }

        .hash {
            color: var(--secondary-color) !important;
        }

        .status-option.active {
            border-color: var(--primary-color) !important;
            background-color: rgba(var(--primary-color-rgb), 0.05) !important;
            /* Requires RGB var if available, else standard tint */
        }

        .status-option.active .status-icon {
            color: var(--primary-color) !important;
        }

        .btn-print:hover {
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
        }

        .panel-title .hash {
            color: var(--secondary-color) !important;
        }
    </style>
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
                    <div class="total">{{ number_format($commande->total, 0, ',', ' ') }}
                        {{ auth()->user()->etablissement->devise ?? 'XAF' }}
                    </div>
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

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $commandes->links('livewire.custom-pagination') }}
    </div>

    <!-- Side Panel for Details -->
    @if($selectedOrder)
        <div class="side-panel-overlay" wire:click="closeSideView">
            <div class="side-panel" wire:click.stop>
                <div class="panel-header">
                    <div class="panel-title">
                        <span class="hash">#</span>{{ substr($selectedOrder->numero_commande, -4) }}
                    </div>
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
                                    <span
                                        class="item-price">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }}
                                        {{ auth()->user()->etablissement->devise ?? 'XAF' }}</span>
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
                        <span>{{ number_format($selectedOrder->total, 0, ',', ' ') }}
                            {{ auth()->user()->etablissement->devise ?? 'XAF' }}</span>
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
    <!-- Order List Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/pages/orders/order-list.css') }}">
</div>