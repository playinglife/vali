<div class="menu">
    <div class="flex justify-center items-center">
        <x-svg.logo mode="light" class="logo"/>
    </div>
    <a href="/" class="menu-item">{{ __('menu.home') }}</a>
    <a href="/about" class="menu-item">{{ __('menu.service') }}</a>
    <a href="/contact" class="menu-item">{{ __('menu.business') }}</a>
    <a href="/contact" class="menu-item">{{ __('menu.custom') }}</a>
    <a href="/contact" class="menu-item">{{ __('menu.about_us') }}</a>
    <a href="/contact" class="menu-item">{{ __('menu.contact') }}</a>
    <span class="menu-item">|</span>

    <x-language />

    <a class="flex-1"></a>
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
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 1em;
            gap: 2em;
            text-align: center;
            text-decoration: none;
            justify-content: flex-start;
            background-color: var(--color-background-transparent);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--color-border);
        }
        .menu .logo {
            padding: var(--padding-small);
            width: 2.5em;
            height: 2.5em;
        }
        .menu-item {
            color: var(--color-text);
        }
    </style>
@endonce