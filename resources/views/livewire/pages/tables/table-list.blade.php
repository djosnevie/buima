@section('title', 'Gestion des Tables')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Tables</li>
@endsection

<div class="tables-management">
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

    <div class="header-actions">
        <div class="left-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input wire:model.live="search" type="text" placeholder="Rechercher une table (N°)...">
            </div>
            <div class="filter-group">
                <button wire:click="setFilter('tous')"
                    class="filter-btn {{ $filterStatus === 'tous' ? 'active' : '' }}">Toutes</button>
                <button wire:click="setFilter('libre')"
                    class="filter-btn {{ $filterStatus === 'libre' ? 'active' : '' }}">Libres</button>
                <button wire:click="setFilter('occupee')"
                    class="filter-btn {{ $filterStatus === 'occupee' ? 'active' : '' }}">Occupées</button>
            </div>
        </div>
        <a href="{{ route('tables.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Nouvelle Table
        </a>
    </div>

    <div class="tables-grid">
        @forelse($tables as $table)
            <div class="table-card status-{{ $table->statut }}">
                <div class="table-header">
                    <span class="table-number">T-{{ $table->numero }}</span>
                    <div class="table-actions">
                        <a href="{{ route('tables.edit', $table->id) }}" class="btn-icon edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button wire:click="delete({{ $table->id }})" wire:confirm="Supprimer cette table ?"
                            class="btn-icon delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="table-body">
                    <div class="capacity">
                        <i class="fas fa-users"></i>
                        <span>{{ $table->capacite }} pers.</span>
                    </div>
                    <div class="status-badge">
                        {{ ucfirst($table->statut) }}
                    </div>
                </div>

                <div class="table-footer">
                    @if($table->statut === 'libre')
                        <a href="{{ route('orders.create', ['table' => $table->id]) }}" class="btn-order">
                            <i class="fas fa-shopping-cart"></i> Commander
                        </a>
                    @endif
                    <button wire:click="toggleStatus({{ $table->id }})"
                        class="btn-toggle {{ $table->statut === 'libre' ? 'btn-occupy' : 'btn-free' }}">
                        @if($table->statut === 'libre')
                            <i class="fas fa-lock"></i> Occuper
                        @else
                            <i class="fas fa-unlock"></i> Libérer
                        @endif
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-chair"></i>
                <p>Aucune table configurée</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        {{ $tables->links('livewire.custom-pagination') }}
    </div>
    <!-- Table List Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/pages/tables/table-list.css') }}">

</div>