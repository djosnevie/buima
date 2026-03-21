<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Gestion des Managers</h2>
                <p class="text-muted">Gérez les propriétaires de restaurants et leurs établissements.</p>
            </div>
            <button class="btn btn-primary text-white" wire:click="create">
                <i class="fas fa-user-plus me-2"></i>Nouveau Manager
            </button>
        </div>
    </div>

    {{-- Feedback --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stats rapides --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary"
                        style="width:50px;height:50px;flex-shrink:0;">
                        <i class="fas fa-user-tie fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Total Managers</div>
                        <h3 class="fw-bold mb-0">{{ $managers->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success"
                        style="width:50px;height:50px;flex-shrink:0;">
                        <i class="fas fa-store fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Restaurants Assignés</div>
                        <h3 class="fw-bold mb-0">
                            {{ $managers->sum(fn($m) => $m->ownedEstablishments->count()) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning"
                        style="width:50px;height:50px;flex-shrink:0;">
                        <i class="fas fa-store-slash fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Sans Restaurant</div>
                        <h3 class="fw-bold mb-0">
                            {{ $managers->filter(fn($m) => $m->ownedEstablishments->isEmpty())->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="fw-bold mb-0">
                    Liste des Managers
                    <span class="badge bg-primary-subtle text-primary rounded-pill ms-2 fs-6">{{ $managers->count() }}</span>
                </h5>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold">MANAGER</th>
                            <th class="py-3 text-muted small fw-bold">RESTAURANTS POSSÉDÉS</th>
                            <th class="py-3 text-muted small fw-bold">DATE DE CRÉATION</th>
                            <th class="pe-4 py-3 text-end text-muted small fw-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($managers as $manager)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center me-3"
                                            style="width:42px;height:42px;font-size:1rem;flex-shrink:0;">
                                            {{ strtoupper(substr($manager->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $manager->name }}</div>
                                            <div class="small text-muted">{{ $manager->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($manager->ownedEstablishments->isEmpty())
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">
                                            <i class="fas fa-exclamation-circle me-1"></i>Non assigné
                                        </span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($manager->ownedEstablishments as $etab)
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                                    <i class="fas fa-store me-1"></i>{{ $etab->nom }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="text-muted small">{{ $manager->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <button wire:click="edit({{ $manager->id }})"
                                        class="btn btn-sm btn-link text-primary me-1"
                                        title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete({{ $manager->id }})"
                                        class="btn btn-sm btn-link text-danger"
                                        title="Supprimer"
                                        onclick="confirm('Êtes-vous sûr de vouloir supprimer ce manager ? Les restaurants lui étant assignés seront détachés.') || event.stopImmediatePropagation()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-tie fa-2x mb-3 text-secondary opacity-50 d-block"></i>
                                    <p class="mb-0">Aucun manager trouvé.</p>
                                    <small>Créez un manager avec le bouton ci-dessus.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Création / Edition --}}
    @if($isOpen)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);"
            aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h5 class="modal-title fw-bold">
                                @if($isEditing)
                                    <i class="fas fa-user-edit me-2 text-primary"></i>Modifier le Manager
                                @else
                                    <i class="fas fa-user-plus me-2 text-primary"></i>Nouveau Manager
                                @endif
                            </h5>
                            <p class="text-muted small mb-0">
                                {{ $isEditing ? 'Modifiez les informations du manager et ses restaurants.' : 'Créez un nouveau compte manager et assignez-lui des restaurants.' }}
                            </p>
                        </div>
                        <button type="button" class="btn-close" wire:click="resetForm"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="save">

                            {{-- Informations personnelles --}}
                            <h6 class="fw-bold text-muted text-uppercase small mb-3">
                                <i class="fas fa-user me-2"></i>Informations Personnelles
                            </h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Nom Complet</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" placeholder="Jean Dupont">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        wire:model="email" placeholder="jean@example.com">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">
                                    Mot de passe
                                    @if($isEditing)
                                        <span class="text-muted fw-normal">(Laisser vide pour ne pas changer)</span>
                                    @endif
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    wire:model="password"
                                    placeholder="{{ $isEditing ? '••••••••' : 'Minimum 8 caractères' }}">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-4">

                            {{-- Assignation restaurants --}}
                            <h6 class="fw-bold text-muted text-uppercase small mb-3">
                                <i class="fas fa-store me-2"></i>Restaurants Assignés
                            </h6>
                            @error('assignedEtablissementIds') 
                                <div class="alert alert-danger small py-2">{{ $message }}</div> 
                            @enderror

                            @if($etablissements->isEmpty())
                                <div class="alert alert-info small py-2">
                                    <i class="fas fa-info-circle me-2"></i>Aucun restaurant disponible. 
                                    <a href="{{ route('setup.restaurant') }}" class="alert-link">Créez-en un d'abord.</a>
                                </div>
                            @else
                                <div class="row g-2">
                                    @foreach($etablissements as $etab)
                                        <div class="col-md-6">
                                            <div class="form-check border rounded-3 p-3 ps-5 {{ in_array($etab->id, $assignedEtablissementIds) ? 'border-primary bg-primary bg-opacity-5' : '' }}">
                                                <input class="form-check-input ms-0 me-3"
                                                    type="checkbox"
                                                    wire:model.live="assignedEtablissementIds"
                                                    value="{{ $etab->id }}"
                                                    id="etab_{{ $etab->id }}">
                                                <label class="form-check-label w-100" for="etab_{{ $etab->id }}" style="cursor:pointer;">
                                                    <span class="fw-bold d-block">{{ $etab->nom }}</span>
                                                    <small class="text-muted">
                                                        @if($etab->manager_id && $etab->manager_id != $selectedManagerId)
                                                            <span class="text-warning">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                Déjà assigné à un autre manager
                                                            </span>
                                                        @else
                                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $etab->adresse ?? $etab->type }}
                                                        @endif
                                                    </small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Un restaurant peut avoir un seul manager. L'assigner ici remplacera tout manager précédent.
                                </small>
                            @endif

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary text-white px-4 py-2 fw-bold flex-grow-1">
                                    <i class="fas fa-save me-2"></i>Enregistrer
                                </button>
                                <button type="button" class="btn btn-light text-muted px-4" wire:click="resetForm">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
