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
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Session Active</h5>
                            <span class="badge bg-success">OUVERTE</span>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Ouverte par :</span>
                                <span class="fw-bold">{{ $currentSession->user->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Date d'ouverture :</span>
                                <span class="fw-bold">{{ $currentSession->date_ouverture->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Montant initial :</span>
                                <span class="fw-bold">{{ number_format($currentSession->montant_ouverture, 0, ',', ' ') }}
                                    {{ auth()->user()->etablissement->devise_display }}</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <form wire:submit.prevent="fermerSession">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Montant réel en caisse</label>
                                <input type="number" wire:model="montant_fermeture_reel"
                                    class="form-control form-control-lg" placeholder="Comptez les espèces...">
                                @error('montant_fermeture_reel') <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Notes / Observations</label>
                                <textarea wire:model="notes" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100 btn-lg">
                                Fermer la Caisse
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