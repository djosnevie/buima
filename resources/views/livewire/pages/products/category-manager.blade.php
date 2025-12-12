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
            <h4>Liste des catégories</h4>
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