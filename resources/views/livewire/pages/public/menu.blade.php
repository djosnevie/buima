<div class="public-menu bg-light min-vh-100">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky-top">
        <div class="container py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    @if($etablissement->logo)
                        <img src="{{ asset('storage/' . $etablissement->logo) }}" alt="Logo" class="rounded-circle"
                            style="width: 48px; height: 48px; object-fit: cover;">
                    @endif
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $etablissement->nom }}</h5>
                        @if($tableNumber)
                            <span class="badge bg-primary rounded-pill">Table {{ $tableNumber }}</span>
                        @endif
                    </div>
                </div>
                <button wire:click="$toggle('showCart')" class="btn btn-dark rounded-circle position-relative p-2"
                    style="width: 44px; height: 44px;">
                    <i class="fas fa-shopping-cart"></i>
                    @if(count($cart) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ count($cart) }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </header>

    <div class="container py-4 px-4">
        @if(session('order_placed'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 text-center py-4 mb-4">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h4 class="fw-bold">{{ session('order_placed') }}</h4>
                <p class="mb-0">Détendez-vous, nous arrivons.</p>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 text-center py-4 mb-4">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h5 class="fw-bold mb-0">{{ session('error') }}</h5>
            </div>
        @endif

        <!-- Categories horizontal scroll -->
        <div class="d-flex gap-2 overflow-auto pb-3 mb-4" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($categories as $category)
                <button wire:click="$set('activeCategory', {{ $category->id }})"
                    class="btn rounded-pill px-4 text-nowrap {{ $activeCategory == $category->id ? 'btn-primary' : 'btn-white border shadow-sm' }}"
                    style="{{ $activeCategory == $category->id ? 'background: ' . ($etablissement->theme_color ?? '#bf3a29') . '; border: none;' : '' }}">
                    {{ $category->nom }}
                </button>
            @endforeach
        </div>

        <!-- Products -->
        <div class="row g-3">
            @foreach($categories->where('id', $activeCategory)->first()?->produits ?? [] as $product)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 flex-row">
                        <div class="p-3 flex-grow-1">
                            <h6 class="fw-bold mb-1">{{ $product->nom }}</h6>
                            <p class="small text-muted mb-2 line-clamp-2">{{ $product->description }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="fw-bold" style="color: {{ $etablissement->theme_color ?? '#bf3a29' }};">
                                    {{ number_format($product->prix_vente, 0, ',', ' ') }}
                                    {{ $etablissement->devise_display }}
                                </span>
                                <button
                                    wire:click="addToCart({{ $product->id }}, '{{ addslashes($product->nom) }}', {{ $product->prix_vente }})"
                                    class="btn btn-sm btn-light rounded-circle shadow-sm"
                                    style="width: 32px; height: 32px; padding: 0;">
                                    <i class="fas fa-plus text-primary"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex-shrink-0" style="width: 100px;">
                            <img src="{{ $product->image_url }}" alt="{{ $product->nom }}" class="w-100 h-100"
                                style="object-fit: cover;"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($product->nom) }}&background=f8f9fa&color=bf3a29';">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Cart Overlay -->
    @if($showCart)
        <div class="cart-overlay animate__animated animate__fadeIn"
            style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050;"
            wire:click="$set('showCart', false)"></div>
        <div class="cart-sidebar bg-white shadow-lg animate__animated animate__slideInRight"
            style="position: fixed; top: 0; right: 0; bottom: 0; width: 350px; z-index: 1060; max-width: 90vw;">
            <div class="d-flex flex-column h-100">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Mon Panier</h5>
                    <button wire:click="$set('showCart', false)" class="btn-close"></button>
                </div>
                <div class="flex-grow-1 overflow-auto p-4">
                    @forelse($cart as $id => $item)
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0">{{ $item['name'] }}</h6>
                                <span class="small text-muted">{{ number_format($item['price'], 0, ',', ' ') }}
                                    {{ $etablissement->devise_display }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button wire:click="removeFromCart({{ $id }})" class="btn btn-sm btn-light rounded-circle p-0"
                                    style="width: 24px; height: 24px;"><i class="fas fa-minus small"></i></button>
                                <span class="fw-bold">{{ $item['quantity'] }}</span>
                                <button
                                    wire:click="addToCart({{ $id }}, '{{ addslashes($item['name']) }}', {{ $item['price'] }})"
                                    class="btn btn-sm btn-light rounded-circle p-0" style="width: 24px; height: 24px;"><i
                                        class="fas fa-plus small"></i></button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-shopping-basket fa-3x mb-3 opacity-25"></i>
                            <p>Votre panier est vide</p>
                        </div>
                    @endforelse
                </div>
                @if(count($cart) > 0)
                    <div class="p-4 bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="h6 mb-0">Total</span>
                            <span class="h4 fw-bold text-primary mb-0">{{ number_format($this->cartTotal, 0, ',', ' ') }}
                                {{ $etablissement->devise_display }}</span>
                        </div>
                        <button wire:click="submitOrder" class="btn btn-primary w-100 btn-lg shadow"
                            style="background: {{ $etablissement->theme_color ?? '#bf3a29' }}; border: none;">
                            Commander maintenant
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>