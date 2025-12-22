{{-- Page Header Component --}}
{{-- Usage: <x-page-header icon="fa-dashboard" title="Dashboard" subtitle="Overview" /> --}}

@props([
    'icon' => 'fa-chart-pie',
    'title' => 'Page Title',
    'subtitle' => null,
    'iconColor' => 'primary' // primary, success, warning, danger, info
])

<div class="page-header">
    <div class="page-header-overlay"></div>
    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="icon-container-xl icon-{{ $iconColor }} text-white">
                <i class="fas {{ $icon }} text-2xl"></i>
            </div>
            <div>
                <h1 class="page-title">{{ $title }}</h1>
                @if($subtitle)
                <p class="page-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if(isset($actions))
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
        @endif
    </div>
</div>
