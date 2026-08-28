@props(['title', 'body'])

<div data-reference="legal-page" class="root-legal-page">
    <div class="grid root-legal-page__grid">
        <div class="grid grid-middle grid-center root-legal-page__main-title">
            <h1 class="dark"> {{ $title }} </h1>
        </div>
        <x-miniviews.panel :padding="false">
            <div class="root-legal-page__line">
                <div class="root-legal-page__line-content">
                    <div class="root-legal-page__body text-small dark">
                        {!! $body !!}
                    </div>
                </div>
            </div>
        </x-miniviews.panel>
    </div>
</div>

@once
    <style lang="scss" scoped>
        .root-legal-page {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            flex: 1 0 auto;
            padding-top: 4em;
        }
        .root-legal-page__grid {
            gap: var(--gap-large);
            padding: 0 25% var(--padding-huge) 25%;
        }
        .root-legal-page__line-content {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            padding: var(--padding-huge);
            box-sizing: border-box;
        }
        .root-legal-page__main-title {
            flex: 1 1 auto;
            min-width: 0;
            padding-right: var(--gap-medium);
            padding-top: var(--padding-medium);
            & > h1 {
                color: var(--color-text-light);
            }
        }
        .root-legal-page__line {
            width: 100%;
        }
        .root-legal-page__body {
            width: 100%;
            & > h2 {
                color: var(--color-text-dark);
                font-size: 1.2rem;
                text-shadow: none;
                margin: 1.25em 0 0.4em;
            }
            & > h2:first-child {
                margin-top: 0;
            }
            & > p,
            & > ul {
                margin: 0 0 0.8em;
            }
            & > ul {
                padding-left: 1.25em;
            }
        }
    </style>
@endonce
