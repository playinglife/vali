@props([])

<div class="root-miniviews-panel">
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
        padding: 7em 25% 3em 25%;
        box-sizing: border-box;

        & > .root-miniviews-panel__content {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: center;
            background-color: var(--color-background-transparent-light);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius-small);
            border: 1px solid var(--color-background-transparent-light-border);
        }
    }
    </style>
@endonce
