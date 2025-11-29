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
    <!-- Table Form Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/pages/tables/table-form.css') }}">

</div>