@props(['title' => null, 'text' => null, 'type' => 'one'])

<div class="root-miniviews-section root-miniviews-section--{{ $type }}">
    @if($title)
        <div class="title">
            <h2> {{ $title }} </h2>
        </div>
    @endif
    @if($text)
        <div class="text">{{ $text }}</div>
    @endif
    <x-dividers.divider1 color="var(--color-border)" height="3" :reverse="$type === 'two'" />
</div>

@once
    <style lang="scss" scoped>
    .root-miniviews-section {
        width: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: start;
        align-items: center;
        background-color: white;
        gap: var(--padding-large);
        &.root-miniviews-section--one {
            
        }
        &.root-miniviews-section--two {
            
        }
        & > .title {   
            padding: var(--padding-large) calc(var(--padding-large) * 3) 0 calc(var(--padding-large) * 3);
            color: var(--color-text-dark);
            & > h2 {   
                color: var(--color-text-dark);
            }
        }
        & > .text {
            color: var(--color-text-dark);
            padding: 0 15% calc(var(--padding-large) * 2) 15%;
            white-space: pre-wrap;
            font-size: 0.8em;
        }
    }
    </style>
@endonce
