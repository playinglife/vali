@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('admin-menu-left')
    <span class="admin-header-title">Product Management</span>
@endsection

@section('admin-menu-center')
    <div class="admin-menu-center-content">
        <button class="admin-menu-center-content-button" type="button" data-admin-grid-action="new">New</button>
        <button class="admin-menu-center-content-button" type="button" data-admin-grid-action="delete">Delete</button>
        <button class="admin-menu-center-content-button" type="button" data-admin-grid-action="duplicate">Duplicate</button>
        <button class="admin-menu-center-content-button" type="button" data-admin-grid-action="refresh">Refresh</button>
    </div>
@endsection

@section('admin-menu-right')
    <form method="POST" action="{{ route('admin.logout') }}" class="admin-menu-right-form">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endsection

@section('content')
    <div class="admin-dashboard">
        <div class="ag-theme-quartz admin-products-grid" data-admin-products-grid id="admin-products-grid" oncontextmenu="return false;"></div>
    </div>
@endsection



<!-- SCRIPTS -->
<script id="transferDataProducts" type="application/json">{!! json_encode($products) !!}</script>
<script>
    window.transferData = window.transferData || {};
    window.transferData.products = JSON.parse(
        document.getElementById('transferDataProducts')?.textContent || '[]'
    );
    window.transferData.productDetailUrlTemplate = "{{ route('admin.products.detail', ['product' => '__PRODUCT_ID__']) }}";
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
        .admin-menu-center-content {
            display: flex;
            gap: var(--gap-small);
            justify-content: flex-start;
            align-items: center;
            flex-direction: row;
        }
        .admin-menu-center-content-button {
            padding: var(--padding-small);
            border-radius: var(--border-radius-small);
            border: 1px solid var(--color-border-medium);
            background-color: var(--color-background-light);
        }
        .admin-menu-right-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: var(--gap-small);
            margin: 0;
        }
    </style>
@endonce
