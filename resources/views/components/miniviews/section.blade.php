@props(['title' => null, 'text' => null, 'content' => null, 'type' => 'one', 'background' => 'transparent', 'blur' => false])

<div class="root-miniviews-section root-miniviews-section--{{ $type }} root-miniviews-section--{{ $background }}">
    @if($title)
        <div class="title">
            <h2> {{ $title }} </h2>
        </div>
    @endif
    @if($text)
        <div class="text">{{ $text }}</div>
    @endif
    @if($content)
        <div class="content">{{ $content }}</div>
    @endif
</div>

@once
    <style lang="scss" scoped>
    .root-miniviews-section {
        min-height: 100%;
        width: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: start;
        align-items: center;
        gap: var(--padding-large);
        @if($blur)
            backdrop-filter: blur(10px);
        @endif
        &.root-miniviews-section--one {
            
        }
        &.root-miniviews-section--two {
            
        }
        &.root-miniviews-section--white {
            background-color: white;
        }
        & > .title {   
            padding: var(--padding-large) calc(var(--padding-large) * 3) 0 calc(var(--padding-large) * 3);
            color: var(--color-text-dark);
            & > h2 {   
                color: var(--color-text-dark);
            }
        }
        & > .text, & > .content {
            /* align-items:center on the section shrinks flex children to content width; stretch so grids use full page width */
            align-self: stretch;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            color: var(--color-text-dark);
            padding: 0 15% calc(var(--padding-large) * 2) 15%;
            font-size: 0.8em;
        }
        & > .text {
            white-space: pre-wrap;
        }
    }
    </style>
@endonce
