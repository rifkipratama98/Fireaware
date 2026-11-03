@php
    $flashTypes = ['success' => 'check-circle', 'error' => 'x-circle', 'warning' => 'exclamation-triangle', 'info' => 'info-circle'];
    $flashKey = collect(['success', 'error', 'warning', 'info'])->first(fn ($key) => session()->has($key));
@endphp

@if ($flashKey)
    <x-alert :type="$flashKey" :icon="$flashTypes[$flashKey] ?? null">
        {!! session($flashKey) !!}
    </x-alert>
@endif

@if ($errors->any())
    <x-alert type="error" icon="x-circle">
        <strong class="d-block mb-1">Mohon perbaiki masalah berikut:</strong>
        <ul class="mb-0 ms-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
