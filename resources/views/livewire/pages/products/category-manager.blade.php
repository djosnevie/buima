<div class="category-manager">
    <div class="manager-header">
        <h3>Gérer les Catégories</h3>
        <button wire:click="$parent.toggleCategoryManager" class="btn-close-modal">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="manager-content">
        <!-- Form Section -->
        <div class="category-form">
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
                            <button wire:click="edit({{ $category->id }})" class="btn-icon edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="delete({{ $category->id }})" wire:confirm="Supprimer cette catégorie ?"
                                class="btn-icon delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .category-manager {
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .manager-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .manager-header h3 {
            margin: 0;
            font-size: 1.25rem;
            color: #1f2937;
        }

        .btn-close-modal {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #6b7280;
            cursor: pointer;
            padding: 0.5rem;
            transition: color 0.3s;
        }

        .btn-close-modal:hover {
            color: #ef4444;
        }

        .manager-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .category-form {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .category-form h4,
        .category-list h4 {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1rem;
            color: #4b5563;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #374151;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .color-picker {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .color-input {
            width: 50px;
            height: 30px;
            padding: 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .btn-save,
        .btn-cancel {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
        }

        .btn-save {
            background: #ff9f43;
            color: white;
        }

        .btn-cancel {
            background: #e5e7eb;
            color: #374151;
        }

        .list-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .category-item:hover {
            border-color: #ff9f43;
            transform: translateX(2px);
        }

        .category-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .color-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .category-name {
            font-weight: 500;
            color: #1f2937;
        }

        .category-type {
            font-size: 0.75rem;
            padding: 0.125rem 0.5rem;
            border-radius: 12px;
            background: #f3f4f6;
            color: #6b7280;
        }

        .category-actions {
            display: flex;
            gap: 0.25rem;
        }

        .alert {
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</div>