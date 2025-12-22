{{-- Section Card Component --}}
{{-- Usage: <x-section-card icon="fa-list" title="Data List" badge="25 items"> Content </x-section-card> --}}

@props([
    'icon' => null,
    'iconColor' => 'primary',
    'title' => 'Section Title',
    'badge' => null
])

<div class="card-section">
    @if($title || $icon || $badge || isset($actions))
    <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            @if($icon)
            <div class="icon-container-md icon-{{ $iconColor }} text-white">
                <i class="fas {{ $icon }}"></i>
            </div>
            @endif
            <h3 class="card-title">{{ $title }}</h3>
        </div>

        <div class="flex items-center gap-2">
            @if($badge)
            <span class="badge-slate">
                {{ $badge }}
            </span>
            @endif

            @isset($actions)
                {{ $actions }}
            @endisset
        </div>
    </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</div>
