@props([
    'products' => null,
    /** null = link with route('products.show'); false = no links; string = custom base URL before slug */
    'productUrl' => null,
    'instanceId' => 'products-grid',
])

@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Product>|null $products */
    $products = $products ?? \App\Models\Product::query()
        ->active()
        ->with(['categories', 'variants.optionValues.option'])
        ->orderBy('name')
        ->get();

    $categories = \App\Models\Category::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    $optionValueChoices = \App\Models\ProductOptionValue::query()
        ->with('option')
        ->whereHas('variants')
        ->orderBy('product_option_id')
        ->orderBy('sort_order')
        ->get();

    /** One entry per distinct option name + value; ids = all DB ids for that label across products. */
    $optionValueGroups = $optionValueChoices
        ->groupBy(function (\App\Models\ProductOptionValue $ov) {
            $name = $ov->option?->name ?? '';

            return mb_strtolower($name."\u{001e}".$ov->value);
        })
        ->map(function (\Illuminate\Support\Collection $group) {
            $first = $group->first();
            $optName = $first->option?->name ?? '';

            return [
                'option_name' => $optName,
                'value_label' => $first->value,
                'ids' => $group->pluck('id')->unique()->sort()->values()->implode(','),
                'value_sort_order' => (int) ($first->sort_order ?? 0),
                'option_sort_order' => (int) ($first->option?->sort_order ?? 0),
            ];
        })
        ->values();

    /** One row per option (Size, Color, …); each row lists value checkboxes on the same line. */
    $optionFilterRows = $optionValueGroups
        ->groupBy(fn (array $g) => mb_strtolower($g['option_name'] ?? ''))
        ->map(function (\Illuminate\Support\Collection $group) {
            $first = $group->first();

            return [
                'option_name' => $first['option_name'],
                'option_sort_order' => $group->min('option_sort_order'),
                'values' => $group
                    ->sortBy(fn (array $v) => [$v['value_sort_order'], mb_strtolower($v['value_label'])])
                    ->values(),
            ];
        })
        ->values()
        ->sortBy('option_sort_order')
        ->values();

    $resultsShownTemplate = __('components.products.results_shown');
    $totalCount = $products->count();
@endphp

<div
    class="root-products"
    id="{{ $instanceId }}"
    data-root-products
    data-results-shown-template="{{ e($resultsShownTemplate) }}"
>
    @if ($products->isEmpty())
        <p class="root-products__catalog-empty">{{ __('components.products.catalog_empty') }}</p>
    @else
        <div class="root-products__filters" role="region" aria-label="{{ __('components.products.filters_region') }}">
            @if ($categories->isNotEmpty())
                <fieldset class="root-products__filter-row" data-root-products-filter-categories>
                    <legend class="root-products__legend">{{ __('components.products.filter_categories') }}</legend>
                    <div class="root-products__checkbox-list">
                        @foreach ($categories as $category)
                            <label class="root-products__check">
                                <input
                                    type="checkbox"
                                    name="{{ $instanceId }}-category[]"
                                    value="{{ $category->id }}"
                                    id="{{ $instanceId }}-cat-{{ $category->id }}"
                                />
                                <span class="root-products__check-text">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            @if ($optionFilterRows->isNotEmpty())
                <fieldset class="root-products__filter-row" data-root-products-filter-options>
                    <legend class="root-products__legend">{{ __('components.products.filter_options') }}</legend>
                    <div class="root-products__option-groups">
                        @foreach ($optionFilterRows as $optIdx => $optRow)
                            <div class="root-products__option-line">
                                @if ($optRow['option_name'] !== '')
                                    <span class="root-products__option-line-label">{{ $optRow['option_name'] }}:</span>
                                @endif
                                <div class="root-products__option-line-values">
                                    @foreach ($optRow['values'] as $valIdx => $val)
                                        @php
                                            $optAria = $optRow['option_name'] !== ''
                                                ? $optRow['option_name'].': '.$val['value_label']
                                                : $val['value_label'];
                                        @endphp
                                        <label class="root-products__check">
                                            <input
                                                type="checkbox"
                                                name="{{ $instanceId }}-option[]"
                                                value="{{ $val['ids'] }}"
                                                id="{{ $instanceId }}-opt-{{ $optIdx }}-{{ $valIdx }}"
                                                aria-label="{{ e($optAria) }}"
                                            />
                                            <span class="root-products__check-text">{{ $val['value_label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            <fieldset class="root-products__filter-row" data-root-products-sort-row>
                <legend class="root-products__legend">{{ __('components.products.sort_label') }}</legend>
                <div class="root-products__radio-list">
                    <label class="root-products__radio">
                        <input
                            type="radio"
                            name="{{ $instanceId }}-sort"
                            value="name_asc"
                            checked
                        />
                        <span class="root-products__radio-text">{{ __('components.products.sort_name_asc') }}</span>
                    </label>
                    <label class="root-products__radio">
                        <input type="radio" name="{{ $instanceId }}-sort" value="name_desc" />
                        <span class="root-products__radio-text">{{ __('components.products.sort_name_desc') }}</span>
                    </label>
                    <label class="root-products__radio">
                        <input type="radio" name="{{ $instanceId }}-sort" value="price_asc" />
                        <span class="root-products__radio-text">{{ __('components.products.sort_price_asc') }}</span>
                    </label>
                    <label class="root-products__radio">
                        <input type="radio" name="{{ $instanceId }}-sort" value="price_desc" />
                        <span class="root-products__radio-text">{{ __('components.products.sort_price_desc') }}</span>
                    </label>
                </div>
            </fieldset>
        </div>

        <p
            class="root-products__results"
            role="status"
            aria-live="polite"
            data-root-products-results
        >
            {{ str_replace(['__VISIBLE__', '__TOTAL__'], [$totalCount, $totalCount], $resultsShownTemplate) }}
        </p>

        <p class="root-products__no-matches" data-root-products-no-matches hidden>
            {{ __('components.products.no_matches') }}
        </p>

        <div class="root-products__grid" data-root-products-grid>
            @foreach ($products as $product)
                @php
                    $categoryIds = $product->categories->pluck('id')->implode(',');
                    $optionValueIds = $product->variants
                        ->flatMap(fn ($v) => $v->optionValues->pluck('id'))
                        ->unique()
                        ->sort()
                        ->values()
                        ->implode(',');
                    $href = $productUrl === false
                        ? null
                        : ($productUrl === null
                            ? route('products.show', $product)
                            : rtrim((string) $productUrl, '/') . '/' . $product->slug);
                @endphp
                <div
                    class="root-products__item"
                    data-category-ids="{{ $categoryIds }}"
                    data-option-value-ids="{{ $optionValueIds }}"
                    data-sort-name="{{ e(\Illuminate\Support\Str::lower($product->name)) }}"
                    data-price="{{ $product->price }}"
                >
                    <x-product :product="$product" :href="$href" />
                </div>
            @endforeach
        </div>
    @endif
</div>

@once
    <script>
        (function () {
            function parseIds(s) {
                if (!s) {
                    return [];
                }
                return String(s)
                    .split(',')
                    .map(function (id) {
                        return id.trim();
                    })
                    .filter(Boolean);
            }

            function checkedValues(fieldset) {
                if (!fieldset) {
                    return [];
                }
                return Array.prototype.map.call(
                    fieldset.querySelectorAll('input[type="checkbox"]:checked'),
                    function (input) {
                        return input.value;
                    },
                );
            }

            function initRoot(root) {
                var grid = root.querySelector('[data-root-products-grid]');
                if (!grid) {
                    return;
                }

                var items = Array.prototype.slice.call(grid.querySelectorAll('.root-products__item'));
                var categoryFieldset = root.querySelector('[data-root-products-filter-categories]');
                var optionFieldset = root.querySelector('[data-root-products-filter-options]');
                var resultsEl = root.querySelector('[data-root-products-results]');
                var noMatchesEl = root.querySelector('[data-root-products-no-matches]');
                var template = root.getAttribute('data-results-shown-template') || '';
                var total = items.length;
                var sortRow = root.querySelector('[data-root-products-sort-row]');

                function update() {
                    var selectedCats = checkedValues(categoryFieldset);
                    var selectedOpts = checkedValues(optionFieldset);
                    var sortInput = sortRow
                        ? sortRow.querySelector('input[type="radio"]:checked')
                        : null;
                    var sort = sortInput ? sortInput.value : 'name_asc';

                    var visible = items.filter(function (el) {
                        var cats = parseIds(el.getAttribute('data-category-ids'));
                        var opts = parseIds(el.getAttribute('data-option-value-ids'));
                        var matchCat =
                            selectedCats.length === 0 ||
                            selectedCats.some(function (id) {
                                return cats.indexOf(id) !== -1;
                            });
                        var matchOpt =
                            selectedOpts.length === 0 ||
                            selectedOpts.some(function (raw) {
                                return parseIds(raw).some(function (id) {
                                    return opts.indexOf(id) !== -1;
                                });
                            });
                        return matchCat && matchOpt;
                    });

                    visible.sort(function (a, b) {
                        var an = a.getAttribute('data-sort-name') || '';
                        var bn = b.getAttribute('data-sort-name') || '';
                        var ap = parseFloat(a.getAttribute('data-price') || '0');
                        var bp = parseFloat(b.getAttribute('data-price') || '0');
                        switch (sort) {
                            case 'name_desc':
                                return bn.localeCompare(an);
                            case 'price_asc':
                                return ap - bp;
                            case 'price_desc':
                                return bp - ap;
                            case 'name_asc':
                            default:
                                return an.localeCompare(bn);
                        }
                    });

                    items.forEach(function (el) {
                        el.hidden = visible.indexOf(el) === -1;
                    });

                    var hiddenItems = items.filter(function (el) {
                        return visible.indexOf(el) === -1;
                    });
                    visible.forEach(function (el) {
                        grid.appendChild(el);
                    });
                    hiddenItems.forEach(function (el) {
                        grid.appendChild(el);
                    });

                    var visibleCount = visible.length;
                    if (resultsEl && template) {
                        resultsEl.textContent = template
                            .replace(/__VISIBLE__/g, String(visibleCount))
                            .replace(/__TOTAL__/g, String(total));
                    }
                    if (noMatchesEl) {
                        noMatchesEl.hidden = visibleCount > 0;
                    }
                }

                root.addEventListener('change', update);
                update();
            }

            function boot() {
                document.querySelectorAll('[data-root-products]').forEach(initRoot);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot);
            } else {
                boot();
            }
        })();
    </script>
    <style>
        .root-products {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            color: var(--color-text-dark);
            font-family: var(--font-family-two);
            font-size: 0.85rem;
        }
        .root-products__catalog-empty {
            margin: 0;
            text-align: center;
            color: var(--color-text-dark);
        }
        .root-products__filters {
            display: flex;
            flex-direction: column;
            width: 100%;
            padding-top: var(--padding-medium);
        }
        .root-products__filter-row {
            border: none;
            margin: 0;
            padding: 0;
            min-width: 0;
        }
        .root-products__legend {
            font-family: var(--font-family-one);
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            opacity: 0.85;
            padding: 0;
            margin: 0 0 0.5em;
        }
        .root-products__checkbox-list,
        .root-products__radio-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55em 1.25em;
            align-items: flex-start;
        }
        .root-products__option-groups {
            display: flex;
            flex-direction: column;
            gap: 0.65em;
            width: 100%;
        }
        .root-products__option-line {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 0.35em 0.85em;
            min-width: 0;
        }
        .root-products__option-line-label {
            font-family: var(--font-family-one);
            font-size: 0.75rem;
            letter-spacing: 0.03em;
            text-transform: none;
            opacity: 0.95;
            flex-shrink: 0;
            color: var(--color-text-dark);
        }
        .root-products__option-line-values {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55em 1.25em;
            align-items: flex-start;
            min-width: 0;
        }
        .root-products__check,
        .root-products__radio {
            display: inline-flex;
            align-items: flex-start;
            gap: 0.45em;
            cursor: pointer;
            font-family: var(--font-family-two);
            font-size: 0.88rem;
            line-height: 1.35;
            color: var(--color-text-dark);
        }
        .root-products__check input,
        .root-products__radio input {
            margin: 0.15em 0 0;
            flex-shrink: 0;
            accent-color: var(--color-one);
        }
        .root-products__results {
            margin: 0;
            font-size: 0.8rem;
            opacity: 0.9;
        }
        .root-products__no-matches {
            margin: 0;
            padding: var(--padding-small);
            text-align: center;
            border: 1px dashed var(--color-border);
            border-radius: var(--border-radius-medium);
            background: color-mix(in srgb, var(--color-background) 65%, white);
        }
        .root-products__grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: var(--gap-large);
            align-items: stretch;
            width: 100%;
        }
        @media (min-width: 36rem) {
            .root-products__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 56rem) {
            .root-products__grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (min-width: 72rem) {
            .root-products__grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        .root-products__item {
            width: 100%;
            min-width: 0;
            display: flex;
            justify-content: center;
        }
        .root-products__item[hidden] {
            display: none !important;
        }
        .root-products__item .root-product-card {
            width: 100%;
            max-width: calc(22rem * var(--product-scale, 0.66));
        }
    </style>
@endonce
