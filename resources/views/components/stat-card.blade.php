{{-- Stat Card Component --}}
{{-- Usage: <x-stat-card icon="fa-coins" label="Total Sales" value="Rp 1.250.000" trend="up" percentage="12.5" /> --}}

@props([
    'icon' => 'fa-chart-line',
    'iconColor' => 'primary', // primary, success, warning, danger, info
    'label' => 'Stat Label',
    'value' => '0',
    'trend' => null, // up, down, neutral
    'percentage' => null,
    'badge' => null,
    'unit' => null
])

<div class="card-stat group">
    <div class="flex items-center justify-between mb-4">
        <div class="icon-container-lg icon-{{ $iconColor }} text-white group-hover:scale-110 transition-transform duration-300">
            <i class="fas {{ $icon }} text-lg"></i>
        </div>

        @if($badge)
            <span class="stat-badge {{ $badge['class'] ?? 'stat-badge-info' }}">
                @if(isset($badge['icon']))
                <i class="fas {{ $badge['icon'] }} text-[10px]"></i>
                @endif
                {{ $badge['text'] }}
            </span>
        @elseif($trend && $percentage)
            @if($trend === 'up')
                <span class="stat-badge-success">
                    <i class="fas fa-arrow-up text-[10px]"></i> {{ $percentage }}%
                </span>
            @elseif($trend === 'down')
                <span class="stat-badge-danger">
                    <i class="fas fa-arrow-down text-[10px]"></i> {{ $percentage }}%
                </span>
            @else
                <span class="stat-badge-info">
                    {{ $percentage }}%
                </span>
            @endif
        @endif
    </div>

    <p class="stat-label">{{ $label }}</p>
    <h3 class="stat-value">
        {{ $value }}
        @if($unit)
        <span class="text-sm font-normal text-slate-500">{{ $unit }}</span>
        @endif
    </h3>

    @if(isset($footer))
    <div class="mt-2 text-xs text-slate-400">
        {{ $footer }}
    </div>
    @endif
</div>
