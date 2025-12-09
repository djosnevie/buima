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

        <!-- Liste des Sections -->
        <div class="col-md-8">
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
                                            <span class="badge bg-light text-dark border">{{ $section->users->count() }}
                                                membres</span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button wire:click="edit({{ $section->id }})"
                                                class="btn btn-sm btn-link text-primary me-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="delete({{ $section->id }})"
                                                class="btn btn-sm btn-link text-danger"
                                                onclick="confirm('Êtes-vous sûr de vouloir supprimer cette section ?') || event.stopImmediatePropagation()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
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
</div>