@props(['date'])

@php
    $caracas = \Carbon\Carbon::parse($date, 'UTC')->setTimezone('America/Caracas');
@endphp

<time datetime="{{ $caracas->toIso8601String() }}">{{ $caracas->format('d/m/Y H:i') }} <span class="text-[var(--app-muted)]">VET</span></time>
