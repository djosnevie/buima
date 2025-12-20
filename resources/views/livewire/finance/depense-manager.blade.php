<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Gestion des Dépenses</h4>
        <div>
            <a href="{{ route('finance.categories') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-tags me-2"></i> Catégories
            </a>
            <button wire:click="openModal()" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nouvelle Dépense
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
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
                            <th class="ps-3">Date</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th>Référence</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depenses as $depense)
                            <tr>
                                <td class="ps-3">{{ $depense->date_depense->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $depense->categorieDepense->nom ?? 'Inconnue' }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($depense->description, 40) }}</td>
                                <td><code class="small text-muted">{{ $depense->reference_piece ?? '-' }}</code></td>
                                <td class="text-end fw-bold text-danger">
                                    -{{ number_format($depense->montant, 0, ',', ' ') }}
                                    {{ auth()->user()->etablissement->devise_display }}
                                </td>
                                <td class="text-end pe-3">
                                    <button wire:click="openModal({{ $depense->id }})"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        onclick="confirm('Supprimer cette dépense ?') || event.stopImmediatePropagation()"
                                        wire:click="delete({{ $depense->id }})" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Aucune dépense trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $depenses->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">{{ $depenseId ? 'Modifier' : 'Ajouter' }} une Dépense</h5>
                        <button type="button" class="btn-close" wire:click="$set('isModalOpen', false)"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Date</label>
                                    <input type="date" wire:model="date_depense" class="form-control">
                                    @error('date_depense') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Montant</label>
                                    <div class="input-group">
                                        <input type="number" wire:model="montant" class="form-control" placeholder="0.00">
                                        <span
                                            class="input-group-text">{{ auth()->user()->etablissement->devise_display }}</span>
                                    </div>
                                    @error('montant') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Catégorie</label>
                                    <select wire:model="categorie_depense_id" class="form-select">
                                        <option value="">Sélectionner une catégorie...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('categorie_depense_id') <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Description</label>
                                    <input type="text" wire:model="description" class="form-control"
                                        placeholder="ex: Facture électricité Nov">
                                    @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Référence Pièce (Optionnel)</label>
                                    <input type="text" wire:model="reference_piece" class="form-control"
                                        placeholder="Numéro facture, reçu...">
                                    @error('reference_piece') <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
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