<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Super Admin Dashboard</h2>
                <p class="text-muted">Vue d'ensemble du système O'Menu</p>
            </div>
            <a href="{{ route('setup.restaurant') }}" class="btn btn-primary text-white fw-bold">
                <i class="fas fa-plus-circle me-2"></i>Nouveau Restaurant
            </a>
        </div>
    </div>

    <!-- Global Stats Overview -->
    <div class="row mb-4">
        <!-- New Restaurants (Growth) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted small fw-bold text-uppercase">Croissance (30j)</div>
                        <div class="icon-circle bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">+{{ $newRestaurantsCount }}</h3>
                    <div class="mt-2 text-muted small">Nouveaux restaurants</div>
                </div>
            </div>
        </div>

        <!-- Top Performing Restaurant -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted small fw-bold text-uppercase">Top Restaurant</div>
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark text-truncate" style="font-size: 1.5rem;">
                        {{ $topRestaurant ? $topRestaurant->nom : 'N/A' }}
                    </h3>
                    <div class="mt-2 text-muted small">
                        {{ $topRestaurant ? $topRestaurant->commandes_count . ' commandes' : 'Aucune donnée' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Restaurants -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted small fw-bold text-uppercase">Restaurants Actifs</div>
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-store"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalRestaurants }}</h3>
                    <div class="mt-2 text-muted small">Sur la plateforme</div>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted small fw-bold text-uppercase">Utilisateurs</div>
                        <div class="icon-circle bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalUsers }}</h3>
                    <div class="mt-2 text-muted small">Comptes actifs</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Liste des Restaurants</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">NOM</th>
                                    <th class="py-3 text-muted small fw-bold">TYPE</th>
                                    <th class="py-3 text-muted small fw-bold">ADMIN / GÉRANT</th>
                                    <th class="py-3 text-muted small fw-bold">UTILISATEURS</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($etablissements as $etablissement)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                @if($etablissement->logo)
                                                    <img src="{{ asset('storage/' . $etablissement->logo) }}"
                                                        class="rounded-circle me-3"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="avatar-initial rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center me-3"
                                                        style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($etablissement->nom, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $etablissement->nom }}</div>
                                                    <div class="small text-muted">
                                                        {{ $etablissement->email ?? 'Pas d\'email' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark border">{{ ucfirst($etablissement->type) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $admin = $etablissement->users->where('role', 'admin')->first();
                                            @endphp
                                            @if($admin)
                                                {{ $admin->name }} <br>
                                                <small class="text-muted">{{ $admin->email }}</small>
                                            @else
                                                <span class="text-danger small">Non assigné</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $etablissement->users->count() }}
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button wire:click="edit({{ $etablissement->id }})"
                                                class="btn btn-sm btn-link text-primary me-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="deleteRestaurant({{ $etablissement->id }})"
                                                class="btn btn-sm btn-link text-danger"
                                                onclick="confirm('ATTENTION: Cela supprimera tout le restaurant et ses données. Êtes-vous sûr ?') || event.stopImmediatePropagation()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <p class="mb-0">Aucun restaurant trouvé.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Edit Modal -->
                    @if($isOpen)
                        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);"
                            aria-modal="true" role="dialog">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Modifier le Restaurant</h5>
                                        <button type="button" class="btn-close" wire:click="resetForm"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <form wire:submit.prevent="update">
                                            <div class="mb-4">
                                                <label class="form-label text-muted small fw-bold">Nom du Restaurant</label>
                                                <input type="text" class="form-control" wire:model="nom">
                                                @error('nom') <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small fw-bold">Type</label>
                                                    <select class="form-select" wire:model="type">
                                                        <option value="mixte">Mixte</option>
                                                        <option value="avec_tables">Sur Place</option>
                                                        <option value="sans_tables">Emporter</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small fw-bold">Devise</label>
                                                    <select class="form-select" wire:model="devise">
                                                        <option value="XAF">FCFA (XAF)</option>
                                                        <option value="EUR">Euro (€)</option>
                                                        <option value="USD">Dollar ($)</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="row g-3 mb-4">
                                                <div class="col-md-4">
                                                    <label class="form-label text-muted small fw-bold">Couleur
                                                        Principale</label>
                                                    <input type="color" class="form-control form-control-color w-100"
                                                        wire:model="theme_color" title="Couleur Principale">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label text-muted small fw-bold">Couleur
                                                        Secondaire</label>
                                                    <input type="color" class="form-control form-control-color w-100"
                                                        wire:model="secondary_color" title="Couleur Secondaire">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label text-muted small fw-bold">Couleur
                                                        Boutons</label>
                                                    <input type="color" class="form-control form-control-color w-100"
                                                        wire:model="button_color" title="Couleur Boutons">
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small fw-bold">Email</label>
                                                    <input type="email" class="form-control" wire:model="email">
                                                    @error('email') <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small fw-bold">Téléphone</label>
                                                    <input type="text" class="form-control" wire:model="telephone">
                                                    @error('telephone') <span
                                                        class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label text-muted small fw-bold">Adresse</label>
                                                <textarea class="form-control" wire:model="adresse" rows="2"></textarea>
                                                @error('adresse') <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="d-grid gap-2 mt-4">
                                                <button type="submit" class="btn btn-primary text-white py-2 fw-bold"
                                                    style="background-color: {{ $button_color ?? '#ff6b35' }}; border-color: {{ $button_color ?? '#ff6b35' }};">Mettre
                                                    à jour</button>
                                                <button type="button" class="btn btn-light text-muted"
                                                    wire:click="resetForm">Annuler</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>