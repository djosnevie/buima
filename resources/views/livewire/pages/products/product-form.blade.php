@section('title', $produit ? 'Modifier le Produit' : 'Nouveau Produit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produits</a></li>
    <li class="breadcrumb-item active">{{ $produit ? 'Modifier' : 'Créer' }}</li>
@endsection

<div class="product-form-container">
    <style>
        /* Dynamic Theme Overrides for Product Form */
        .btn-save {
            background: var(--primary-color) !important;
            border: none !important;
        }

        .btn-save:hover {
            opacity: 0.9;
        }

        .upload-btn {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .upload-btn:hover {
            background: rgba(var(--primary-color-rgb), 0.05) !important;
        }

        .switch input:checked+.slider {
            background-color: var(--primary-color) !important;
        }

        .form-control:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.15) !important;
        }
    </style>
    <div class="form-card">
        <form wire:submit="save">
            <div class="form-grid">
                <!-- Left Column: Image -->
                <div class="image-column">
                    <div class="image-preview">
                        @if($newImage)
                            <img src="{{ $newImage->temporaryUrl() }}">
                        @elseif($image)
                            <img src="{{ asset('storage/' . $image) }}">
                        @else
                            <div class="placeholder">
                                <i class="fas fa-camera"></i>
                                <span>Ajouter une photo</span>
                            </div>
                        @endif

                        <input type="file" wire:model="newImage" id="image-upload" class="hidden-input">
                        <label for="image-upload" class="upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i> Changer l'image
                        </label>
                    </div>
                    @error('newImage') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <!-- Right Column: Details -->
                <div class="details-column">
                    <div class="form-group">
                        <label>Nom du produit</label>
                        <input type="text" wire:model="nom" class="form-control" placeholder="Ex: Burger Classic">
                        @error('nom') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Catégorie</label>
                        <select wire:model="categorie_id" class="form-control">
                            <option value="">Aucune catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nom }}</option>
                            @endforeach
                        </select>
                        @error('categorie_id') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Prix de vente (CFA)</label>
                        <input type="number" step="0.01" wire:model="prix_vente" class="form-control"
                            placeholder="0.00">
                        @error('prix_vente') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea wire:model="description" class="form-control" rows="3"
                            placeholder="Description du produit..."></textarea>
                        @error('description') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="switch">
                            <input type="checkbox" wire:model="disponible">
                            <span class="slider round"></span>
                        </label>
                        <span>Disponible à la vente</span>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('products.index') }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Product Form Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/pages/products/product-form.css') }}">

</div>