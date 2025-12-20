@section('title', $supplierId ? 'Modifier Fournisseur' : 'Nouveau Fournisseur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Fournisseurs</a></li>
    <li class="breadcrumb-item active">{{ $supplierId ? 'Modifier' : 'Nouveau' }}</li>
@endsection

<div class="supplier-form-container">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form wire:submit.prevent="save">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nom du fournisseur <span class="text-danger">*</span></label>
                        <input type="text" wire:model="nom" class="form-control @error('nom') is-invalid @enderror"
                            placeholder="Ex: Brasserie du Congo">
                        @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Personne de contact</label>
                        <input type="text" wire:model="contact"
                            class="form-control @error('contact') is-invalid @enderror" placeholder="Ex: Jean Dupont">
                        @error('contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Téléphone</label>
                        <input type="text" wire:model="telephone"
                            class="form-control @error('telephone') is-invalid @enderror" placeholder="+242 ...">
                        @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="contact@fournisseur.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Adresse</label>
                        <textarea wire:model="adresse" class="form-control @error('adresse') is-invalid @enderror"
                            rows="3" placeholder="Adresse complète..."></textarea>
                        @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-light px-4">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4"
                            style="background: var(--primary-color); border: none;">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>