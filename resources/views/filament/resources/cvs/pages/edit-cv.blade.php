<x-filament-panels::page>
    @if($reviewId)
        <div style="position: fixed; top: 0; right: 0; bottom: 0; width: 24rem; background-color: #111827; box-shadow: -4px 0 24px rgba(0,0,0,0.3); z-index: 9999; border-left: 1px solid #374151; overflow-y: auto;">
            @livewire('cv-review-sidebar', ['reviewId' => $reviewId], key('sidebar-'.$reviewId))
        </div>
    @endif
    
    <div class="{{ $reviewId ? 'mr-96' : '' }}">
        {{ $this->content }}
    </div>
</x-filament-panels::page>
