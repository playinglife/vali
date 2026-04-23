<div data-reference="aboutus" class="root-aboutus">
    <div class="grid root-aboutus__grid">
        <div class="grid grid-middle grid-center root-aboutus__main-title">
            <h2> {{ __('pages.aboutus.title1') }} </h2>
        </div>
        <x-miniviews.panel :padding="false">
            <div class="root-aboutus__line">
                <div class="root-aboutus__line-content">
                    <div class="root-aboutus__line-content-item">
                        <p class="text-small dark">{!! __('pages.aboutus.message') !!}</p>
                    </div>
                </div>
            </div>
        </x-miniviews.panel>
    </div>
</div>

@once
    <style lang="scss" scoped>
        .root-aboutus {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            flex: 1 0 auto;
            padding-top: 4em;
        }
        .root-aboutus__grid {
            gap: var(--gap-large);
            padding: 0 25% var(--padding-huge) 25%;
        }
        .root-aboutus__line-content {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            padding: var(--padding-huge);
            box-sizing: border-box;
        }
        .root-aboutus__main-title {
            flex: 1 1 auto;
            min-width: 0;
            padding-right: var(--gap-medium);
            padding-top: var(--padding-medium);
        }
        .root-aboutus__line {
            width: 100%;
        }
        .root-aboutus__line-content-item {
            width: 100%;
            display: flex;
            justify-content: center;
            & > p {
                white-space: pre-line;
            }
        }
        .root-aboutus__line-content-item-buttons {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: var(--gap-medium);
            padding: var(--padding-small);
        }
    </style>
@endonce
