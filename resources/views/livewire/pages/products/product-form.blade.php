@section('title', $produit ? 'Modifier le Produit' : 'Nouveau Produit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produits</a></li>
    <li class="breadcrumb-item active">{{ $produit ? 'Modifier' : 'Créer' }}</li>
@endsection

<div class="product-form-container">
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
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nom }}</option>
                            @endforeach
                        </select>
                        @error('categorie_id') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Prix de vente (€)</label>
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
    <style>
        .product-form-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .image-preview {
            width: 100%;
            height: 300px;
            background: #f3f4f6;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            border: 2px dashed #d1d5db;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #9ca3af;
            gap: 0.5rem;
        }

        .placeholder i {
            font-size: 3rem;
        }

        .hidden-input {
            display: none;
        }

        .upload-btn {
            position: absolute;
            bottom: 1rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            background: white;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Switch Toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #ff9f43;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #ff9f43;
        }

        input:checked+.slider:before {
            transform: translateX(24px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #f3f4f6;
        }

        .btn-cancel {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            color: #6b7280;
            font-weight: 600;
            background: #f3f4f6;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-save {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #ff9f43, #ee5253);
            color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 159, 67, 0.3);
        }

        .error-msg {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: block;
        }
    </style>
</div>