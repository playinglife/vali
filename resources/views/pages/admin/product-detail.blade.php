@extends('layouts.admin')

@section('title', 'Product Detail')

@section('admin-menu-left')
    <a class="admin-back-link" href="{{ route('admin.dashboard') }}">Back</a>
    <span class="admin-header-title">Product Detail: {{ $product['name'] }}</span>
@endsection

@section('admin-menu-center')
    <div class="admin-menu-center-content">
        <button class="admin-menu-center-content-button" type="button" data-admin-grid-action="new">New</button>
        <button class="admin-menu-center-content-button">Delete</button>
    </div>
@endsection

@section('admin-menu-right')
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endsection

@section('content')
    <div class="admin-dashboard">
        <div class="admin-header">
            <div class="admin-header-left">
                <a class="admin-back-link" href="{{ route('admin.dashboard') }}">Back</a>
                <span class="admin-header-title">Product Detail: {{ $product['name'] }}</span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
        <div class="admin-meta">
            <span><strong>ID:</strong> {{ $product['id'] }}</span>
            <span><strong>SKU:</strong> {{ $product['sku'] }}</span>
            <span><strong>Slug:</strong> {{ $product['slug'] }}</span>
        </div>
        <div class="ag-theme-quartz admin-products-grid" id="admin-product-detail-grid" oncontextmenu="return false;"></div>
    </div>
@endsection

<script id="transferDataProduct" type="application/json">{!! json_encode($product) !!}</script>
<script id="transferDataVariants" type="application/json">{!! json_encode($variants) !!}</script>
<script id="transferDataOptions" type="application/json">{!! json_encode($options) !!}</script>
<script>
    window.transferData = window.transferData || {};
    window.transferData.product = JSON.parse(
        document.getElementById('transferDataProduct')?.textContent || '{}'
    );
    window.transferData.variants = JSON.parse(
        document.getElementById('transferDataVariants')?.textContent || '[]'
    );
    window.transferData.options = JSON.parse(
        document.getElementById('transferDataOptions')?.textContent || '[]'
    );
</script>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
@vite('resources/js/admin/product-detail.js')

@once
    <style>
        .admin-dashboard { padding: var(--padding-small); gap: var(--gap-small); display: flex; flex-direction: column; height: 100%; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; gap: var(--gap-small); }
        .admin-header-left { display: flex; align-items: center; gap: var(--gap-small); }
        .admin-back-link { color: var(--color-text-light); text-decoration: none; border: 1px solid var(--color-border-medium); border-radius: 4px; padding: 0.2rem 0.55rem; }
        .admin-products-grid { width: 100%; height: 100%; flex: 1; }
        .admin-header-title { font-size: var(--text-size-small); font-weight: 700; color: var(--color-text-light); }
        .admin-meta { display: flex; gap: 1rem; color: var(--color-text-light); font-size: 0.875rem; }
    </style>
@endonce
