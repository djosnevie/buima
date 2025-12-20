@section('title', 'Point de Vente')

<div class="pos-wrapper" style="height: calc(100vh - 150px);">
    <style>
        .pos-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 1rem;
            height: 100%;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .product-item {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            background: white;
        }

        .product-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .product-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
        }

        .cart-panel {
            background: white;
            border-radius: 12px;
            display: flex;
            flex-column: column;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .category-pill {
            cursor: pointer;
            white-space: nowrap;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            background: #f8f9fa;
            border: 1px solid #eee;
        }

        .category-pill.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .cart-items {
            flex-grow: 1;
            overflow-y: auto;
        }

        .item-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.5rem;
            padding: 0.8rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            border: 1px solid #ddd;
            background: white;
        }
    </style>

    <div class="pos-container">
        <!-- Main Area -->
        <div class="d-flex flex-column h-100">
            <!-- Search & Categories -->
            <div class="mb-3">
                <div class="input-group mb-3 shadow-sm border-0">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" wire:model.live="search" class="form-control border-0"
                        placeholder="Rechercher un produit...">
                </div>

                <div class="d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: none;">
                    <div wire:click="$set('selectedCategory', null)"
                        class="category-pill {{ is_null($selectedCategory) ? 'active' : '' }}">Tous</div>
                    @foreach($categories as $cat)
                        <div wire:click="$set('selectedCategory', {{ $cat->id }})"
                            class="category-pill {{ $selectedCategory == $cat->id ? 'active' : '' }}">
                            {{ $cat->nom }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Products -->
            <div class="product-grid">
                @foreach($produits as $p)
                    <div class="product-item" wire:click="addToCart({{ $p->id }})">
                        <img src="{{ $p->image_url }}"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($p->nom) }}&background=f8f9fa&color=bf3a29';">
                        <div class="p-2">
                            <div class="fw-bold small text-truncate">{{ $p->nom }}</div>
                            <div class="text-primary fw-bold">{{ number_format($p->prix_vente, 0, ',', ' ') }}
                                <small>{{ auth()->user()->etablissement->devise_display }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                {{ $produits->links() }}
            </div>
        </div>

        <!-- Cart Area -->
        <div class="cart-panel d-flex flex-column p-0 h-100">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-4">
                <h6 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2"></i>Commande Actuelle</h6>
                @if($activeSession)
                    <span class="badge bg-success small"><i class="fas fa-cash-register me-1"></i>
                        {{ $activeSession->caisse->nom }}</span>
                @endif
            </div>

            <div class="cart-items p-2">
                @forelse($cart as $id => $item)
                    <div class="item-row">
                        <div>
                            <div class="fw-bold small">{{ $item['nom'] }}</div>
                            <div class="text-muted small">{{ number_format($item['prix'], 0, ',', ' ') }} x
                                {{ $item['quantite'] }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ number_format($item['prix'] * $item['quantite'], 0, ',', ' ') }}</div>
                            <div class="d-flex gap-1 justify-content-end mt-1">
                                <button wire:click="updateQuantity({{ $id }}, -1)" class="qty-btn"><i
                                        class="fas fa-minus small"></i></button>
                                <button wire:click="updateQuantity({{ $id }}, 1)" class="qty-btn"><i
                                        class="fas fa-plus small"></i></button>
                                <button wire:click="removeFromCart({{ $id }})" class="qty-btn text-danger border-danger"><i
                                        class="fas fa-trash small"></i></button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-shopping-basket fa-3x mb-3 opacity-25"></i>
                        <p>Panier vide</p>
                    </div>
                @endforelse
            </div>

            <div class="p-3 bg-light border-top mt-auto rounded-bottom-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Type</span>
                    <select wire:model="orderType" class="form-select form-select-sm w-auto">
                        <option value="emporter">À emporter</option>
                        <option value="sur_place">Sur place</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Sous-total</span>
                    <span class="small">{{ number_format($this->subtotal, 0, ',', ' ') }}</span>
                </div>
                @if(auth()->user()->etablissement->tva_applicable)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">TVA ({{ (float) auth()->user()->etablissement->tva_taux }}%)</span>
                        <span
                            class="small">{{ number_format($this->subtotal * (auth()->user()->etablissement->tva_taux / 100), 0, ',', ' ') }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-3 border-top pt-2">
                    <span class="h5 fw-bold mb-0">TOTAL</span>
                    <span class="h4 fw-bold mb-0 text-primary">
                        @php
                            $tva = auth()->user()->etablissement->tva_applicable ? ($this->subtotal *
                                (auth()->user()->etablissement->tva_taux / 100)) : 0;
                        @endphp
                        {{ number_format($this->subtotal + $tva, 0, ',', ' ') }}
                        {{ auth()->user()->etablissement->devise_display }}
                    </span>
                </div>

                <div class="mb-3">
                    <label class="small text-muted mb-1">Méthode de paiement</label>
                    <div class="d-flex gap-2">
                        <button wire:click="$set('paymentMethod', 'especes')"
                            class="btn btn-sm flex-grow-1 {{ $paymentMethod === 'especes' ? 'btn-primary' : 'btn-outline-primary' }}">Cash</button>
                        <button wire:click="$set('paymentMethod', 'carte')"
                            class="btn btn-sm flex-grow-1 {{ $paymentMethod === 'carte' ? 'btn-primary' : 'btn-outline-primary' }}">Mode</button>
                        <button wire:click="$set('paymentMethod', 'mobile_money')"
                            class="btn btn-sm flex-grow-1 {{ $paymentMethod === 'mobile_money' ? 'btn-primary' : 'btn-outline-primary' }}">MOMO</button>
                    </div>
                </div>

                <button wire:click="processOrder" class="btn btn-primary w-100 py-2 fw-bold" @if(empty($cart)) disabled
                @endif>
                    <i class="fas fa-check-circle me-2"></i> VALIDER & PAYER
                </button>
            </div>
        </div>
    </div>

    <!-- Session Modal -->
    @if($showSessionModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.8); z-index: 2000;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Ouverture de Caisse</h5>
                        <a href="{{ route('dashboard') }}" class="btn-close"></a>
                    </div>
                    <form wire:submit.prevent="openSession">
                        <div class="modal-body p-4">
                            <p class="text-muted">Veuillez choisir une caisse et indiquer le montant initial pour commencer.
                            </p>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Caisse</label>
                                <select wire:model="caisseId" class="form-select">
                                    <option value="">Choisir une caisse...</option>
                                    @foreach($availableCaisses as $caisse)
                                        <option value="{{ $caisse->id }}">{{ $caisse->nom }} ({{ $caisse->code }})</option>
                                    @endforeach
                                </select>
                                @error('caisseId') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Montant d'ouverture (Fonds de caisse)</label>
                                <div class="input-group">
                                    <input type="number" wire:model="montant_ouverture" class="form-control"
                                        placeholder="0.00">
                                    <span
                                        class="input-group-text">{{ auth()->user()->etablissement->devise_display }}</span>
                                </div>
                                @error('montant_ouverture') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <a href="{{ route('dashboard') }}" class="btn btn-light">Retour au Dashboard</a>
                            <button type="submit" class="btn btn-primary" style="background: var(--primary-color)">Ouvrir la
                                session</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>