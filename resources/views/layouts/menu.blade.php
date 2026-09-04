@php
    $locale = app()->getLocale();
    $menuHome = request()->is($locale);
    $menuProducts = request()->is($locale.'/products', $locale.'/products/*');
    $menuCustom = request()->is($locale.'/custom');
    $menuAboutUs = request()->is($locale.'/aboutus');
    $menuContact = request()->is($locale.'/contact*');
    $menuCart = request()->is($locale.'/cart');
    $cartItemCount = (int) collect(session('cart', []))->sum(fn (array $line) => (int) ($line['quantity'] ?? 0));
    $cartBadgeText = $cartItemCount > 99 ? '99+' : (string) $cartItemCount;
    $menuLocales = \App\Http\Middleware\SetLocale::supportedLocalesWithFlags();
@endphp
<div id="main-menu" class="menu">
    <a href="{{ route('home') }}" class="menu__logo flex justify-center items-center" @if($menuHome) aria-current="page" @endif>
        <x-svg.logo mode="light" class="logo"/>
    </a>
    <nav id="main-menu-nav" class="menu__nav" data-menu-nav>
        <a href="{{ route('home') }}" class="menu-item @if($menuHome) menu-item--active @endif" @if($menuHome) aria-current="page" @endif>{{ __('menu.home') }}</a>
        <a href="{{ route('products.index') }}" class="menu-item @if($menuProducts) menu-item--active @endif" @if($menuProducts) aria-current="page" @endif>{{ __('menu.products') }}</a>
        <a href="{{ route('custom') }}" class="menu-item @if($menuCustom) menu-item--active @endif" @if($menuCustom) aria-current="page" @endif>{{ __('menu.custom') }}</a>
        <a href="{{ route('aboutus') }}" class="menu-item @if($menuAboutUs) menu-item--active @endif" @if($menuAboutUs) aria-current="page" @endif>{{ __('menu.about_us') }}</a>
        <a href="{{ route('contact') }}" class="menu-item @if($menuContact) menu-item--active @endif" @if($menuContact) aria-current="page" @endif>{{ __('menu.contact') }}</a>
        <span class="menu-item menu-item--divider">|</span>
        <a
            href="{{ route('cart') }}"
            class="menu-item menu-item--cart @if($menuCart) menu-item--active @endif"
            @if($menuCart) aria-current="page" @endif
            aria-label="{{ __('menu.cart_aria', ['count' => $cartItemCount]) }}"
        >
            <span class="menu-item__cart-wrap">
                <x-icon name="heroicon-o-shopping-cart" class="menu-item__cart" aria-hidden="true" />
                @if ($cartItemCount > 0)
                    <span class="menu-item__cart-badge">{{ $cartBadgeText }}</span>
                @endif
            </span>
        </a>
    </nav>
    <div class="menu__actions">
        @if (count($menuLocales) > 1)
            <button
                type="button"
                class="ignore-global menu-circle menu-lang"
                aria-label="{{ __('menu.lang_open') }}"
                aria-controls="main-menu-lang"
                aria-expanded="false"
                data-menu-lang
                data-label-open="{{ __('menu.lang_open') }}"
                data-label-close="{{ __('menu.lang_close') }}"
            >
                <x-icon name="heroicon-o-language" class="menu-circle__icon menu-lang__icon--idle" aria-hidden="true" />
                <x-icon name="heroicon-o-x-mark" class="menu-circle__icon menu-lang__icon--close" aria-hidden="true" />
            </button>
        @endif
        <x-language-switcher class="menu__language" />
        <button
            type="button"
            class="ignore-global menu-circle menu-burger"
            aria-label="{{ __('menu.open') }}"
            aria-controls="main-menu-nav"
            aria-expanded="false"
            data-menu-burger
            data-label-open="{{ __('menu.open') }}"
            data-label-close="{{ __('menu.close') }}"
        >
            <x-icon name="heroicon-o-bars-3" class="menu-circle__icon menu-burger__icon--bars" aria-hidden="true" />
            <x-icon name="heroicon-o-x-mark" class="menu-circle__icon menu-burger__icon--close" aria-hidden="true" />
        </button>
    </div>
    @if (count($menuLocales) > 1)
        <nav id="main-menu-lang" class="menu__lang-nav" data-menu-lang-nav>
            @foreach ($menuLocales as $menuLocale)
                <a
                    href="{{ url(\App\Http\Middleware\SetLocale::localizedPath($menuLocale['code'])) }}"
                    class="menu-item @if($locale === $menuLocale['code']) menu-item--active @endif"
                    hreflang="{{ $menuLocale['code'] }}"
                    @if($locale === $menuLocale['code']) aria-current="true" @endif
                >
                    @if ($menuLocale['icon'])
                        <x-icon name="{{ $menuLocale['icon'] }}" class="menu__lang-flag" aria-hidden="true" />
                    @endif
                    {{ $menuLocale['name'] }}
                </a>
            @endforeach
        </nav>
    @endif
</div>

@once
    <style>
        .menu {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4em;
            box-sizing: border-box;
            z-index: 100;
            display: flex;
            align-items: center;
            width: 100%;
            padding: 1em;
            gap: 2em;
            text-align: center;
            text-decoration: none;
            justify-content: flex-start;
            background-color: var(--color-background-transparent-dark);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--color-border);
        }
        .menu .logo {
            font-size: 1.2rem;
            padding: var(--padding-small);
            width: 2.5em;
            height: 2.5em;
        }
        .menu a.menu__logo {
            font-size: inherit;
        }
        .menu__nav {
            display: flex;
            align-items: center;
            flex: 1 1 auto;
            min-width: 0;
            gap: 2em;
        }
        .menu__actions {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            gap: var(--gap-small);
            margin-left: auto;
        }
        .menu-item {
            color: var(--color-text-light);
        }
        .menu-item.menu-item--active {
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 4px;
        }
        .menu-item--cart {
            position: relative;
        }
        .menu-item__cart-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
        }
        .menu-item__cart {
            width: 1.5em;
            height: 1.5em;
            display: block;
        }
        .menu-item__cart-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -1.4em;
            right: -1.7em;
            box-sizing: border-box;
            width: 2em;
            height: 2em;
            border-radius: 999px;
            background: var(--color-action);
            border: 1px solid var(--color-border-transparent-light);
            color: var(--color-text-light);
        }
        .menu.menu--hidden {
            display: none !important;
        }
        .menu-circle {
            display: none;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 2.5em;
            height: 2.5em;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border-radius: 50%;
            border: 1px solid var(--color-border) !important;
            background-color: var(--color-background-light);
            color: var(--color-text-dark);
            cursor: pointer;
        }
        .menu-circle__icon {
            width: 1.5em;
            height: 1.5em;
            display: block;
        }
        .menu-burger__icon--close,
        .menu-lang__icon--close {
            display: none;
        }
        .menu__lang-nav {
            display: none;
        }
        @media (max-width: 64em) {
            .menu.menu--open,
            .menu.menu--lang-open {
                opacity: 1 !important;
            }
            .menu-circle {
                display: inline-flex;
            }
            .menu.menu--open .menu-burger__icon--bars {
                display: none;
            }
            .menu.menu--open .menu-burger__icon--close {
                display: block;
            }
            .menu.menu--lang-open .menu-lang__icon--idle {
                display: none;
            }
            .menu.menu--lang-open .menu-lang__icon--close {
                display: block;
            }
            .menu .menu__language {
                display: none;
            }
            .menu__nav,
            .menu__lang-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                flex: none;
                flex-direction: column;
                align-items: stretch;
                gap: 0;
                width: 100%;
                box-sizing: border-box;
                padding: var(--padding-small) 0;
                background-color: var(--color-background-transparent-dark);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--color-border);
                text-align: left;
            }
            .menu.menu--open .menu__nav,
            .menu.menu--lang-open .menu__lang-nav {
                display: flex;
            }
            .menu__nav .menu-item,
            .menu__lang-nav .menu-item {
                display: flex;
                align-items: center;
                min-height: 2.75em;
                padding: var(--padding-tiny) var(--padding-small);
                box-sizing: border-box;
            }
            .menu__nav .menu-item--divider {
                display: none;
            }
            .menu__nav .menu-item__cart-badge {
                top: 50%;
                right: auto;
                left: calc(100% + 0.35em);
                transform: translateY(-50%);
            }
            .menu__lang-flag {
                width: 1.4em;
                flex-shrink: 0;
                margin-right: 0.5em;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.getElementById('main-menu');
            const burger = menu && menu.querySelector('[data-menu-burger]');
            if (!menu || !burger) {
                return;
            }

            const langBtn = menu.querySelector('[data-menu-lang]');
            const desktopQuery = window.matchMedia('(min-width: 64.01em)');
            const main = document.getElementById('main-content');

            function setMenuOpen(open) {
                menu.classList.toggle('menu--open', open);
                burger.setAttribute('aria-expanded', open ? 'true' : 'false');
                burger.setAttribute('aria-label', open ? burger.dataset.labelClose : burger.dataset.labelOpen);
                if (open) {
                    closeLang();
                }
            }

            function closeLang() {
                menu.classList.remove('menu--lang-open');
                if (langBtn) {
                    langBtn.setAttribute('aria-expanded', 'false');
                    langBtn.setAttribute('aria-label', langBtn.dataset.labelOpen);
                }
            }

            function setLangOpen(open) {
                if (!langBtn) {
                    return;
                }
                menu.classList.toggle('menu--lang-open', open);
                langBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                langBtn.setAttribute('aria-label', open ? langBtn.dataset.labelClose : langBtn.dataset.labelOpen);
                if (open) {
                    menu.classList.remove('menu--open');
                    burger.setAttribute('aria-expanded', 'false');
                    burger.setAttribute('aria-label', burger.dataset.labelOpen);
                }
            }

            function closeAll() {
                setMenuOpen(false);
                closeLang();
            }

            burger.addEventListener('click', function (event) {
                event.stopPropagation();
                setMenuOpen(!menu.classList.contains('menu--open'));
            });

            if (langBtn) {
                langBtn.addEventListener('click', function (event) {
                    event.stopPropagation();
                    setLangOpen(!menu.classList.contains('menu--lang-open'));
                });
            }

            document.addEventListener('click', function (event) {
                if (!menu.contains(event.target)) {
                    closeAll();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAll();
                }
            });

            desktopQuery.addEventListener('change', function (event) {
                if (event.matches) {
                    closeAll();
                }
            });

            if (main) {
                main.addEventListener('scroll', function () {
                    if (menu.classList.contains('menu--open') || menu.classList.contains('menu--lang-open')) {
                        closeAll();
                    }
                }, { passive: true });
            }
        });
    </script>
@endonce
