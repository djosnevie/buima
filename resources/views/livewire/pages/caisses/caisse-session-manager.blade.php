@section('title', 'Sessions de Caisse - ' . $caisse->nom)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('caisses.index') }}">Caisses</a></li>
    <li class="breadcrumb-item active">Sessions</li>
@endsection

<div class="session-management">
    @if(!$currentSession)
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow rounded-4 p-4 text-center">
                    <div class="mb-4 text-primary"><i class="fas fa-lock fa-3x"></i></div>
                    <h4 class="fw-bold mb-3">La caisse est fermée</h4>
                    <p class="text-muted mb-4">Veuillez entrer le montant initial en caisse pour ouvrir la session.</p>
                    <form wire:submit.prevent="ouvrirSession">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Montant d'ouverture</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white"><i class="fas fa-money-bill"></i></span>
                                <input type="number" wire:model="montant_ouverture" class="form-control" placeholder="0.00">
                            </div>
                            @error('montant_ouverture') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100"
                            style="background: var(--primary-color); border: none;">
                            Ouvrir la Caisse
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        @php
            $devise = auth()->user()->etablissement->devise_display ?? 'FCFA';
            $fmt = fn($v) => number_format((float)$v, 0, ',', ' ') . ' ' . $devise;
        @endphp
        <div class="row g-4">
            <!-- Rapport de fermeture -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Rapport de Caisse</h5>
                            <span class="badge bg-success">OUVERTE</span>
                        </div>
                        <small class="text-muted d-block mb-3">
                            <i class="fas fa-user me-1"></i> {{ $currentSession->user->name }}
                            &nbsp;|&nbsp;
                            <i class="fas fa-clock me-1"></i> {{ $currentSession->date_ouverture->format('d/m/Y H:i') }}
                        </small>

                        {{-- Grille du rapport --}}
                        <div class="rounded-3 p-3 mb-3" style="background:#f8f9fa;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small"><i class="fas fa-wallet me-1"></i> Fond de caisse</span>
                                <span class="fw-bold">{{ $fmt($currentSession->montant_ouverture) }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small fw-semibold">Total des ventes</span>
                                <span class="fw-bold text-success">{{ $fmt($stats['total_ventes'] ?? 0) }}</span>
                            </div>
                            {{-- Détail par mode de paiement --}}
                            <div class="ps-3 mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small"><i class="fas fa-money-bill-wave me-1"></i> Cash</span>
                                    <span class="small">{{ $fmt($stats['par_mode']['especes'] ?? 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small"><i class="fas fa-mobile-alt me-1"></i> Mobile Money</span>
                                    <span class="small">{{ $fmt($stats['par_mode']['mobile_money'] ?? 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small"><i class="fas fa-credit-card me-1"></i> Carte</span>
                                    <span class="small">{{ $fmt($stats['par_mode']['carte'] ?? 0) }}</span>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold">
                                <span class="small">Montant attendu (total ventes)</span>
                                <span class="text-primary">{{ $fmt($stats['montant_attendu'] ?? 0) }}</span>
                            </div>
                        </div>

                        {{-- Formulaire de fermeture --}}
                        <form wire:submit.prevent="fermerSession">
                            <div class="mb-2">
                                <label class="form-label fw-bold">Montant réel compté</label>
                                <input type="number" wire:model.live="montant_fermeture_reel"
                                    class="form-control form-control-lg" placeholder="Comptez les espèces...">
                                @error('montant_fermeture_reel') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            {{-- Aperçu de l'écart en temps réel --}}
                            @if($montant_fermeture_reel !== null && $montant_fermeture_reel !== '')
                                @php
                                    $ecartVal = (float)$montant_fermeture_reel - (float)($stats['montant_attendu'] ?? 0);
                                    $ecartClass = $ecartVal < 0 ? 'danger' : ($ecartVal > 0 ? 'warning' : 'success');
                                    $ecartLabel = $ecartVal < 0 ? 'Manquant' : ($ecartVal > 0 ? 'Excédent' : 'Parfait ✓');
                                @endphp
                                <div class="alert alert-{{ $ecartClass }} py-2 px-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Écart : {{ $ecartLabel }}</span>
                                        <span class="fw-bold">{{ $fmt(abs($ecartVal)) }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Notes / Observations</label>
                                <textarea wire:model="notes" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100 btn-lg">
                                <i class="fas fa-lock me-2"></i> Fermer la Caisse
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Historique des Sessions</h5>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="text-muted">
                                    <tr>
                                        <th>Ouverture</th>
                                        <th>Fermeture</th>
                                        <th>Responsable</th>
                                        <th class="text-end">Écart</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $sess)
                                        <tr>
                                            <td>{{ $sess->date_ouverture->format('d/m/Y H:i') }}</td>
                                            <td>{{ $sess->date_fermeture ? $sess->date_fermeture->format('d/m/Y H:i') : '-' }}
                                            </td>
                                            <td>{{ $sess->user->name }}</td>
                                            <td
                                                class="text-end fw-bold {{ $sess->ecart < 0 ? 'text-danger' : ($sess->ecart > 0 ? 'text-warning' : 'text-success') }}">
                                                {{ $sess->date_fermeture ? number_format($sess->ecart, 0, ',', ' ') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $history->links('livewire.custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>