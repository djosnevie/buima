<div>
    @if($isOpen)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5); z-index: 1055;"
            aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Personnel -
                            {{ $etablissementName ?? (auth()->user()->etablissement->nom ?? 'Restaurant') }}
                            <span
                                class="badge bg-primary-subtle text-primary rounded-pill ms-2 fs-6">{{ count($employees) }}</span>
                        </h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <div class="modal-body p-4">
                        @if(count($employees) > 0)
                            <div class="list-group list-group-flush">
                                @foreach($employees as $employee)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom-0 border-top">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initial rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $employee->name }}</div>
                                                <div class="small text-muted">{{ $employee->email }}</div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge {{ $employee->role === 'admin' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-info-subtle text-info-emphasis' }} rounded-pill px-3 mb-1">
                                                {{ $employee->role === 'admin' ? 'Gérant' : 'Employé' }}
                                            </span>
                                            @if($employee->section)
                                                <div class="small text-muted">{{ $employee->section->nom }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-2x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-0">Aucun employé trouvé pour cet établissement.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" wire:click="close">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>