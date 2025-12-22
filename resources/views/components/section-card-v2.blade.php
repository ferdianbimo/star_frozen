{{-- Section Card Component V2 - Clean Design --}}
{{-- Usage: <x-section-card-v2 icon="fa-list" title="Data List" badge="25"> --}}

@props([
    'icon' => null,
    'iconColor' => 'primary',
    'title' => 'Section Title',
    'badge' => null
])

<div class="card-section">
    @if($title || $icon || $badge || isset($actions))
    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            @if($icon)
            <div class="icon-container-md icon-{{ $iconColor }}">
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

    {{ $slot }}
</div>
