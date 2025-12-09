<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="pagination-container">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" wire:click="previousPage" wire:loading.attr="disabled" rel="prev"
                            class="page-link" aria-label="@lang('pagination.previous')">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <button type="button" wire:click="gotoPage({{ $page }})" class="page-link"
                                        aria-label="@lang('pagination.goto_page', ['page' => $page])">
                                        {{ $page }}
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button type="button" wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="page-link"
                            aria-label="@lang('pagination.next')">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>

<style>
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .pagination {
        display: flex;
        gap: 0.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
    }

    .page-item {
        display: inline-block;
    }

    .page-link {
        min-width: 40px;
        height: 40px;
        padding: 0.5rem 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.3s ease;
        background: white;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .page-link:hover {
        background: #fdfdfd;
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .page-item.disabled .page-link {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f9fafb;
    }

    .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
        border-color: #e5e7eb;
    }
</style>