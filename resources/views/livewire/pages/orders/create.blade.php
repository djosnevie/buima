@section('title', 'Nouvelle Commande')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Commandes</a></li>
    <li class="breadcrumb-item active">Nouvelle</li>
@endsection

<div class="create-order-page">
    <style>
        /* Dynamic Theme Overrides */
        .btn-category.active {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .product-card:hover {
            border-color: var(--primary-color) !important;
        }

        .product-price {
            color: var(--primary-color) !important;
        }

        .btn-order-type:hover {
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
            background: rgba(255, 159, 67, 0.05); /* Light tint fallback */
        }

        .btn-check:checked+.btn-order-type {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .btn-check:checked+.btn-order-type:hover {
            opacity: 0.9;
        }

        .input-group.shadow-sm:focus-within {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.15) !important; /* Note: requires RGB var or approx */
        }

        .input-group .input-group-text i {
            color: var(--primary-color) !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
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
    <div class="row g-4">
        <!-- Left Column: Menu & Selection -->
        <div class="col-lg-8">
            <!-- Order Type & Table Selection -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Type de commande</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="orderType" id="sur_place" value="sur_place"
                                    wire:model.live="orderType" autocomplete="off">
                                <label class="btn btn-order-type" for="sur_place">
                                    <i class="fas fa-utensils me-2"></i>Sur place
                                </label>

                                <input type="radio" class="btn-check" name="orderType" id="emporter" value="emporter"
                                    wire:model.live="orderType" autocomplete="off">
                                <label class="btn btn-order-type" for="emporter">
                                    <i class="fas fa-shopping-bag me-2"></i>Emporter
                                </label>

                                <input type="radio" class="btn-check" name="orderType" id="livraison" value="livraison"
                                    wire:model.live="orderType" autocomplete="off">
                                <label class="btn btn-order-type" for="livraison">
                                    <i class="fas fa-motorcycle me-2"></i>Livraison
                                </label>
                            </div>
                        </div>

                        @if($orderType === 'sur_place')
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Table</label>
                                <select class="form-select @error('selectedTable') is-invalid @enderror"
                                    wire:model.live="selectedTable">
                                    <option value="">Sélectionner une table</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}">
                                            Table {{ $table->numero }} ({{ $table->capacite }} pers.) - {{ ucfirst($table->zone) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedTable') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Search Filter -->
            <div class="mb-4">
                <div class="input-group shadow-sm rounded overflow-hidden">
                    <span class="input-group-text bg-white border-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-0 py-2" placeholder="Rechercher un produit (ex: Burger, Pizza...)" wire:model.live.debounce.300ms="search">
                    @if($search)
                        <button class="btn btn-white border-0 text-muted" wire:click="$set('search', '')"><i class="fas fa-times"></i></button>
                    @endif
                </div>
            </div>

            <!-- Categories -->
            <div class="categories-wrapper mb-4">
                <div class="d-flex gap-2 overflow-auto pb-2">
                    <button wire:click="selectCategory(null)"
                        class="btn btn-category {{ is_null($selectedCategory) ? 'active' : '' }}">
                        Tous
                    </button>
                    @foreach($categories as $category)
                        <button wire:click="selectCategory({{ $category->id }})"
                            class="btn btn-category {{ $selectedCategory == $category->id ? 'active' : '' }}">
                            {{ $category->nom }}
                        </button>
                    @endforeach
                </div>
            </div>
            


            <!-- Products Grid -->
            <div class="products-grid">
                @forelse($produits as $produit)
                    <div class="product-card" wire:click="addToCart({{ $produit->id }})">
                        <div class="product-image">
                            @if($produit->image)
                                <img src="{{ asset('images/' . $produit->image) }}" alt="{{ $produit->nom }}"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'placeholder-image\'><i class=\'fas fa-utensils\'></i></div>'">
                            @else
                                <div class="placeholder-image">
                                    <i class="fas fa-utensils"></i>
                                </div>
                            @endif
                        </div>
                        <div class="product-info">
                            <h6 class="product-title">{{ $produit->nom }}</h6>
                            <div class="product-price">{{ number_format($produit->prix_vente, 0, ',', ' ') }} {{ auth()->user()->etablissement->devise ?? 'XAF' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p>Aucun produit dans cette catégorie</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Cart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2"></i>Panier</h5>
                </div>
                <div class="card-body d-flex flex-column p-0">
                    <!-- Client Info (if needed) -->
                    @if($orderType !== 'sur_place')
                        <div class="p-3 bg-light border-bottom">
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm @error('clientName') is-invalid @enderror"
                                    placeholder="Nom du client" wire:model="clientName">
                                @error('clientName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm @error('clientPhone') is-invalid @enderror"
                                    placeholder="Téléphone" wire:model="clientPhone">
                                @error('clientPhone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @if($orderType === 'livraison')
                                <div>
                                    <textarea class="form-control form-control-sm" placeholder="Adresse de livraison"
                                        wire:model="clientAddress" rows="2"></textarea>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Cart Items -->
                    <div class="cart-items flex-grow-1 overflow-auto p-3">
                        @forelse($cart as $item)
                            <div class="cart-item d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="quantity-controls btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary"
                                            wire:click="decrementQuantity({{ $item['produit_id'] }})">-</button>
                                        <span class="btn btn-outline-secondary disabled fw-bold text-dark"
                                            style="width: 30px;">{{ $item['quantite'] }}</span>
                                        <button class="btn btn-outline-secondary"
                                            wire:click="incrementQuantity({{ $item['produit_id'] }})">+</button>
                                    </div>
                                    <div class="item-details ms-2">
                                        <div class="fw-bold">{{ $item['nom'] }}</div>
                                        <div class="text-muted small">{{ number_format($item['prix_unitaire'], 0, ',', ' ') }} {{ auth()->user()->etablissement->devise ?? 'XAF' }}</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">
                                        {{ number_format($item['prix_unitaire'] * $item['quantite'], 0, ',', ' ') }} {{ auth()->user()->etablissement->devise ?? 'XAF' }}
                                    </div>
                                    <button class="btn btn-link text-danger p-0 small text-decoration-none"
                                        wire:click="removeFromCart({{ $item['produit_id'] }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-shopping-basket fa-2x mb-3 opacity-50"></i>
                                <p class="mb-0">Votre panier est vide</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Totals & Actions -->
                    <div class="p-3 bg-light border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Sous-total</span>
                            <span class="fw-bold">{{ number_format($this->subtotal, 0, ',', ' ') }} {{ auth()->user()->etablissement->devise ?? 'XAF' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">TVA (10%)</span>
                            <span>{{ number_format($this->taxes, 0, ',', ' ') }} {{ auth()->user()->etablissement->devise ?? 'XAF' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pt-2 border-top">
                            <span class="h5 mb-0">Total</span>
                            <span class="h5 mb-0 text-primary">{{ number_format($this->total, 0, ',', ' ') }} {{ auth()->user()->etablissement->devise ?? 'XAF' }}</span>
                        </div>

                        <div class="mb-3">
                            <textarea class="form-control" placeholder="Notes (allergies, cuisson...)" wire:model="notes"
                                rows="2"></textarea>
                        </div>

                        <button class="btn btn-primary w-100 py-2 fw-bold" wire:click="createOrder"
                            @if(empty($cart)) disabled @endif>
                            <i class="fas fa-check-circle me-2"></i>Valider la commande
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Order Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/pages/orders/create.css') }}">

</div>
