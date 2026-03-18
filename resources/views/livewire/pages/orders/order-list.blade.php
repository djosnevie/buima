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
                            @elseif($commande->statut === 'servie')
                                Servie
                            @elseif($commande->statut === 'payee')
                                Payée
                            @elseif($commande->statut === 'annulee')
                                Annulée
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
                        {{ auth()->user()->etablissement->devise_display ?? 'FCFA' }}
                    </div>
                    <div class="actions">
                        @if($commande->statut === 'en_attente')
                            <button wire:click.stop="updateStatus({{ $commande->id }}, 'servie')" class="btn-action btn-serve"
                                title="Marquer comme servie">
                                <i class="fas fa-concierge-bell"></i>
                            </button>
                        @elseif($commande->statut === 'servie')
                            <button wire:click.stop="updateStatus({{ $commande->id }}, 'payee')" class="btn-action btn-pay"
                                title="Encaisser">
                                <i class="fas fa-euro-sign"></i>
                            </button>
                        @endif
                        @if(in_array($commande->statut, ['servie', 'payee']))
                            <button
                                onclick="printInvoice('{{ route('orders.invoice', $commande->id) }}'); event.stopPropagation();"
                                class="btn-action btn-print" title="Imprimer la facture">
                                <i class="fas fa-print"></i>
                            </button>
                        @endif
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
                        @if($isEditing)
                            <span class="badge bg-warning text-dark ms-2">Édition</span>
                        @endif
                    </div>
                    <div class="header-actions-panel">
                        @if(!$isEditing && !in_array($selectedOrder->statut, ['payee', 'annulee']))
                            <a href="{{ route('orders.edit', $selectedOrder->id) }}" class="btn-icon" title="Ouvrir dans le POS pour modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif
                        <button wire:click="closeSideView" class="btn-close-panel">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="panel-content">
                    @if($isEditing)
                        <!-- Edit Form -->
                        <div class="edit-form space-y-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client</label>
                                        <input type="text" wire:model="editForm.client_nom" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact</label>
                                        <input type="text" wire:model="editForm.client_telephone" class="form-control" placeholder="Tél...">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Type</label>
                                <select wire:model.live="editForm.type_commande" class="form-control">
                                    <option value="sur_place">Sur Place</option>
                                    <option value="emporter">À Emporter</option>
                                    <option value="livraison">Livraison</option>
                                </select>
                            </div>

                            @if($editForm['type_commande'] === 'sur_place')
                                <div class="form-group">
                                    <label>Table</label>
                                    <select wire:model="editForm.table_id" class="form-control">
                                        <option value="">Choisir...</option>
                                        @foreach($tables as $table)
                                            <option value="{{ $table->id }}">Table {{ $table->numero }} ({{ $table->places }} pl.)</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group">
                                <label>Notes</label>
                                <textarea wire:model="editForm.notes" class="form-control" rows="2"></textarea>
                            </div>

                            <!-- Add Product Section -->
                             <div class="form-group relative">
                                <label>Ajouter un produit</label>
                                <div class="search-container relative">
                                    <input type="text" 
                                           wire:model.live="productSearch" 
                                           class="form-control" 
                                           placeholder="Rechercher pour ajouter...">
                                    @if(!empty($searchResults))
                                        <div class="search-results absolute w-full bg-white shadow-lg rounded-lg mt-1 border border-gray-200 z-50">
                                            @foreach($searchResults as $result)
                                                <div wire:click="addProductToOrder({{ $result->id }})" 
                                                     class="p-2 hover:bg-orange-50 cursor-pointer flex justify-between items-center border-b last:border-0 transition-colors">
                                                    <div class="flex flex-col">
                                                        <span class="font-medium">{{ $result->nom }}</span>
                                                        <span class="text-xs text-gray-500">{{ $result->categorie->nom ?? 'Sans catégorie' }}</span>
                                                    </div>
                                                    <span class="font-bold text-orange-500">{{ number_format($result->prix_vente, 0, ',', ' ') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="panel-section mt-4">
                            <div class="section-title">Articles (Édition)</div>
                            <div class="order-items-list">
                                @foreach($selectedOrder->items as $item)
                                    <div class="order-item editing">
                                        <div class="item-image-container me-3" style="width: 40px; height: 40px; overflow: hidden; border-radius: 8px;">
                                            @if($item->produit->image)
                                                <img src="{{ asset('images/' . $item->produit->image) }}" 
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($item->produit->nom) }}&background=random';"
                                                     class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">
                                                    <i class="fas fa-utensils"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="item-info flex-grow-1">
                                            <span class="item-name">{{ $item->produit->nom }}</span>
                                        </div>
                                        <div class="qty-controls">
                                            <button wire:click="updateItemQuantity({{ $item->id }}, {{ $item->quantite - 1 }})" class="btn-qty">-</button>
                                            <span class="qty-display">{{ $item->quantite }}</span>
                                            <button wire:click="updateItemQuantity({{ $item->id }}, {{ $item->quantite + 1 }})" class="btn-qty">+</button>
                                            <button wire:click="removeItem({{ $item->id }})" class="btn-remove-item text-red-500 ms-2"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="panel-actions mt-4 flex justify-end gap-2">
                            <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">Annuler</button>
                            <button wire:click="saveOrder" class="btn btn-primary btn-sm">Enregistrer</button>
                        </div>

                    @else
                        <!-- Read-only Mode -->
                        <div class="panel-section">
                            <div class="section-title">Informations Client</div>
                            <div class="detail-row">
                                <span class="detail-label">Client:</span>
                                <span class="detail-value">{{ $selectedOrder->client_nom ?: 'N/A' }}</span>
                            </div>
                            @if($selectedOrder->client_telephone)
                                <div class="detail-row">
                                    <span class="detail-label">Contact:</span>
                                    <span class="detail-value">{{ $selectedOrder->client_telephone }}</span>
                                </div>
                            @endif
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
                                        <div class="item-image-container me-3" style="width: 40px; height: 40px; overflow: hidden; border-radius: 8px;">
                                            @if($item->produit->image)
                                                <img src="{{ asset('images/' . $item->produit->image) }}" 
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($item->produit->nom) }}&background=random';"
                                                     class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">
                                                    <i class="fas fa-utensils"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="item-info">
                                            <span class="item-qty">{{ $item->quantite }}</span>
                                            <span class="item-name">{{ $item->produit->nom }}</span>
                                        </div>
                                        <span class="item-price">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }} {{ auth()->user()->etablissement->devise_display ?? 'FCFA' }}</span>
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
                    @endif
                </div>

                @if(!$isEditing)
                    <div class="panel-footer">
                        <div class="total-row">
                            <span>Total</span>
                            <span>{{ number_format($selectedOrder->total, 0, ',', ' ') }} {{ auth()->user()->etablissement->devise_display ?? 'FCFA' }}</span>
                        </div>

                        <div class="panel-section">
                            <div class="section-title">Changer le statut</div>
                            
                            @if($selectedOrder->statut === 'payee')
                                <div class="alert alert-info py-2 text-sm">
                                    <i class="fas fa-lock me-2"></i> Commande verrouillée (Payée)
                                </div>
                            @endif

                            <div class="status-grid">
                                @php
                                    $statuses = [
                                        'en_attente' => ['label' => 'En attente', 'icon' => 'clock', 'color' => 'text-orange-500 bg-orange-50 border-orange-200'],
                                        'servie' => ['label' => 'Servie', 'icon' => 'concierge-bell', 'color' => 'text-violet-500 bg-violet-50 border-violet-200'],
                                        'payee' => ['label' => 'Payée', 'icon' => 'check-circle', 'color' => 'text-blue-500 bg-blue-50 border-blue-200'],
                                        'annulee' => ['label' => 'Annulée', 'icon' => 'ban', 'color' => 'text-red-500 bg-red-50 border-red-200'],
                                    ];
                                @endphp

                                @foreach($statuses as $key => $status)
                                    @php
                                        $isDisabled = false;
                                        // Lock logic: if payee, everything disabled except annulee (for admins)
                                        if ($selectedOrder->statut === 'payee') {
                                            if ($key === 'annulee') {
                                                if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                                                    $isDisabled = true;
                                                }
                                            } else {
                                                $isDisabled = true;
                                            }
                                        } elseif ($selectedOrder->statut === 'annulee') {
                                            // Lock logic: if annulee, disable all other buttons
                                            if ($key !== 'annulee') {
                                                $isDisabled = true;
                                            }
                                        }
                                    @endphp
                                    
                                    <button wire:click="updateStatus({{ $selectedOrder->id }}, '{{ $key }}')"
                                        {{ $isDisabled ? 'disabled' : '' }}
                                        class="status-option {{ $selectedOrder->statut === $key ? 'active' : '' }} {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        <div class="status-icon">
                                            <i class="fas fa-{{ $status['icon'] }}"></i>
                                        </div>
                                        <span>{{ $status['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="action-buttons">
                            @if(in_array($selectedOrder->statut, ['servie', 'payee']))
                                <button onclick="printInvoice('{{ route('orders.invoice', $selectedOrder->id) }}')" class="btn-panel btn-print">
                                    <i class="fas fa-print"></i> Imprimer Facture
                                </button>
                            @endif
                            @if($selectedOrder->statut !== 'payee' || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <button wire:click="deleteOrder({{ $selectedOrder->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer cette commande ?" class="btn-panel btn-delete-order">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
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
    <!-- Manager Validation Modal -->
    @if($showManagerPinModal)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-lock me-2"></i>Validation Requise</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cancelManagerApproval"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="mb-4">L'annulation d'une commande nécessite l'approbation d'un manager. Veuillez saisir un mot de passe autorisé.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mot de passe Manager</label>
                            <input type="password" class="form-control form-control-lg @error('managerPassword') is-invalid @enderror" wire:model="managerPassword" placeholder="Saisissez le mot de passe...">
                            @error('managerPassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="cancelManagerApproval">Annuler</button>
                        <button type="button" class="btn btn-danger px-4" wire:click="validateManagerApproval">Autoriser l'annulation</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Order List Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/pages/orders/order-list.css') }}">
</div>