@extends('layouts.admin')

@section('title', 'Order detail')

@section('admin-menu-left')
    <a class="admin-back-link" href="{{ route('admin.orders') }}">Back</a>
    <div class="admin-header-left">
        <span class="admin-header-title">Order items</span>
        <span class="admin-header-title">#{{ $order['order_number'] ?? $order['id'] }} — {{ $order['email'] ?? '' }}</span>
    </div>
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
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endsection

@section('content')
    <div class="admin-order-detail">
        <div class="ag-theme-quartz admin-order-detail-grid" id="admin-order-detail-grid" oncontextmenu="return false;"></div>
    </div>
@endsection

<script id="transferDataOrder" type="application/json">{!! json_encode($order) !!}</script>
<script>
    window.transferData = window.transferData || {};
    window.transferData.order = JSON.parse(
        document.getElementById('transferDataOrder')?.textContent || '{}'
    );
</script>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
@vite('resources/js/admin/order-detail.js')

@once
    <style>
        .admin-order-detail {
            padding: var(--padding-small);
            gap: var(--gap-small);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .admin-order-detail-grid {
            width: 100%;
            height: 100%;
            flex: 1;
        }
        .admin-header-left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: var(--gap-small);
        }
        .admin-back-link {
            color: var(--color-text-light);
            text-decoration: none;
            border: 1px solid var(--color-border-medium);
            border-radius: 4px;
            padding: 0.2rem 0.55rem;
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
    </style>
@endonce
