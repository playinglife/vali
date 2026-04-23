@extends('layouts.admin')

@section('title', 'Orders')

@section('admin-menu-left')
    <span class="admin-header-title">Orders</span>
@endsection

@section('admin-menu-center')
    <div class="admin-menu-center-content">
        <button class="admin-menu-center-content-button" type="button" data-admin-grid-action="delete">Delete</button>
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
    <div class="admin-orders">
        <div class="ag-theme-quartz admin-orders-grid" data-admin-orders-grid id="admin-orders-grid" oncontextmenu="return false;"></div>
    </div>
@endsection



<!-- SCRIPTS -->
<script id="transferDataOrders" type="application/json">{!! json_encode($orders) !!}</script>
<script>
    window.transferData = window.transferData || {};
    window.transferData.orders = JSON.parse(
        document.getElementById('transferDataOrders')?.textContent || '[]'
    );
    window.transferData.orderDetailUrlTemplate = "{{ route('admin.orders.show', ['order' => '__ORDER_ID__']) }}";
</script>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
@vite('resources/js/admin/orders.js')



@once
    <style>
        .admin-orders {
            padding: var(--padding-small);
            height: 100%;
            box-sizing: border-box;
        }
        .admin-orders__placeholder {
            margin: 0;
            font-family: var(--font-family-one);
            font-size: var(--text-size-small);
            color: var(--color-text-light);
        }
        .admin-menu-right-form {
            display: flex;
            align-items: center;
            margin: 0;
        }
        .admin-header-title {
            font-size: var(--text-size-small);
            font-weight: 700;
            color: var(--color-text-light);
        }
    </style>
@endonce
