@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="root-views-product">
        <article class="root-views-product__article">
            <h1>{{ $product->name }}</h1>
            @if ($product->short_description)
                <p class="root-views-product__lead">{{ $product->short_description }}</p>
            @endif
            @if ($product->description)
                <div class="root-views-product__body">{!! nl2br(e($product->description)) !!}</div>
            @endif
            <p class="root-views-product__price">
                {{ number_format((float) $product->price, 2) }}&nbsp;{{ __('components.product.currency') }}
            </p>
        </article>
    </div>
    <style>
        .root-views-product {
            box-sizing: border-box;
            width: 100%;
            max-width: 42rem;
            margin: 0 auto;
            padding: var(--padding-large);
            color: var(--color-text-dark);
        }
        .root-views-product__article h1 {
            color: var(--color-text-dark);
            margin-top: 0;
        }
        .root-views-product__lead {
            font-size: 1rem;
            opacity: 0.92;
        }
        .root-views-product__body {
            font-size: 0.9rem;
            line-height: 1.5;
            margin: var(--gap-medium) 0;
        }
        .root-views-product__price {
            font-family: var(--font-family-one);
            font-size: 1.2rem;
            color: var(--color-one);
        }
    </style>
@endsection
