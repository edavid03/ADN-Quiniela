@props([
    'match',
    'side' => 'left',
])

@php
    $home = $match['home'];
    $away = $match['away'];
    $hasScore = $home['goles'] !== null && $away['goles'] !== null;
@endphp

<article {{ $attributes->merge(['class' => 'bracket-cell bracket-cell--'.$side]) }}>
    <div class="bracket-card">
        @foreach ([$home, $away] as $lado)
            <div @class(['bracket-team', 'bracket-team--win' => $lado['winner']])>
                <span class="bracket-flag">
                    @if ($lado['team'])
                        {!! $lado['team']->flagEmojiHtml() !!}
                    @else
                        <span class="bracket-flag--ph" aria-hidden="true">?</span>
                    @endif
                </span>
                <span class="bracket-team__name">{{ $lado['label'] }}</span>
                <span class="bracket-team__score">{{ $hasScore ? $lado['goles'] : '' }}</span>
            </div>
        @endforeach

        @if (! empty($match['fecha_utc']))
            <div class="bracket-cell__meta">
                <x-local-time :date="$match['fecha_utc']" />
            </div>
        @endif
    </div>
</article>
