@section('title', 'Gestion des Fournisseurs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Fournisseurs</li>
@endsection

<div class="suppliers-management">
    <style>
        .btn-add {
            background: var(--primary-color) !important;
            border: none !important;
        }

        .btn-add:hover {
            opacity: 0.9;
        }

        .supplier-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .supplier-card:hover {
            transform: translateY(-5px);
        }

        .supplier-info h4 {
            margin-top: 0.5rem;
            font-weight: 700;
            color: #333;
        }

        .supplier-meta {
            color: #666;
            font-size: 0.9rem;
        }

        .supplier-meta i {
            width: 20px;
            color: var(--primary-color);
        }

        .card-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .btn-icon.edit {
            background: rgba(255, 159, 67, 0.1);
            color: var(--primary-color);
        }

        .btn-icon.delete {
            background: #fceaea;
            color: #ea5455;
        }
    </style>

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="header-actions d-flex justify-content-between align-items-center mb-4">
        <div class="search-box position-relative" style="width: 300px;">
            <i class="fas fa-search position-absolute" style="left: 10px; top: 12px; color: #999;"></i>
            <input wire:model.live="search" type="text" class="form-control ps-5"
                placeholder="Rechercher un fournisseur...">
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-add">
                <i class="fas fa-plus"></i> Nouveau Fournisseur
            </a>
        @endif
    </div>

    <div class="row g-4">
        @forelse($suppliers as $supplier)
            <div class="col-md-4">
                <div class="supplier-card">
                    <div class="supplier-info">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar bg-light-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px; background: rgba(191, 58, 41, 0.1); color: var(--primary-color);">
                                <i class="fas fa-truck"></i>
                            </div>
                            <span class="text-muted small">#{{ $supplier->id }}</span>
                        </div>
                        <h4>{{ $supplier->nom }}</h4>
                        <div class="supplier-meta mt-3">
                            <p class="mb-1"><i class="fas fa-user-tie"></i> {{ $supplier->contact ?: 'N/A' }}</p>
                            <p class="mb-1"><i class="fas fa-phone"></i> {{ $supplier->telephone ?: 'N/A' }}</p>
                            <p class="mb-1"><i class="fas fa-envelope"></i> {{ $supplier->email ?: 'N/A' }}</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt"></i>
                                {{ Str::limit($supplier->adresse, 40) ?: 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="card-actions">
                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn-icon edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button wire:click="delete({{ $supplier->id }})" wire:confirm="Supprimer ce fournisseur ?"
                                class="btn-icon delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun fournisseur trouvé</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="pagination-container mt-4">
        {{ $suppliers->links('livewire.custom-pagination') }}
    </div>
</div>