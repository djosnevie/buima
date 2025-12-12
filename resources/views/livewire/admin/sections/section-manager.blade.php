<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Gestion des Sections</h2>
                <p class="text-muted">Gérez les différentes zones de votre restaurant (Cuisine, Bar, Salle, etc.)</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Formulaire Creation/Edition -->
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">{{ $isEditing ? 'Modifier la Section' : 'Nouvelle Section' }}</h5>

                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">NOM DE LA SECTION</label>
                                <input type="text" class="form-control bg-light border-0" wire:model="nom"
                                    placeholder="Ex: Cuisine Principale">
                                @error('nom') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">DESCRIPTION (Optionnel)</label>
                                <textarea class="form-control bg-light border-0" wire:model="description" rows="3"
                                    placeholder="Description de la zone..."></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            @if(auth()->user()->isSuperAdmin())
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Établissement</label>
                                    <select class="form-select bg-light border-0" wire:model="etablissement_id">
                                        <option value="">Sélectionner un restaurant</option>
                                        @foreach($etablissements as $etablissement)
                                            <option value="{{ $etablissement->id }}">{{ $etablissement->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('etablissement_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="actifSwitch" wire:model="actif">
                                <label class="form-check-label" for="actifSwitch">Section Active</label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary text-white fw-bold py-2">
                                    {{ $isEditing ? 'Mettre à jour' : 'Créer la Section' }}
                                </button>
                                @if($isEditing)
                                    <button type="button" class="btn btn-light text-muted" wire:click="create">
                                        Annuler
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Liste des Sections -->
        <div class="{{ (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) ? 'col-md-8' : 'col-md-12' }}">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold" style="width: 40%;">NOM</th>
                                    @if(auth()->user()->isSuperAdmin())
                                        <th class="py-3 text-muted small fw-bold">ÉTABLISSEMENT</th>
                                    @endif
                                    <th class="py-3 text-muted small fw-bold">STATUT</th>
                                    <th class="py-3 text-muted small fw-bold">UTILISATEURS</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sections as $section)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold">{{ $section->nom }}</div>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;">
                                                {{ $section->description }}
                                            </div>
                                        </td>
                                        @if(auth()->user()->isSuperAdmin())
                                            <td>
                                                <span
                                                    class="fw-bold text-dark">{{ $section->etablissement->nom ?? 'N/A' }}</span>
                                            </td>
                                        @endif
                                        <td>
                                            @if($section->actif)
                                                <span
                                                    class="badge bg-success-subtle text-success rounded-pill px-3">Actif</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger rounded-pill px-3">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="showUsers({{ $section->id }})"
                                                class="btn badge bg-light text-dark border hover-shadow"
                                                style="cursor: pointer;">
                                                <i class="fas fa-users me-1" style="color: #bf3a29;"></i>
                                                {{ $section->users->count() }} membres
                                            </button>
                                        </td>
                                        <td class="pe-4 text-end">
                                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                                <button wire:click="edit({{ $section->id }})"
                                                    class="btn btn-sm btn-link text-primary me-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button wire:click="delete({{ $section->id }})"
                                                    class="btn btn-sm btn-link text-danger"
                                                    onclick="confirm('Êtes-vous sûr de vouloir supprimer cette section ?') || event.stopImmediatePropagation()">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-layer-group fa-2x mb-3 text-secondary opacity-50"></i>
                                            <p class="mb-0">Aucune section configurée.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users List Modal -->
    @if($isUsersModalOpen)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true"
            role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Utilisateurs - {{ $viewingSectionName }}</h5>
                        <button type="button" class="btn-close" wire:click="closeUsersModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        @if(count($usersInSection) > 0)
                            <div class="list-group list-group-flush">
                                @foreach($usersInSection as $user)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom-0 border-top">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initial rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                                <div class="small text-muted">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-dark">{{ ucfirst($user->role) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-2x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-0">Aucun utilisateur assigné à cette section.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" wire:click="closeUsersModal">Fermer</button>
                        <a href="{{ route('settings.users') }}" class="btn btn-primary text-white">Gérer les
                            utilisateurs</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>