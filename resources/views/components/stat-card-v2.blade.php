{{-- Stat Card Component V2 - Clean & Minimal --}}
{{-- Usage: <x-stat-card-v2 icon="fa-coins" label="Sales" value="Rp 1.250.000" trend="up" percentage="12" /> --}}

@props([
    'icon' => 'fa-chart-line',
    'iconColor' => 'primary',
    'label' => 'Metric Label',
    'value' => '0',
    'unit' => null,
    'trend' => null,
    'percentage' => null,
    'badge' => null
])

<div class="card-stat">
    <div class="flex items-start justify-between mb-4">
        <div class="icon-container-md icon-{{ $iconColor }}">
            <i class="fas {{ $icon }}"></i>
        </div>

        @if($trend)
            @if($trend === 'up')
                <span class="stat-badge-success">
                    <i class="fas fa-arrow-up text-[10px]"></i>
                    {{ $percentage }}%
                </span>
            @elseif($trend === 'down')
                <span class="stat-badge-danger">
                    <i class="fas fa-arrow-down text-[10px]"></i>
                    {{ $percentage }}%
                </span>
            @endif
        @elseif($badge)
            @php
                $badgeClass = is_array($badge) ? ($badge['class'] ?? 'info') : 'info';
                $badgeIcon = is_array($badge) ? ($badge['icon'] ?? null) : null;
                $badgeText = is_array($badge) ? ($badge['text'] ?? $badge) : $badge;
            @endphp
            <span class="stat-badge-{{ $badgeClass }}">
                @if($badgeIcon)
                <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                @endif
                {{ $badgeText }}
            </span>
        @endif
    </div>

    <p class="stat-label">{{ $label }}</p>
    <div class="flex items-baseline gap-2">
        <h3 class="stat-value">{{ $value }}</h3>
        @if($unit)
        <span class="text-sm text-slate-500">{{ $unit }}</span>
        @endif
    </div>

    @isset($footer)
    <div class="mt-3 pt-3 border-t border-slate-100">
        {{ $footer }}
    </div>
    @endisset
</div>
