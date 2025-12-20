<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Catégories de Dépenses</h4>
        <button wire:click="openModal()" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nouvelle Catégorie
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="p-3 border-bottom">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" wire:model.live="search" class="form-control border-start-0"
                        placeholder="Rechercher...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Nom</th>
                            <th>Description</th>
                            <th>Date Création</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $cat->nom }}</td>
                                <td>{{ Str::limit($cat->description, 50) }}</td>
                                <td>{{ $cat->created_at->format('d/m/Y') }}</td>
                                <td class="text-end pe-3">
                                    <button wire:click="openModal({{ $cat->id }})"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        onclick="confirm('Supprimer cette catégorie ?') || event.stopImmediatePropagation()"
                                        wire:click="delete({{ $cat->id }})" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Aucune catégorie trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">{{ $categorieId ? 'Modifier' : 'Ajouter' }} une Catégorie</h5>
                        <button type="button" class="btn-close" wire:click="$set('isModalOpen', false)"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nom de la catégorie</label>
                                <input type="text" wire:model="nom" class="form-control"
                                    placeholder="ex: Loyers, Salaires, Achats...">
                                @error('nom') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description (Optionnel)</label>
                                <textarea wire:model="description" class="form-control" rows="3"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light"
                                wire:click="$set('isModalOpen', false)">Annuler</button>
                            <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>