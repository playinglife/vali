@props(['variant', 'price_brackets', 'show' => false])

@once
    @push('styles')
        @vite(['resources/scss/price-brackets.scss'])
    @endpush
@endonce
<div class="root-product-detail__brackets-wrap" data-variant-price-brackets-id="{{ $variant->id }}" @if ($price_brackets->isEmpty() || !$show) hidden @endif>
    <x-miniviews.group>
        <h4 class="dark label">{{ __('components.product.price_brackets') }}</h4>
        <table class="root-product-detail__brackets-table">
            <colgroup>
                <col />
                <col />
                <col />
                <col class="root-product-detail__brackets-col-price" />
            </colgroup>
            <thead>
                <tr>
                    <th class="root-product-detail__brackets-th-qty text-tiny" colspan="3" scope="colgroup">
                        {{ __('components.product.quantity_range') }}
                    </th>
                    <th class="root-product-detail__brackets-th-price text-tiny" scope="col">
                        {{ __('components.product.bracket_price') }}
                    </th>
                </tr>
            </thead>
            <tbody class="root-product-detail__brackets-body">
                @foreach ($price_brackets as $bracket)
                    <tr
                        class="root-product-detail__brackets-row"
                        data-qty-min="{{ $bracket->start_quantity }}"
                        data-qty-max="{{ $bracket->end_quantity !== null ? $bracket->end_quantity : '' }}"
                    >
                        <td class="root-product-detail__brackets-q-start">{{ $bracket->start_quantity }}</td>
                        <td class="root-product-detail__brackets-q-sep" aria-hidden="true">
                            @if ($bracket->end_quantity !== null)
                                –
                            @endif
                        </td>
                        <td class="root-product-detail__brackets-q-end">
                            @if ($bracket->end_quantity !== null)
                                {{ $bracket->end_quantity }}
                            @else
                                {{ __('components.product.qty_plus_open') }}
                            @endif
                        </td>
                        <td class="root-product-detail__brackets-price">
                            {{ number_format((float) $bracket->price, 2) }}&nbsp;{{ __('components.product.currency') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-miniviews.group>
</div>