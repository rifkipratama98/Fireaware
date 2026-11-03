@props([
    'type' => 'info',
    'dismissible' => true,
    'icon' => null,
])

@php
    $variants = ['success' => 'success', 'info' => 'info', 'warning' => 'warning', 'error' => 'danger', 'danger' => 'danger'];
    $variant = $variants[$type] ?? 'info';
    $defaultIcons = ['success' => 'check-circle', 'info' => 'info-circle', 'warning' => 'exclamation-triangle', 'danger' => 'x-circle'];
    $iconClass = $icon ?? $defaultIcons[$variant] ?? 'info-circle';
@endphp

<div {{ $attributes->merge(['class' => 'alert alert-' . $variant . ' d-flex align-items-start gap-2 mb-3']) }} role="alert">
    <i class="bi bi-{{ $iconClass }} mt-1"></i>
    <div class="flex-grow-1">{{ $slot }}</div>
    @if ($dismissible)
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
    @endif
</div>
