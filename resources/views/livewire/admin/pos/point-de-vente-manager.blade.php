<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Mes Points de Vente</h4>
            <p class="text-muted small mb-0">Gérez vos différents établissements et succursales</p>
        </div>
        <button wire:click="openModal" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
            <i class="fas fa-plus-circle me-2"></i>Nouveau Point de Vente
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="fas fa-search"></i>
                </span>
                <input wire:model.live="search" type="text" class="form-control border-start-0 ps-0"
                    placeholder="Rechercher par nom...">
            </div>
        </div>
    </div>

    <!-- Restaurants Grid -->
    <div class="row g-4">
        @forelse($etablissements as $etablissement)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 restaurant-card overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;">
                                <i class="fas fa-store fa-lg"></i>
                            </div>
                            <span
                                class="badge bg-{{ $etablissement->actif ? 'success' : 'danger' }}-subtle text-{{ $etablissement->actif ? 'success' : 'danger' }} px-3 py-2">
                                {{ $etablissement->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $etablissement->nom }}</h5>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1"></i>
                            {{ $etablissement->adresse ?? 'Adresse non définie' }}</p>

                        <div class="d-flex gap-2 mb-3">
                            <div class="flex-grow-1 bg-light rounded-3 p-2 text-center">
                                <span class="d-block text-muted tiny-text">Serveurs</span>
                                <span class="fw-bold">{{ $etablissement->users->count() }}</span>
                            </div>
                            <div class="flex-grow-1 bg-light rounded-3 p-2 text-center">
                                <span class="d-block text-muted tiny-text">Tables</span>
                                <span class="fw-bold">{{ $etablissement->tables_count ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button wire:click="edit({{ $etablissement->id }})"
                                class="btn btn-outline-primary flex-grow-1 rounded-3">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </button>
                            @if(auth()->user()->etablissement_id != $etablissement->id)
                                <button wire:click="switchEtablissement({{ $etablissement->id }})"
                                    class="btn btn-primary rounded-3 text-white">
                                    <i class="fas fa-sign-in-alt me-1"></i> Activer
                                </button>
                            @else
                                <span class="badge bg-success d-flex align-items-center px-3">Actif</span>
                            @endif
                            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-3">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
                    <div class="py-4">
                        <i class="fas fa-store-slash fa-4x text-muted opacity-25 mb-3"></i>
                        <h5 class="text-muted">Aucun point de vente trouvé</h5>
                        <button wire:click="openModal" class="btn btn-primary mt-3">Créer mon premier point de
                            vente</button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal -->
    @if($isOpen)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-0 p-4">
                        <h5 class="modal-title fw-bold">
                            {{ $selectedId ? 'Modifier Point de Vente' : 'Nouveau Point de Vente' }}
                        </h5>
                        <button wire:click="$set('isOpen', false)" type="button" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4 pt-0">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Nom de l'établissement</label>
                                <input wire:model="nom" type="text" class="form-control rounded-3"
                                    placeholder="Ex: Restaurant Ma Campagne">
                                @error('nom') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium">Type</label>
                                    <select wire:model="type" class="form-select rounded-3">
                                        <option value="mixte">Mixte</option>
                                        <option value="avec_tables">Avec Tables</option>
                                        <option value="sans_tables">Emporter</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium">Devise</label>
                                    <select wire:model="devise" class="form-select rounded-3">
                                        <option value="XAF">FCFA</option>
                                        <option value="CDF">FC</option>
                                        <option value="USD">USD ($)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Téléphone</label>
                                <input wire:model="telephone" type="text" class="form-control rounded-3">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Adresse</label>
                                <textarea wire:model="adresse" class="form-control rounded-3" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" wire:click="$set('isOpen', false)"
                                class="btn btn-light px-4">Annuler</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                {{ $selectedId ? 'Enregistrer les modifications' : 'Créer l\'établissement' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .tiny-text {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .restaurant-card {
            transition: transform 0.2s;
        }

        .restaurant-card:hover {
            transform: translateY(-5px);
        }
    </style>
</div>