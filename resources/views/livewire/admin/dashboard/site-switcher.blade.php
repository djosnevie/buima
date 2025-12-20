<div class="d-inline-block">
    <div class="dropdown">
        <button class="btn btn-light bg-white border shadow-sm dropdown-toggle d-flex align-items-center gap-2"
            type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-filter text-muted"></i>
            <span class="fw-medium">
                @if($currentSiteId)
                    {{ $sites->firstWhere('id', $currentSiteId)->nom ?? 'Site Inconnu' }}
                @else
                    Vue Globale
                @endif
            </span>
        </button>
        <ul class="dropdown-menu shadow-sm border-0 mt-2">
            <li>
                <a class="dropdown-item d-flex align-items-center justify-content-between {{ is_null($currentSiteId) ? 'active bg-light text-primary fw-bold' : '' }}"
                    href="#" wire:click.prevent="switchSite('global')">
                    <span><i class="fas fa-globe me-2 text-muted"></i>Vue Globale</span>
                    @if(is_null($currentSiteId)) <i class="fas fa-check small"></i> @endif
                </a>
            </li>
            <li>
                <hr class="dropdown-divider my-1">
            </li>
            @foreach($sites as $site)
                <li>
                    <a class="dropdown-item d-flex align-items-center justify-content-between {{ $currentSiteId == $site->id ? 'active bg-light text-primary fw-bold' : '' }}"
                        href="#" wire:click.prevent="switchSite({{ $site->id }})">
                        <span><i class="fas fa-store me-2 text-muted"></i>{{ $site->nom }}</span>
                        @if($currentSiteId == $site->id) <i class="fas fa-check small"></i> @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>