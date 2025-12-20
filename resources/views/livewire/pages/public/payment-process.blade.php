<div class="payment-process bg-light min-vh-100 d-flex align-items-center">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                        <div class="mb-3">
                            @if($etablissement->logo)
                                <img src="{{ asset('storage/' . $etablissement->logo) }}" alt="Logo"
                                    class="rounded-circle shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 70px; height: 70px; font-size: 24px; font-weight: bold;">
                                    {{ substr($etablissement->nom, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-1">{{ $etablissement->nom }}</h4>
                        <p class="text-muted small">Finalisation de votre commande</p>
                    </div>

                    <div class="card-body p-4">
                        @if($paymentStatus === 'success')
                            <div class="text-center py-4 animate__animated animate__zoomIn">
                                <i class="fas fa-check-circle text-success fa-4x mb-4"></i>
                                <h3 class="fw-bold mb-2">Merci !</h3>
                                <p class="text-muted mb-4">Votre paiement de
                                    <strong>{{ number_format($commande->total, 0, ',', ' ') }}
                                        {{ $etablissement->devise_display }}</strong> a été confirmé.</p>
                                <a href="{{ route('public.menu', $etablissement->slug) }}"
                                    class="btn btn-primary btn-lg rounded-pill px-5 w-100"
                                    style="background: var(--primary-color); border: none;">
                                    Retour au Menu
                                </a>
                            </div>
                        @else
                            <div class="total-box bg-light rounded-4 p-4 text-center mb-4">
                                <span class="text-muted d-block small mb-1">Montant à payer</span>
                                <h2 class="fw-bold mb-0" style="color: var(--primary-color);">
                                    {{ number_format($commande->total, 0, ',', ' ') }} {{ $etablissement->devise_display }}
                                </h2>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-3">Sélectionnez votre moyen de paiement</label>
                                <div class="d-flex flex-column gap-3">
                                    <label
                                        class="payment-option p-3 border rounded-4 d-flex align-items-center gap-3 cursor-pointer {{ $method === 'mobile_money' ? 'border-primary bg-light' : '' }}">
                                        <input type="radio" wire:model.live="method" value="mobile_money" class="d-none">
                                        <div class="icon bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                                            <i class="fas fa-mobile-alt fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">Mobile Money</div>
                                            <div class="small text-muted">Orange, MTN...</div>
                                        </div>
                                        @if($method === 'mobile_money') <i class="fas fa-check-circle text-primary"></i>
                                        @endif
                                    </label>

                                    <label
                                        class="payment-option p-3 border rounded-4 d-flex align-items-center gap-3 cursor-pointer {{ $method === 'card' ? 'border-primary bg-light' : '' }}">
                                        <input type="radio" wire:model.live="method" value="card" class="d-none">
                                        <div class="icon bg-info bg-opacity-10 text-info rounded-3 p-2">
                                            <i class="fas fa-credit-card fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">Carte Bancaire</div>
                                            <div class="small text-muted">Visa, Mastercard...</div>
                                        </div>
                                        @if($method === 'card') <i class="fas fa-check-circle text-primary"></i> @endif
                                    </label>
                                </div>
                            </div>

                            @if($method === 'mobile_money')
                                <div class="mb-4 animate__animated animate__fadeIn">
                                    <label class="form-label fw-bold">Numéro de téléphone</label>
                                    <input type="tel" wire:model="phoneNumber" class="form-control form-control-lg rounded-3"
                                        placeholder="Ex: 06 123 45 67">
                                    <div class="form-text small">Vous recevrez une demande de confirmation sur votre mobile.
                                    </div>
                                </div>
                            @endif

                            <button wire:click="processPayment" wire:loading.attr="disabled"
                                class="btn btn-primary btn-lg w-100 rounded-pill py-3 shadow-sm mt-2"
                                style="background: var(--primary-color); border: none;">
                                <span wire:loading.remove wire:target="processPayment">
                                    Confirmer le paiement
                                </span>
                                <span wire:loading wire:target="processPayment">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Traitement...
                                </span>
                            </button>

                            <p class="text-center text-muted small mt-4">
                                <i class="fas fa-lock me-1"></i> Paiement sécurisé et crypté
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .payment-option {
            transition: all 0.2s ease;
        }

        .payment-option:hover {
            border-color: var(--primary-color);
            background: #fff8f7;
        }

        .payment-option.border-primary {
            border-width: 2px !important;
        }
    </style>
</div>