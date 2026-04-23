@props(['blur' => false, 'padding' => true])

<div class="root-miniviews-panel root-miniviews-panel--{{ $blur ? 'blurred' : 'normal' }} root-miniviews-panel--{{ $padding ? 'padded' : 'unpadded' }}">
    <div class="root-miniviews-panel__content">
        {{ $slot }}
    </div>
</div>

@once
    <style lang="scss" scoped>
    .root-miniviews-panel {
        width: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: start;
        align-items: center;
        box-sizing: border-box;
        &.root-miniviews-panel--blurred {
            backdrop-filter: blur(10px);
        }
        &.root-miniviews-panel--normal {
            backdrop-filter: none;
        }
        &.root-miniviews-panel--full-height {
            height: 100%;
        }
        &.root-miniviews-panel--normal-height {
            height: auto;
        }
        &.root-miniviews-panel--padded {
            padding: 7em 25% 3em 25%;
        }
        &.root-miniviews-panel--unpadded {
            padding: 0;
        }
        & > .root-miniviews-panel__content {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: center;
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            background-color: var(--color-background-transparent-light);
            border: 1px solid var(--color-background-transparent-light-border);
            border-radius: var(--border-radius-small);
            gap: var(--gap-large);
            padding: var(--padding-medium);
        }
    }
    </style>
@endonce
