<div>
    @if($isOpen)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">Ajustement de Stock</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4">
                            @if($item)
                                <div class="bg-light p-3 rounded-3 mb-4">
                                    <div class="small text-muted">Ajustement pour :</div>
                                    <div class="fw-bold h5 mb-0">{{ $item->nom }}</div>
                                    <div class="small">Stock actuel :
                                        <strong>{{ $typeItem === 'produit' ? ($item->stock->quantite ?? 0) : $item->stock_actuel }}</strong>
                                    </div>
                                    <div class="small">Seuil d'alerte actuel :
                                        <strong>{{ $typeItem === 'produit' ? ($item->stock->seuil_alerte ?? 0) : $item->seuil_alerte }}</strong>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold">Seuil d'alerte</label>
                                <input type="number" step="0.01" wire:model="seuilAlerte"
                                    class="form-control @error('seuilAlerte') is-invalid @enderror" placeholder="0.00">
                                @error('seuilAlerte') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text small">Définit quand l'article apparaît en alerte stock faible.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Type de mouvement</label>
                                <div class="d-flex gap-2">
                                    <button type="button" wire:click="$set('typeMouvement', 'entree')"
                                        class="btn flex-fill {{ $typeMouvement === 'entree' ? 'btn-success' : 'btn-outline-success' }}">
                                        <i class="fas fa-plus-circle me-1"></i> Entrée
                                    </button>
                                    <button type="button" wire:click="$set('typeMouvement', 'sortie')"
                                        class="btn flex-fill {{ $typeMouvement === 'sortie' ? 'btn-danger' : 'btn-outline-danger' }}">
                                        <i class="fas fa-minus-circle me-1"></i> Sortie
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Quantité</label>
                                <input type="number" step="0.01" wire:model="quantite"
                                    class="form-control @error('quantite') is-invalid @enderror" placeholder="0.00">
                                @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Motif / Commentaire</label>
                                <textarea wire:model="motif" class="form-control @error('motif') is-invalid @enderror"
                                    rows="3" placeholder="Ex: Livraison fournisseur, Casse, Inventaire..."></textarea>
                                @error('motif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light px-4" wire:click="close">Annuler</button>
                            <button type="submit" class="btn btn-primary px-4"
                                style="background: var(--primary-color); border: none;">Confirmer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>