@php
    $menuHome = request()->path() === '';
    $menuService = request()->is('service');
    $menuContact = request()->is('contact*');
    $menuCart = request()->is('cart');
    $cartItemCount = (int) collect(session('cart', []))->sum(fn (array $line) => (int) ($line['quantity'] ?? 0));
    $cartBadgeText = $cartItemCount > 99 ? '99+' : (string) $cartItemCount;
@endphp
<div id="main-menu" class="menu">
    <div class="flex justify-center items-center">
        <x-svg.logo mode="light" class="logo"/>
    </div>
    <a href="/" class="menu-item @if($menuHome) menu-item--active @endif" @if($menuHome) aria-current="page" @endif>{{ __('menu.home') }}</a>
    <a href="/service" class="menu-item @if($menuService) menu-item--active @endif" @if($menuService) aria-current="page" @endif>{{ __('menu.service') }}</a>
    <a href="/contact" class="menu-item @if($menuContact) menu-item--active @endif" @if($menuContact) aria-current="page" @endif>{{ __('menu.business') }}</a>
    <a href="/contact" class="menu-item @if($menuContact) menu-item--active @endif" @if($menuContact) aria-current="page" @endif>{{ __('menu.custom') }}</a>
    <a href="/contact" class="menu-item @if($menuContact) menu-item--active @endif" @if($menuContact) aria-current="page" @endif>{{ __('menu.about_us') }}</a>
    <a href="/contact" class="menu-item @if($menuContact) menu-item--active @endif" @if($menuContact) aria-current="page" @endif>{{ __('menu.contact') }}</a>
    <span class="menu-item">|</span>
    <a
        href="/cart"
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

    <x-language-switcher class="menu__language" />
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
            padding: var(--padding-small);
            width: 2.5em;
            height: 2.5em;
        }
        .menu-item {
            color: var(--color-text-light);
        }
        .menu-item.menu-item--active {
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 4px;
        }
        .menu .menu__language {
            margin-left: auto;
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
    </style>
@endonce