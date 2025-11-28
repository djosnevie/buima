@section('title', $table ? 'Modifier la Table' : 'Nouvelle Table')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tables.index') }}">Tables</a></li>
    <li class="breadcrumb-item active">{{ $table ? 'Modifier' : 'Créer' }}</li>
@endsection

<div class="table-form-container">
    <div class="form-card">
        <form wire:submit="save">
            <div class="form-group">
                <label>Numéro de la table</label>
                <input type="text" wire:model="numero" class="form-control" placeholder="Ex: 12, A1, Terrasse-1">
                @error('numero') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Capacité (personnes)</label>
                <input type="number" wire:model="capacite" class="form-control" min="1">
                @error('capacite') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Statut initial</label>
                <select wire:model="statut" class="form-control">
                    <option value="libre">Libre</option>
                    <option value="occupee">Occupée</option>
                    <option value="reservee">Réservée</option>
                </select>
                @error('statut') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('tables.index') }}" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
    <style>
        .table-form-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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