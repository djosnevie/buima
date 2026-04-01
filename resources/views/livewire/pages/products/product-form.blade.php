@section('title', $produit ? 'Modifier le Produit' : 'Nouveau Produit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produits</a></li>
    <li class="breadcrumb-item active">{{ $produit ? 'Modifier' : 'Créer' }}</li>
@endsection

<div class="product-form-container">
    <form wire:submit.prevent="save">
        <div class="form-header-bar d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="form-title m-0 fw-bold">{{ $produit ? 'Modifier le Produit' : 'Ajouter un Nouveau Produit' }}</h2>
                <p class="text-muted m-0">Renseignez les informations de base, la tarification et le média.</p>
            </div>
            <div class="form-actions d-flex gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-light border shadow-sm">
                    <i class="fas fa-times me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary shadow-sm" style="background-color: var(--primary-color); border: none;">
                    <i class="fas fa-save me-1"></i> {{ $produit ? 'Enregistrer les modifications' : 'Créer le Produit' }}
                </button>
            </div>
        </div>

        @if (session()->has('error'))
            <div class="alert alert-danger shadow-sm border-0 mb-4 rounded-3">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Column : Main Intel -->
            <div class="col-lg-8">
                
                <!-- CARD 1: GENERAL INFO -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0 text-dark">Informations Générales</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold">Nom du produit <span class="text-danger">*</span></label>
                            <input type="text" wire:model="nom" class="form-control form-control-lg bg-light border-0" placeholder="Ex: Poulet Mayo, Coca-Cola...">
                            @error('nom') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted fw-semibold">Description</label>
                            <textarea wire:model="description" class="form-control bg-light border-0" rows="3" placeholder="Informations complémentaires, ingrédients, allergènes..."></textarea>
                            @error('description') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- CARD 2: MEDIA -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0 text-dark">Image du Produit</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="upload-zone position-relative rounded-4 p-5 text-center border mt-2 mb-2" style="border-style: dashed !important; border-width: 2px !important; border-color: #dee2e6 !important; background-color: #f8f9fa;">
                            <input type="file" wire:model="newImage" id="image-upload" class="position-absolute w-100 h-100" style="opacity: 0; top: 0; left: 0; cursor: pointer; z-index: 10;">
                            
                            @if($newImage)
                                <img src="{{ $newImage->temporaryUrl() }}" class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 200px; object-fit: cover;">
                                <h6 class="text-primary fw-bold m-0"><i class="fas fa-sync-alt me-1"></i> Cliquez ou glissez pour changer</h6>
                            @elseif($image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public_uploads')->url($image) }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($nom) }}&background=f8f9fa&color=bf3a29';" class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 200px; object-fit: cover;">
                                <h6 class="text-primary fw-bold m-0"><i class="fas fa-sync-alt me-1"></i> Cliquez ou glissez pour changer l'image</h6>
                            @else
                                <div class="text-muted d-flex flex-column align-items-center justify-content-center" style="height: 150px;">
                                    <i class="fas fa-cloud-upload-alt text-secondary mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5 class="fw-bold mb-1">Glissez une image ici</h5>
                                    <p class="small mb-0">ou cliquez pour parcourir (JPG, PNG - Max 2MB)</p>
                                </div>
                            @endif
                            <div wire:loading wire:target="newImage" class="mt-3 text-primary">
                                <i class="fas fa-spinner fa-spin me-2"></i> Chargement...
                            </div>
                        </div>
                        @error('newImage') <div class="text-danger mt-2 small text-center"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- CARD 3: PRICING -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0 text-dark">Tarification</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-semibold">Prix de Vente <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg shadow-sm rounded-3">
                                    <input type="number" step="0.01" wire:model="prix_vente" class="form-control border-0 bg-light" placeholder="0.00">
                                    <span class="input-group-text border-0 bg-light text-muted">{{ auth()->user()->etablissement->devise_display ?? 'FCFA' }}</span>
                                </div>
                                @error('prix_vente') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted fw-semibold">Prix d'Achat (Optionnel)</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3">
                                    <input type="number" step="0.01" wire:model="prix_achat" class="form-control border-0 bg-light" placeholder="0.00">
                                    <span class="input-group-text border-0 bg-light text-muted">{{ auth()->user()->etablissement->devise_display ?? 'FCFA' }}</span>
                                </div>
                                @error('prix_achat') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted fw-semibold">TVA (%) <span class="text-secondary fw-normal">(Optionnel)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">%</span>
                                    <input type="number" wire:model="tva" class="form-control bg-light border-start-0 ps-0" step="0.01" min="0" max="100" placeholder="Ex: 20">
                                </div>
                                @error('tva') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                            </div>
                            
                            <!-- NOUVEAU : Quantité Minimum -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted fw-semibold">Quantité Min. Commande <span class="text-secondary fw-normal">(Par défaut: 1)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-boxes"></i></span>
                                    <input type="number" wire:model="quantite_minimum" class="form-control bg-light border-start-0 ps-0" min="1" step="1" placeholder="Ex: 5">
                                </div>
                                @error('quantite_minimum') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column : Organization & Options -->
            <div class="col-lg-4">
                
                <!-- CARD 4: ORGANIZATION -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0 text-dark">Organisation</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold d-flex justify-content-between align-items-center">
                                <span>Catégorie <span class="text-danger">*</span></span>
                                <a href="{{ route('categories.index') }}" target="_blank" class="small text-decoration-none text-primary" title="Gérer les catégories"><i class="fas fa-external-link-alt"></i></a>
                            </label>
                            <select wire:model.live="categorie_id" class="form-select form-select-lg bg-light border-0 shadow-sm">
                                <option value="">--- Sélectionner ---</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nom }}</option>
                                @endforeach
                            </select>
                            @error('categorie_id') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- CARD 5: OPTIONS -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0 text-dark">Options Avancées</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Toggle Disponibilité -->
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Visible au Comptoir</h6>
                                <p class="small text-muted mb-0">Rendre cet article disponible à la vente dans le POS.</p>
                            </div>
                            <div class="form-check form-switch form-switch-lg">
                                <input class="form-check-input" type="checkbox" id="flexSwitchDispo" wire:model="disponible" style="width: 3em; height: 1.5em; cursor: pointer;">
                            </div>
                        </div>

                        <!-- Toggle Gestion Stock -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Gestion de Stock</h6>
                                <p class="small text-muted mb-0">Suivre la quantité en inventaire pour cet article.</p>
                            </div>
                            <div class="form-check form-switch form-switch-lg">
                                <input class="form-check-input" type="checkbox" id="flexSwitchStock" wire:model="gestion_stock" style="width: 3em; height: 1.5em; cursor: pointer;">
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            
        </div>
    </form>
    
    <style>
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
            background-color: #fff !important;
            border: 1px solid var(--primary-color) !important;
        }
        .btn-check:checked + .btn-outline-secondary {
            background-color: rgba(var(--primary-color-rgb), 0.1) !important;
            border: 2px solid var(--primary-color) !important;
            color: var(--primary-color) !important;
            box-shadow: inset 0 3px 5px rgba(0,0,0,.05);
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.25) !important;
            border-color: rgba(var(--primary-color-rgb), 0.5) !important;
            background-color: #fff !important;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        /* Upload zone drag styling via CSS */
        .upload-zone:hover {
            background-color: rgba(var(--primary-color-rgb), 0.03) !important;
            border-color: var(--primary-color) !important;
            cursor: pointer;
        }
    </style>
</div>