<div data-reference="contact" class="root-contact">
    <div class="grid root-contact__grid">
        <div class="grid grid-middle grid-center root-contact__main-title">
            <h2> {{ __('pages.contact.title1') }} </h2>
        </div>
        <x-miniviews.panel :padding="false">
            <div class="root-contact__line">
                <div class="root-contact__line-content">
                    <div class="col root-contact__line-content-left">
                        <x-icon name="heroicon-s-map-pin" class="small-icon" />
                        <h4 class="dark">{{ __('pages.contact.address') }}</h4>
                        <p class="dark">{!! __('pages.contact.address_text') !!}</p>
                    </div>
                    <div class="col root-contact__line-content-right">
                        <x-icon name="heroicon-s-envelope" class="small-icon" />
                        <h4 class="dark">{{ __('pages.contact.email') }}</h4>
                        <p class="dark">{!! __('pages.contact.email_text') !!}</p>
                    </div>
                </div>
            </div>

            <div class="root-contact__line">
                <div class="root-contact__line-content">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d4305.806624961042!2d24.792378!3d46.224045!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x474b75b8ec8195c5%3A0x569121231c1b7b2e!2sStrada%20Libert%C4%83%C8%9Bii%209%2C%20545400%20Sighi%C8%99oara%2C%20Romania!5e1!3m2!1sen!2sus!4v1776875907754!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>                    
                </div>
            </div>
        </x-miniviews.panel>
    </div>
</div>

@once
    <style lang="scss" scoped>
        .root-contact {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            flex: 1 0 auto;
            padding-top: 4em;
        }
        .root-contact__grid {
            gap: var(--gap-large);
            padding: 0 25% var(--padding-huge) 25%;
        }
        .root-contact__line-content {
            width: 100%;
            display: flex;
            flex-direction: row;
            gap: var(--gap-medium);
            box-sizing: border-box;
            & > iframe {
                width: 100%;
            }
            & > .root-contact__line-content-left {
                flex: 1 1 auto;
                min-width: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: var(--gap-small);
                p {
                    align-self: stretch;
                    text-align: center;
                    font-size: var(--text-size-normal);
                    margin: 0;
                }
            }
            & > .root-contact__line-content-right {
                flex: 1 1 auto;
                min-width: 0;
                display: flex;
                flex-direction: column;
                gap: var(--gap-small);
                justify-content: center;
                align-items: center;
                p {
                    align-self: stretch;
                    text-align: center;
                    font-size: var(--text-size-normal);
                    margin: 0;
                }
            }
        }
        .root-contact__main-title {
            flex: 1 1 auto;
            min-width: 0;
            padding-right: var(--gap-medium);
            padding-top: var(--padding-medium);
        }
        .root-contact__line {
            width: 100%;
        }
        .root-contact__line-content-item {
            width: 100%;
            display: flex;
            justify-content: center;
            & > p {
                white-space: pre-line;
            }
        }
        .root-contact__line-content-item-buttons {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: var(--gap-medium);
            padding: var(--padding-small);
        }
    </style>
@endonce
