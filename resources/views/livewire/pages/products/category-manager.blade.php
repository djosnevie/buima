<div class="category-manager">
    <style>
        /* Dynamic Theme Overrides for Category Manager */
        .btn-save {
            background: var(--primary-color) !important;
            border: none !important;
        }

        .btn-save:hover {
            opacity: 0.9;
        }

        .btn-icon.edit:hover {
            color: var(--primary-color) !important;
        }

        .color-input:checked {
            border-color: var(--primary-color) !important;
        }

        .form-control:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.15) !important;
        }
    </style>
    <div class="manager-header">
        <h3>Gérer les Catégories</h3>
        <button wire:click="$parent.toggleCategoryManager" class="btn-close-modal">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="manager-content">
        <!-- Form Section -->
        <div class="category-form">
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                <h4>{{ $editingId ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}</h4>

                @if (session()->has('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form wire:submit.prevent="save">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" wire:model="nom" class="form-control" placeholder="Ex: Entrées">
                        @error('nom') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Type</label>
                        <select wire:model="type" class="form-control">
                            <option value="entree">Entrée</option>
                            <option value="plat">Plat</option>
                            <option value="dessert">Dessert</option>
                            <option value="boisson">Boisson</option>
                        </select>
                        @error('type') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Couleur</label>
                        <div class="color-picker">
                            <input type="color" wire:model="couleur" class="color-input">
                            <span class="color-code">{{ $couleur }}</span>
                        </div>
                        @error('couleur') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-actions">
                        @if($editingId)
                            <button type="button" wire:click="resetForm" class="btn-cancel">Annuler</button>
                        @endif
                        <button type="submit" class="btn-save">
                            {{ $editingId ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Vous n'avez pas les droits pour modifier les catégories.
                </div>
            @endif
        </div>

        <!-- List Section -->
        <div class="category-list">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h4 style="margin: 0;">Liste des catégories</h4>
                @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="file" id="importCategory" wire:model.live="importFile" style="display:none;" accept=".xlsx,.xls,.csv">
                        <label for="importCategory" class="btn-secondary" style="background-color: #2b5797; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem; cursor: pointer; margin-bottom: 0;">
                            <i class="fas fa-file-import"></i> Importer
                        </label>
                        <div wire:loading wire:target="importFile" class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">...</span>
                        </div>

                        <button wire:click="exportExcel" class="btn-secondary" style="background-color: #217346; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem;">
                            <i class="fas fa-file-excel"></i> Exporter
                        </button>
                    </div>
                @endif
            </div>
            <div class="list-container">
                @foreach($categories as $category)
                    <div class="category-item">
                        <div class="category-info">
                            <span class="color-dot" style="background-color: {{ $category->couleur }}"></span>
                            <span class="category-name">{{ $category->nom }}</span>
                            <span class="category-type badge-{{ $category->type }}">{{ ucfirst($category->type) }}</span>
                        </div>
                        <div class="category-actions">
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <button wire:click="edit({{ $category->id }})" class="btn-icon edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="delete({{ $category->id }})" wire:confirm="Supprimer cette catégorie ?"
                                    class="btn-icon delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Category Manager Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/pages/products/category-manager.css') }}">

</div>