<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Gestion du Personnel</h2>
                <p class="text-muted">Gérez vos employés et leurs accès aux sections.</p>
            </div>
            <button class="btn btn-primary text-white" wire:click="create">
                <i class="fas fa-user-plus me-2"></i>Nouvel Utilisateur
            </button>
        </div>
    </div>

    <!-- Feedback Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">NOM</th>
                                    @if(auth()->user()->isSuperAdmin())
                                        <th class="py-3 text-muted small fw-bold">ÉTABLISSEMENT</th>
                                    @endif
                                    <th class="py-3 text-muted small fw-bold">RÔLE</th>
                                    <th class="py-3 text-muted small fw-bold">SECTION ASSIGNÉE</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initial rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center me-3"
                                                    style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    <div class="small text-muted">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        @if(auth()->user()->isSuperAdmin())
                                            <td>
                                                @if($user->etablissement)
                                                    <span class="fw-bold text-dark">{{ $user->etablissement->nom }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-3">Global
                                                        / Super Admin</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if($user->role === 'admin')
                                                <span
                                                    class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3">Gérant
                                                    / Admin</span>
                                            @else
                                                <span
                                                    class="badge bg-info-subtle text-info-emphasis rounded-pill px-3">Employé</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->section)
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ $user->section->nom }}</span>
                                            @elseif($user->role === 'admin')
                                                <span class="text-muted small">Accès Global</span>
                                            @else
                                                <span class="text-muted small">Aucune (Accès limité)</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button wire:click="edit({{ $user->id }})"
                                                class="btn btn-sm btn-link text-primary me-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="delete({{ $user->id }})"
                                                class="btn btn-sm btn-link text-danger"
                                                onclick="confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?') || event.stopImmediatePropagation()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-users fa-2x mb-3 text-secondary opacity-50"></i>
                                            <p class="mb-0">Aucun autre utilisateur trouvé.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($isOpen)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true"
            role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">{{ $isEditing ? 'Modifier Utilisateur' : 'Nouvel Utilisateur' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="resetForm"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Nom Complet</label>
                                <input type="text" class="form-control" wire:model="name" placeholder="John Doe">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Email</label>
                                <input type="email" class="form-control" wire:model="email" placeholder="john@example.com">
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Mot de passe
                                    {{ $isEditing ? '(Laisser vide pour ne pas changer)' : '' }}</label>
                                <input type="password" class="form-control" wire:model="password">
                                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Rôle</label>
                                    <select class="form-select" wire:model="role">
                                        <option value="user">Employé</option>
                                        <option value="admin">Gérant / Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Section (Zone)</label>
                                    <select class="form-select" wire:model="section_id">
                                        <option value="">Aucune (ou Accès Global)</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->nom }}
                                                @if(auth()->user()->isSuperAdmin())
                                                    ({{ $section->etablissement->nom ?? 'N/A' }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if(auth()->user()->isSuperAdmin())
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Établissement</label>
                                    <select class="form-select" wire:model="etablissement_id">
                                        <option value="">Aucun (Global / Super Admin)</option>
                                        @foreach($etablissements as $etablissement)
                                            <option value="{{ $etablissement->id }}">{{ $etablissement->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary text-white py-2 fw-bold">Enregistrer</button>
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