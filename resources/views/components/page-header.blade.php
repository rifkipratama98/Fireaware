@props([
    'title' => '',
    'description' => null,
])

<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1 fw-semibold">{{ $title }}</h1>
        @if ($description)
            <p class="text-secondary-soft mb-0">{{ $description }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="d-flex gap-2">{{ $actions }}</div>
    @endif
</div>
