@section('title', 'Gestion des Caisses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Caisses</li>
@endsection

<div class="caisse-management">
    <div class="row g-4">
        {{-- Formulaire visible uniquement pour les admins --}}
        @if(auth()->user()->isAdmin())
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">{{ $editingCaisseId ? 'Modifier la Caisse' : 'Ajouter une Caisse' }}</h5>
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom de la caisse</label>
                            <input type="text" wire:model="nom" class="form-control"
                                placeholder="Ex: Caisse Principale">
                            @error('nom') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Code / Identifiant</label>
                            <input type="text" wire:model="code" class="form-control" placeholder="Ex: CAISSE-01">
                            @error('code') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100"
                            style="background: var(--primary-color); border: none;">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                        @if($editingCaisseId)
                            <button type="button" wire:click="$reset(['nom', 'code', 'editingCaisseId'])"
                                class="btn btn-light w-100 mt-2">Annuler</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Code</th>
                                    <th class="px-4 py-3">Nom</th>
                                    <th class="px-4 py-3">Statut Session</th>
                                    <th class="px-4 py-3">État</th>
                                    <th class="px-4 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($caisses as $caisse)
                                    @php $currentSession = $caisse->currentSession(); @endphp
                                    <tr>
                                        <td class="px-4 py-3 fw-bold">{{ $caisse->code }}</td>
                                        <td class="px-4 py-3">{{ $caisse->nom }}</td>
                                        <td class="px-4 py-3">
                                            @if($currentSession)
                                                <span class="badge bg-success">Ouverte
                                                    ({{ $currentSession->user->name }})</span>
                                            @else
                                                <span class="badge bg-secondary">Fermée</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="form-check form-switch">
                                                @if(auth()->user()->isAdmin())
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        wire:click="toggle({{ $caisse->id }})" {{ $caisse->active ? 'checked' : '' }}>
                                                @else
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        {{ $caisse->active ? 'checked' : '' }} disabled>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            @if(auth()->user()->isAdmin())
                                                <button wire:click="edit({{ $caisse->id }})"
                                                    class="btn btn-sm btn-light border"><i class="fas fa-edit"></i></button>
                                            @endif
                                            <a href="{{ route('caisses.sessions', $caisse->id) }}"
                                                class="btn btn-sm btn-light border"><i class="fas fa-history"></i>
                                                Sessions</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>