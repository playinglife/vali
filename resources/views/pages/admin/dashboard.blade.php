@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="admin-dashboard">
        <div class="admin-header">
            <span class="admin-header-title">Product Management</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>

        @php
            $gridRows = $products->map(function ($product) {
                $product->variants_count = $product->Variants->count();
                return $product;
            })->values();
        @endphp

        <div class="ag-theme-quartz admin-products-grid" data-admin-products-grid id="admin-products-grid" oncontextmenu="return false;"></div>

    </div>
@endsection



<!-- SCRIPTS -->
<script id="transferDataProducts" type="application/json">{!! $gridRows->toJson() !!}</script>
<script>
    window.transferData = window.transferData || {};
    window.transferData.products = JSON.parse(
        document.getElementById('transferDataProducts')?.textContent || '[]'
    );
</script>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
@vite('resources/js/admin/dashboard.js')



<!-- STYLES -->
@once
    <style>
        .admin-dashboard {
            padding: var(--padding-small);
            gap: var(--gap-small);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-products-grid {
            width: 100%;
            height: 100%;
            flex: 1;
        }

        .admin-header-title {
            font-size: var(--text-size-small);
            font-weight: 700;
            color: var(--color-text-light);
        }
    </style>
@endonce
