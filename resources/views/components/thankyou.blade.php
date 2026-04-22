<x-menu-height-compensator />
<div data-reference="thankyou" class="root-thankyou">
    <div class="grid root-thankyou__grid">
        <div class="grid grid-middle grid-center root-thankyou__main-title">
            <h2 class="dark"> {{ __('pages.thankyou.title1') }} </h2>
        </div>
        <x-miniviews.panel :padding="false">
            <div class="root-thankyou__line">
                <div class="root-thankyou__line-content">
                    <div class="root-thankyou__line-content-item">
                        <p class="text-small dark">{{ __('pages.thankyou.message') }}</p>
                    </div>
                    <div class="root-thankyou__line-content-item-buttons">
                        <x-button text="{{ __('pages.thankyou.back_to_products') }}" url="{{ url('/products') }}" aria-label="{{ __('pages.thankyou.back_to_products') }}" />
                    </div>
                </div>
            </div>
        </x-miniviews.panel>
    </div>
</div>

@once
    <style lang="scss" scoped>
        .root-thankyou {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            flex: 1 0 auto;
        }
        .root-thankyou__grid {
            gap: var(--gap-large);
            padding: 0 25% var(--padding-huge) 25%;
        }
        .root-thankyou__line-content {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            padding: var(--padding-huge);
            box-sizing: border-box;
        }
        .root-thankyou__main-title {
            flex: 1 1 auto;
            min-width: 0;
            padding-right: var(--gap-medium);
            padding-top: var(--padding-medium);
        }
        .root-thankyou__line {
            width: 100%;
        }
        .root-thankyou__line-content-item {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .root-thankyou__line-content-item-buttons {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: var(--gap-medium);
            padding: var(--padding-small);
        }
    </style>
@endonce
