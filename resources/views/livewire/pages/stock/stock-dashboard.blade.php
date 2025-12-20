@section('title', 'Gestion des Stocks')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Stocks</li>
@endsection

<div class="stock-management">
    <style>
        .nav-tabs-custom { border-bottom: 2px solid #f0f0f0; margin-bottom: 2rem; }
        .nav-tabs-custom .nav-link { border: none; color: #666; font-weight: 500; padding: 1rem 1.5rem; position: relative; }
        .nav-tabs-custom .nav-link.active { color: var(--primary-color); background: transparent; }
        .nav-tabs-custom .nav-link.active::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 100%; height: 2px; background: var(--primary-color); }
        .stat-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .badge-alert { background: #fee2e2; color: #ef4444; }
        .stock-table thead { background: #f9fafb; }
        .stock-table th { font-weight: 600; color: #374151; padding: 1rem; }
        .stock-table td { vertical-align: middle; padding: 1rem; }
        .progress { height: 8px; border-radius: 4px; }
    </style>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="row g-4">
                 <!-- Rupture -->
                <div class="col-md-6">
                    <div class="stat-card d-flex align-items-center gap-3 border border-danger border-opacity-25" style="background: #fff5f5;">
                        <div class="icon-box p-3 rounded-circle bg-danger text-white">
                            <i class="fas fa-ban fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="h2 fw-bold mb-0 text-danger">{{ $stockOutCount }}</h3>
                            <p class="text-danger small fw-bold mb-0">Rupture de Stock</p>
                        </div>
                    </div>
                </div>
                 <!-- Faible -->
                <div class="col-md-6">
                    <div class="stat-card d-flex align-items-center gap-3 border border-warning border-opacity-25" style="background: #fffbeb;">
                        <div class="icon-box p-3 rounded-circle bg-warning text-white">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="h2 fw-bold mb-0 text-warning">{{ $stockLowCount }}</h3>
                            <p class="text-warning small fw-bold mb-0">Stock Faible</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
             <div class="stat-card h-100">
                <h5 class="fw-bold mb-3"><i class="fas fa-history me-2"></i>Mouvements récents</h5>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @foreach($recentMovements as $move)
                                <tr>
                                    <td><span class="badge {{ $move->type === 'entree' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($move->type) }}</span></td>
                                    <td class="fw-bold">{{ $move->stockable->nom ?? 'Produit supprimé' }}</td>
                                    <td class="text-center">{{ $move->quantite }}</td>
                                    <td class="text-muted small text-end">{{ $move->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
             </div>
        </div>
    </div>

    <ul class="nav nav-tabs nav-tabs-custom">
        <li class="nav-item">
            <button wire:click="setTab('produits')" class="nav-link {{ $activeTab === 'produits' ? 'active' : '' }}">
                <i class="fas fa-hamburger me-2"></i>Produits Finis
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="setTab('ingredients')" class="nav-link {{ $activeTab === 'ingredients' ? 'active' : '' }}">
                <i class="fas fa-carrot me-2"></i>Ingrédients / Matières
            </button>
        </li>
    </ul>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="search-box position-relative" style="width: 300px;">
                    <i class="fas fa-search position-absolute" style="left: 10px; top: 12px; color: #999;"></i>
                    <input wire:model.live="search" type="text" class="form-control ps-5" placeholder="Rechercher...">
                </div>
                <div class="actions">
                     <button wire:click="exportStock" class="btn btn-outline-primary me-2"><i class="fas fa-file-export me-1"></i> Export</button>
                     @if($activeTab === 'ingredients')
                        <button wire:click="openIngredientModal" class="btn btn-primary" style="background: var(--primary-color); border: none;"><i class="fas fa-plus me-1"></i> Nouvel Ingrédient</button>
                     @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table stock-table mb-0">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th class="text-center">Stock Actuel</th>
                            <th class="text-center">Seuil Alerte</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            @php
                                $stock = $activeTab === 'produits' ? ($item->stock->quantite ?? 0) : $item->stock_actuel;
                                $seuil = $activeTab === 'produits' ? ($item->stock->seuil_alerte ?? 0) : $item->seuil_alerte;
                                $isLow = $stock <= $seuil;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($activeTab === 'produits')
                                            <img src="{{ $item->image_url }}" 
                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($item->nom) }}&background=f8f9fa&color=bf3a29';"
                                                 class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light p-2 rounded text-center" style="width: 40px;"><i class="fas {{ $activeTab === 'produits' ? 'fa-hamburger' : 'fa-seedling' }}"></i></div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $item->nom }}</div>
                                            <div class="text-muted small">{{ $activeTab === 'produits' ? ($item->categorie->nom ?? 'Sans catégorie') : ($item->unite ?: 'unité') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center h5 fw-bold {{ $isLow ? 'text-danger' : 'text-success' }}">
                                    {{ $stock }}
                                </td>
                                <td class="text-center text-muted">
                                    {{ $seuil }}
                                </td>
                                <td>
                                    @if($isLow)
                                        <span class="badge badge-alert">Stock Faible</span>
                                    @else
                                        <span class="badge bg-light-success" style="background: #ecfdf5; color: #059669;">Optimal</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($activeTab === 'ingredients')
                                        <button wire:click="openIngredientModal({{ $item->id }})" class="btn btn-sm btn-light border" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    <button wire:click="$dispatch('openAdjustmentModal', { type: '{{ $activeTab === 'produits' ? 'produit' : 'ingredient' }}', id: {{ $item->id }} })" 
                                            class="btn btn-sm btn-light border" title="Ajuster Stock">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <button wire:click="showHistory('{{ $activeTab === 'produits' ? 'produit' : 'ingredient' }}', {{ $item->id }})" 
                                            class="btn btn-sm btn-light border" title="Historique"><i class="fas fa-history"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Aucun résultat trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">
                {{ $items->links('livewire.custom-pagination') }}
            </div>
        </div>
    </div>

    <!-- History Modal -->
    @if($showHistoryModal)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg mt-5">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Historique : {{ $historyItem->nom }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showHistoryModal', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Avant</th>
                                    <th>Mouvement</th>
                                    <th>Après</th>
                                    <th>Motif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($itemMovements as $move)
                                <tr>
                                    <td>{{ $move->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $move->type === 'entree' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($move->type) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $move->quantite_avant }}</td>
                                    <td class="fw-bold {{ $move->type === 'entree' ? 'text-success' : 'text-danger' }}">
                                        {{ $move->type === 'entree' ? '+' : '-' }}{{ $move->quantite }}
                                    </td>
                                    <td class="fw-bold">{{ $move->quantite_apres }}</td>
                                    <td class="small">{{ $move->commentaire }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Ingredient Modal -->
    @if($showIngredientModal)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg mt-5">
                <form wire:submit.prevent="saveIngredient">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">{{ $ingredientId ? 'Modifier' : 'Nouvel' }} Ingrédient</h5>
                        <button type="button" class="btn-close" wire:click="$set('showIngredientModal', false)"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom</label>
                            <input type="text" wire:model="nom" class="form-control" placeholder="Ex: Viande hachée">
                            @error('nom') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Unité</label>
                                <input type="text" wire:model="unite" class="form-control" placeholder="kg, L, pièce">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Seuil Alerte</label>
                                <input type="number" wire:model="seuil_alerte" class="form-control">
                            </div>
                        </div>
                        @if(!$ingredientId)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Stock Initial</label>
                            <input type="number" wire:model="stock_actuel" class="form-control">
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" wire:click="$set('showIngredientModal', false)">Annuler</button>
                        <button type="submit" class="btn btn-primary" style="background: var(--primary-color)">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @livewire('stock.stock-adjustment')
</div>
