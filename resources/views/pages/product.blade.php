@extends('layouts.app')

@section('title', $product->seoTitle())
@section('description', $product->seoDescription())
@section('og_type', 'product')
@section('og_image', $product->firstVariantStorageImageUrl())

@push('head')
        <link rel="preload" as="image" href="{{ $product->firstVariantStorageImageUrl() ?: \App\Models\Product::genericProductImageUrl() }}" fetchpriority="high">
@endpush

@push('jsonld')
        <script type="application/ld+json">{!! json_encode($product->jsonLdProduct(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush

@section('content')

    <div class="root-views-product">

        <!-- Page 1 -->
        <x-miniviews.panel>
            <x-product-detail :Product="$product" />
        </x-miniviews.panel>

        <!-- Footer -->
        @include('layouts.footer', ['backgroundImage' => 'none'])

    </div>
    
@endsection 

@once
    <style lang="scss" scoped>
        .root-views-product {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            flex: 1;
            min-height: 0;
            overflow: auto;
            background-image: url("{{ asset('images/detailed.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            & > .page-1 {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
@endonce