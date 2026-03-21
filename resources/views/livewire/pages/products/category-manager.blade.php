<div class="category-manager-container w-100">
    <style>
        .color-picker-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .color-input {
            width: 40px;
            height: 40px;
            padding: 0;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            overflow: hidden;
            background: none;
        }
        .color-input::-webkit-color-swatch-wrapper {
            padding: 0;
        }
        .color-input::-webkit-color-swatch {
            border: none;
            border-radius: 6px;
        }
        .badge-entree { background-color: #10B981; color: white;}
        .badge-plat { background-color: #3B82F6; color: white;}
        .badge-dessert { background-color: #EC4899; color: white;}
        .badge-boisson { background-color: #6366F1; color: white;}
        .badge-autre { background-color: #6b7280; color: white;}
        .badge-accompagnement { background-color: #f59e0b; color: white;}
        
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0 fw-bold text-dark">Gérer les Catégories</h2>
            <p class="text-muted m-0">Créez, modifiez et organisez vos catégories d'articles.</p>
        </div>
        @if(!request()->routeIs('categories.index'))
            <button wire:click="$parent.toggleCategoryManager" class="btn btn-light border shadow-sm">
                <i class="fas fa-times me-1"></i> Fermer
            </button>
        @endif
    </div>

    <div class="row g-4">
        <!-- Form Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <i class="fas {{ $editingId ? 'fa-pen' : 'fa-plus-circle' }} text-primary me-2"></i> 
                            {{ $editingId ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
                        @else
                            <i class="fas fa-lock text-secondary me-2"></i> Accès restreint
                        @endif
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                        
                        @if (session()->has('message'))
                            <div class="alert alert-success shadow-sm border-0 py-2 px-3 small rounded-3 mb-3">
                                <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger shadow-sm border-0 py-2 px-3 small rounded-3 mb-3">
                                <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label class="form-label text-muted fw-semibold">Nom de la catégorie</label>
                                <input type="text" wire:model="nom" class="form-control bg-light border-0" placeholder="Ex: Entrées, Boissons chades...">
                                @error('nom') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted fw-semibold">Type par défaut</label>
                                <select wire:model="type" class="form-select bg-light border-0">
                                    <option value="entree">Entrée</option>
                                    <option value="plat">Plat Principal</option>
                                    <option value="dessert">Dessert</option>
                                    <option value="boisson">Boisson</option>
                                    <option value="accompagnement">Accompagnement</option>
                                    <option value="autre">Autre</option>
                                </select>
                                @error('type') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted fw-semibold">Couleur distinctive</label>
                                <div class="color-picker-wrapper gap-3 bg-light p-2 rounded-3">
                                    <input type="color" wire:model="couleur" class="color-input">
                                    <span class="text-dark fw-bold text-uppercase" style="letter-spacing: 1px;">{{ $couleur }}</span>
                                </div>
                                @error('couleur') <div class="text-danger mt-1 small"><i class="fas fa-info-circle"></i> {{ $message }}</div> @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary shadow-sm fw-bold" style="background-color: var(--primary-color); border: none; padding: 12px;">
                                    {{ $editingId ? 'Mettre à jour' : 'Créer la catégorie' }}
                                </button>
                                @if($editingId)
                                    <button type="button" wire:click="resetForm" class="btn btn-light border text-muted">Annuler la modification</button>
                                @endif
                            </div>
                        </form>
                    @else
                        <div class="alert alert-secondary border-0 bg-light text-center py-4">
                            <i class="fas fa-shield-alt fs-2 text-muted mb-2 opacity-50"></i>
                            <p class="mb-0 small">Vous n'avez pas les droits nécessaires pour gérer les catégories d'articles.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Répertoire des catégories</h5>
                    
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                        <div class="d-flex gap-2">
                            <input type="file" id="importCategory" wire:model.live="importFile" style="display:none;" accept=".xlsx,.xls,.csv">
                            <label for="importCategory" class="btn btn-sm btn-light border shadow-sm mb-0 d-flex align-items-center" style="cursor: pointer;" title="Importer Excel">
                                <i class="fas fa-file-import text-secondary me-1"></i> Importer
                            </label>
                            
                            <div wire:loading wire:target="importFile" class="spinner-border spinner-border-sm text-primary ms-2 align-self-center" role="status">
                                <span class="visually-hidden">...</span>
                            </div>

                            <button wire:click="exportExcel" class="btn btn-sm btn-success border-0 shadow-sm d-flex align-items-center" style="background-color: #217346;" title="Exporter Excel">
                                <i class="fas fa-file-excel me-1"></i> Exporter
                            </button>
                        </div>
                    @endif
                </div>
                
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 600px; overflow-y: auto;">
                        @forelse($categories as $category)
                            <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-bottom-0 border-top" style="transition: all 0.2s;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle shadow-sm" style="width: 16px; height: 16px; background-color: {{ $category->couleur }};"></div>
                                    <div class="fw-bold text-dark" style="font-size: 1.1rem;">{{ $category->nom }}</div>
                                    <span class="badge badge-{{ $category->type ?? 'autre' }} rounded-pill px-3 py-1 fw-normal" style="font-size: 0.8rem;">
                                        {{ ucfirst($category->type ?? 'Autre') }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                        <button wire:click="edit({{ $category->id }})" class="btn btn-sm btn-light border text-primary rounded-3" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="delete({{ $category->id }})" wire:confirm="Supprimer cette catégorie définitivement ?" class="btn btn-sm btn-light border text-danger rounded-3" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open text-muted opacity-25" style="font-size: 4rem;"></i>
                                <p class="mt-3 text-muted">Aucune catégorie trouvée.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>