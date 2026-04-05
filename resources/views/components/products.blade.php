<!-- PROPS -->
@props([
    'products' => null,
    /** null = link with route('products.show'); false = no links; string = custom base URL before slug */
    'productUrl' => null,
    'instanceId' => 'products-grid',
])



<!-- PHP -->
@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Product>|null $products */
    $products = $products ?? \App\Models\Product::query()
        ->active()
        ->with(['categories', 'variants.optionValues.option', 'options.values', 'shortDescriptionTranslation', 'descriptionTranslation'])
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



<!-- TEMPLATE -->
<div
    class="root-products"
    id="{{ $instanceId }}"
    data-root-products
    data-results-shown-template="{{ e($resultsShownTemplate) }}"
>
    @if ($products->isEmpty())
        <p class="root-products__catalog-empty">{{ __('components.products.catalog_empty') }}</p>
    @else
        <p class="root-products__results transparent-blurred-background-light" role="status" aria-live="polite">
            <span data-root-products-results>{{ str_replace(['__VISIBLE__', '__TOTAL__'], [$totalCount, $totalCount], $resultsShownTemplate) }}</span>
            <button type="button" class="root-products__filter-button" data-root-products-filter-toggle aria-expanded="false" aria-controls="{{ $instanceId }}-filters">
                <x-icon name="heroicon-s-funnel" class="filter-icon" aria-hidden="true" />
            </button>
        </p>

        <div class="root-products__filters transparent-blurred-background-light" id="{{ $instanceId }}-filters" role="region" aria-label="{{ __('components.products.filters_region') }}">



            @if ($categories->isNotEmpty())
                <fieldset class="root-products__filter-row" data-root-products-filter-categories>
                    <legend class="root-products__legend">{{ __('components.products.filter_categories') }}</legend>
                    <div class="root-products__checkbox-list">
                        @foreach ($categories as $category)
                            <x-checkbox
                                :id="$instanceId . '-cat-' . $category->id"
                                :value="$category->id"
                                :label="$category->name"
                            />
                        @endforeach
                    </div>
                </fieldset>
            @endif



            @if ($optionFilterRows->isNotEmpty())
                @php
                    $maxOptionValueCount = (int) $optionFilterRows->max(fn (array $row) => $row['values']->count());
                @endphp
                <fieldset class="root-products__filter-row" data-root-products-filter-options>
                    <legend class="root-products__legend">{{ __('components.products.filter_options') }}</legend>
                    <div class="root-products__option-table-wrap">
                        <table class="root-products__option-table">
                            @foreach ($optionFilterRows as $optIdx => $optRow)
                                @php
                                    $optValues = $optRow['values'];
                                    $optValueCount = $optValues->count();
                                @endphp
                                <tr class="root-products__option-row">
                                    <th scope="row" class="root-products__option-line-label">
                                        @if ($optRow['option_name'] !== '')
                                            <span class="root-products__option-line-label-text">{{ $optRow['option_name'] }}</span>
                                        @endif
                                    </th>
                                    @foreach ($optValues as $valIdx => $val)
                                        @php
                                            $optAria = $optRow['option_name'] !== ''
                                                ? $optRow['option_name'].': '.$val['value_label']
                                                : $val['value_label'];
                                        @endphp
                                        <td class="root-products__option-cell">
                                            <x-checkbox
                                                :id="$instanceId . '-opt-' . $optIdx . '-' . $valIdx"
                                                :value="$val['ids']"
                                                :data-option-group="$optIdx"
                                                :label="$val['value_label']"
                                                :aria-label="$optAria"
                                            />
                                        </td>
                                    @endforeach
                                    @for ($pad = $optValueCount; $pad < $maxOptionValueCount; $pad++)
                                        <td class="root-products__option-cell root-products__option-cell--pad" aria-hidden="true"></td>
                                    @endfor
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </fieldset>
            @endif



            <fieldset class="root-products__filter-row" data-root-products-sort-row>
                <legend class="root-products__legend">{{ __('components.products.sort_label') }}</legend>
                <div class="root-products__radio-list">
                    <x-radio :id="$instanceId . '-sort-name-asc'" :name="$instanceId . '-sort'" value="name_asc" :label="__('components.products.sort_name_asc')" checked />
                    <x-radio :id="$instanceId . '-sort-name-desc'" :name="$instanceId . '-sort'" value="name_desc" :label="__('components.products.sort_name_desc')" />
                    <x-radio :id="$instanceId . '-sort-price-asc'" :name="$instanceId . '-sort'" value="price_asc" :label="__('components.products.sort_price_asc')" />
                    <x-radio :id="$instanceId . '-sort-price-desc'" :name="$instanceId . '-sort'" value="price_desc" :label="__('components.products.sort_price_desc')" />
                </div>
            </fieldset>
        </div>

        <p class="root-products__no-matches transparent-blurred-background-light" data-root-products-no-matches hidden>
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



<!-- SCRIPT -->
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

            /** Checked option checkboxes grouped by data-option-group (one row = one option type). */
            function checkedOptionGroupsByRow(fieldset) {
                var groups = {};
                if (!fieldset) {
                    return groups;
                }
                fieldset.querySelectorAll('input[type="checkbox"]:checked').forEach(function (input) {
                    var g = input.getAttribute('data-option-group');
                    if (g === null || g === '') {
                        g = '0';
                    }
                    if (!groups[g]) {
                        groups[g] = [];
                    }
                    groups[g].push(input.value);
                });
                return groups;
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
                var filterArea = root.querySelector('.root-products__filters');
                var filterToggleBtn = root.querySelector('[data-root-products-filter-toggle]');
                var showFilter = false;

                if (filterArea) {
                    filterArea.style.display = 'none';
                }
                if (filterToggleBtn && filterArea) {
                    filterToggleBtn.addEventListener('click', function () {
                        showFilter = !showFilter;
                        filterArea.style.display = showFilter ? '' : 'none';
                        filterToggleBtn.setAttribute('aria-expanded', showFilter ? 'true' : 'false');
                    });
                }

                function update() {
                    var selectedCats = checkedValues(categoryFieldset);
                    var optionGroups = checkedOptionGroupsByRow(optionFieldset);
                    var sortInput = sortRow
                        ? sortRow.querySelector('input[type="radio"]:checked')
                        : null;
                    var sort = sortInput ? sortInput.value : 'name_asc';

                    var visible = items.filter(function (el) {
                        var cats = parseIds(el.getAttribute('data-category-ids'));
                        var opts = parseIds(el.getAttribute('data-option-value-ids'));
                        /* Categories: OR within selected categories. */
                        var matchCat =
                            selectedCats.length === 0 ||
                            selectedCats.some(function (id) {
                                return cats.indexOf(id) !== -1;
                            });
                        /*
                         * Options: OR within each option row (e.g. S or M), AND between rows (e.g. (S|M) and (white|navy)).
                         * Rows with no selection are ignored.
                         */
                        var groupKeys = Object.keys(optionGroups);
                        var matchOpt =
                            groupKeys.length === 0 ||
                            groupKeys.every(function (g) {
                                var raws = optionGroups[g];
                                return raws.some(function (raw) {
                                    return parseIds(raw).some(function (id) {
                                        return opts.indexOf(id) !== -1;
                                    });
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
@endonce



<!-- STYLE -->
 @once
    <style>
        .root-products {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            color: var(--color-text-dark);
            font-family: var(--font-family-two);
            font-size: var(--text-size-small);
            font-weight: var(--font-weight-bold);
        }
        .root-products__catalog-empty {
            margin: 0;
            text-align: center;
            color: var(--color-text-dark);
        }
        .root-products__filters {
            box-sizing: border-box;
            width: 100%;
            padding: var(--padding-medium);
            display: flex;
            flex-direction: column;
            gap: var(--gap-large);
            align-items: stretch;
            min-width: 0;
        }
        .root-products__filter-row {
            border: none;
            margin: 0;
            padding: 0;
            min-width: 0;
        }
        .root-products__legend {
        }
        .root-products__checkbox-list,
        .root-products__radio-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55em 1.25em;
            align-items: flex-start;
        }
        .root-products__option-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
        }
        .root-products__option-table {
            width: max-content;
            max-width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5em;
            table-layout: auto;
        }
        .root-products__option-row {
            vertical-align: middle;
        }
        .root-products__option-table th.root-products__option-line-label {
            text-align: start;
            font-weight: inherit;
            vertical-align: middle;
            padding: 0 0.75em 0 0;
            white-space: nowrap;
            font-family: var(--font-family-one);
            font-size: var(--text-size-small);
            letter-spacing: 0.03em;
            text-transform: none;
            opacity: 0.95;
            color: var(--color-text-dark);
        }
        .root-products__option-line-label-text {
            text-transform: uppercase;
        }
        .root-products__option-line-label-text::after {
            content: ':';
        }
        .root-products__option-table td.root-products__option-cell {
            vertical-align: middle;
            padding: 0 0.65em;
            white-space: nowrap;
        }
        .root-products__option-cell--pad {
            padding: 0 !important;
            border: none !important;
        }
        .root-products__check--option-table {
            display: inline-flex;
            align-items: flex-start;
            gap: 0.45em;
            margin: 0;
            vertical-align: middle;
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
        .root-products__check-text,
        .root-products__radio-text {
            font-size: var(--text-size-small);
        }

        .root-products__results {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: var(--padding-medium);
            font-size: var(--text-size-small);
            padding: var(--padding-medium);
            margin-top: calc(var(--padding-large) * 3);
            color: var(--color-text-dark);
        }
        .root-products__results [data-root-products-filter-toggle] {
        }
        .filter-icon {
        }
        .root-products__no-matches {
            margin: 0;
            padding: var(--padding-small);
            text-align: center;
        }
        /* Native CSS Grid: gap does not break column counts (unlike flex + flex-basis % + gap). */
        .root-products__grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: var(--gap-large) var(--gap-medium);
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            margin: 0;
            align-items: stretch;
        }
        @media (max-width: 80em) {
            .root-products__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 48em) {
            .root-products__grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
        .root-products__item {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            min-height: 0;
        }
        .root-products__item[hidden] {
            display: none !important;
        }
        .root-products__filter-button {
            aspect-ratio: 1 / 1;
        }
    </style>
@endonce
