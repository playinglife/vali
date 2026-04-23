@props([
    'triggerLabel' => 'Menu',
])

<details class="admin-burger" data-admin-burger>
    <summary
        class="ignore-global admin-burger__trigger"
        aria-label="{{ $triggerLabel }}"
        aria-haspopup="menu"
    >
        <x-icon name="heroicon-o-bars-3" class="admin-burger__icon" aria-hidden="true" />
    </summary>
    <ul class="admin-burger__dropdown" role="menu" aria-label="{{ $triggerLabel }}">
        <li role="none">
            <a href="{{ route('admin.dashboard') }}" class="ignore-global admin-burger__item" role="menuitem">Products</a>
        </li>
        <li role="none">
            <a href="{{ route('admin.orders') }}" class="ignore-global admin-burger__item" role="menuitem">Orders</a>
        </li>
    </ul>
</details>

@once
    <style>
        .admin-burger {
            position: relative;
            display: flex;
            align-items: center;
            flex-shrink: 0;
            box-sizing: border-box;
        }

        .admin-burger > summary {
            list-style: none;
        }

        .admin-burger > summary::-webkit-details-marker {
            display: none;
        }

        .admin-burger__trigger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.25rem;
            height: 1.25rem;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border-radius: var(--border-radius-small);
            border: 1px solid var(--color-border-medium);
            background-color: var(--color-background-light);
            color: var(--color-text-dark);
            cursor: pointer;
        }

        .admin-burger__trigger:hover,
        .admin-burger__trigger:focus-visible {
            background-color: var(--color-interact-1);
            outline: none;
        }

        .admin-burger__trigger:focus-visible {
            outline: 2px solid var(--color-one);
            outline-offset: 2px;
        }

        .admin-burger__icon {
            width: 1.35rem;
            height: 1.35rem;
        }

        .admin-burger__dropdown {
            position: absolute;
            top: calc(100% + 0.25rem);
            left: 0;
            margin: 0;
            padding: 0;
            list-style: none;
            min-width: 10rem;
            z-index: 300;
            box-sizing: border-box;
            background-color: var(--color-background-light);
            border: 1px solid var(--color-border-medium);
            border-radius: var(--border-radius-small);
            box-shadow: 0 0.35rem 1rem color-mix(in srgb, black 18%, transparent);
        }

        .admin-burger__item {
            display: block;
            width: 100%;
            margin: 0;
            padding: var(--padding-small) var(--padding-medium);
            box-sizing: border-box;
            text-align: left;
            text-decoration: none;
            font-family: var(--font-family-one);
            font-size: var(--text-size-tiny);
            color: var(--color-text-dark);
            background: transparent;
            border: none;
            cursor: pointer;
        }

        a.admin-burger__item {
            color: inherit;
        }

        .admin-burger__item:hover,
        .admin-burger__item:focus-visible {
            background-color: var(--color-interact-1);
            color: var(--color-text-light);
            outline: none;
        }
    </style>

    <script>
        document.addEventListener('click', function (event) {
            document.querySelectorAll('.admin-burger[open]').forEach(function (el) {
                if (!el.contains(event.target)) {
                    el.removeAttribute('open');
                }
            });
        });
    </script>
@endonce
