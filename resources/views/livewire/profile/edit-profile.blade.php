<div>
    @if($isOpen)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true"
            role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Modifier mon Profil</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Nom Complet</label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Email</label>
                                <input type="email" class="form-control" wire:model="email">
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Rôle</label>
                                    <input type="text" class="form-control bg-light" wire:model="role" readonly disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Section</label>
                                    <input type="text" class="form-control bg-light" wire:model="section" readonly disabled>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Nouveau mot de passe (optionnel)</label>
                                <input type="password" class="form-control" wire:model="password"
                                    placeholder="Laisser vide pour conserver l'actuel">
                                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary text-white py-2 fw-bold">Enregistrer</button>
                                <button type="button" class="btn btn-light text-muted"
                                    wire:click="closeModal">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>