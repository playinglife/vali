@php
    $gtmId = \App\Support\Gtm::id();
    $showBanner = \App\Support\Gtm::showBanner();
@endphp
@if ($gtmId !== null && $showBanner)
        <div data-reference="cookie-banner" class="root-cookie-banner" role="region" aria-label="{{ __('pages.cookies.aria') }}">
            <p class="text-tiny light root-cookie-banner__text">{!! __('pages.cookies.message', [
                'privacy' => '<a href="'.e(route('privacy')).'">'.e(__('pages.privacy.meta_title')).'</a>',
            ]) !!}</p>
            <div class="root-cookie-banner__actions">
                <x-button type="button" text="{{ __('pages.cookies.accept') }}" data-cookie-consent="granted" />
                <x-button type="button" text="{{ __('pages.cookies.reject') }}" data-cookie-consent="denied" />
            </div>
        </div>
    <script>
        (function () {
            var id = @json($gtmId);
            var banner = document.querySelector('[data-reference="cookie-banner"]');
            function setConsent(value) {
                var secure = location.protocol === 'https:' ? '; Secure' : '';
                document.cookie = @json(\App\Support\Gtm::COOKIE) + '=' + value + '; Path=/; Max-Age=31536000; SameSite=Lax' + secure;
            }
            function loadGtm() {
                if (window.__gtmLoaded || !id) {
                    return;
                }
                window.__gtmLoaded = true;
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
                var script = document.createElement('script');
                script.async = true;
                script.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(id);
                document.head.appendChild(script);
            }
            if (banner) {
                banner.addEventListener('click', function (event) {
                    var button = event.target.closest('[data-cookie-consent]');
                    if (!button) {
                        return;
                    }
                    var value = button.getAttribute('data-cookie-consent');
                    if (value !== 'granted' && value !== 'denied') {
                        return;
                    }
                    setConsent(value);
                    banner.remove();
                    if (value === 'granted') {
                        loadGtm();
                    }
                });
            }
        })();
    </script>
    @once
        <style>
            .root-cookie-banner {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 200;
                box-sizing: border-box;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: var(--gap-medium);
                padding: var(--padding-small) var(--padding-medium);
                background-color: var(--color-background-transparent-dark);
                backdrop-filter: blur(10px);
                border-top: 1px solid var(--color-border);
            }
            .root-cookie-banner__text {
                margin: 0;
                max-width: 42em;
                text-align: center;
            }
            .root-cookie-banner__text > a {
                color: inherit;
                text-decoration: underline;
            }
            .root-cookie-banner__actions {
                display: flex;
                flex-wrap: wrap;
                gap: var(--gap-small);
            }
        </style>
    @endonce
@endif
