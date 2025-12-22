{{-- Page Header Component V2 - Clean & Modern --}}
{{-- Usage: <x-page-header-v2 icon="fa-chart-pie" title="Dashboard" subtitle="Overview"> --}}

@props([
    'icon' => 'fa-chart-pie',
    'title' => 'Page Title',
    'subtitle' => null,
    'iconColor' => 'primary'
])

<div class="page-header">
    <div class="page-header-content">
        <div class="flex items-center gap-4">
            <div class="icon-container-lg icon-{{ $iconColor }}">
                <i class="fas {{ $icon }} text-xl"></i>
            </div>
            <div>
                <h1 class="page-title">{{ $title }}</h1>
                @if($subtitle)
                <p class="page-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @isset($actions)
        <div class="flex items-center gap-3 flex-wrap">
            {{ $actions }}
        </div>
        @endisset
    </div>
</div>
